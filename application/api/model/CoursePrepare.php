<?php
/**
 * Author: luo
 * Time: 2018/4/9 9:35
 */

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class CoursePrepare extends Base
{

    protected function setSidsAttr($value)
    {
        return !empty($value) && is_array($value) ? implode(',', $value) : $value;
    }

    protected function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    protected function setIntStartHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    protected function setIntEndHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function getSidsAttr($value)
    {
        return is_string($value) ? explode(',', $value) : $value;
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,sex,student_name,photo_url');
    }

    public function coursePrepareAttachment()
    {
        return $this->hasMany('CoursePrepareAttachment', 'cp_id', 'cp_id');
    }

    public function courseArrange()
    {
        return $this->hasOne('CourseArrange', 'ca_id', 'ca_id')
            ->field('ca_id,name,cid,teach_eid,second_eid,lid,sj_id,cr_id,int_day,int_start_hour,int_end_hour,is_attendance,is_trial,is_makeup');
    }

    //添加备课
    public function addPreparation($data)
    {
        try {
            $this->startTrans();

            if(empty($data['content'])) throw new FailResult('备课内容不能为空');
            if(!empty($data['ca_id'])) {
                $old_preparation = CoursePrepare::get(['ca_id' => $data['ca_id']]);
                if(!empty($old_preparation)) return $this->user_error('排课已经备过课');

                $course = CourseArrange::get($data['ca_id']);
                $data = array_merge($course->toArray(), $data);
            }

            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false) throw new FailResult($this->getErrorMsg());

            $cp_id = $this->cp_id;

            if (!empty($data['course_prepare_attachment']) && is_array($data['course_prepare_attachment'])) {
                $attachment_data = $data['course_prepare_attachment'];
                $m_cpa = new CoursePrepareAttachment();
                foreach ($attachment_data as $row_data) {
                    if (isset($row_data['file_id'])) {
                        $file_info = get_file_info($row_data['file_id']);
                        $row_data  = array_merge($row_data, $file_info);
                    }
                    $row_data['cp_id'] = $cp_id;
                    $rs = $m_cpa->data([])->allowField(true)->isUpdate(false)->save($row_data);
                    if ($rs === false) throw new FailResult('添加备课附件失败');
                }
            }

            if(!empty($data['ca_id'])) {
                $course = !empty($course) && ($course instanceof CourseArrange) ? $course : CourseArrange::get($data['ca_id']);
                $course->is_prepare = 1;
                $course->prepare_file_nums = empty($data['course_prepare_attachment']) ? 0 : count($data['course_prepare_attachment']);
                $rs = $course->allowField('is_prepare,prepare_file_nums')->isUpdate(true)->save();
                if($rs === false) return false;
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $cp_id;
    }

    public function getStudents()
    {
        if(empty($this->getData())) return $this->user_error('备课数据为空');

        $sids = [];
        if($this->cid > 0) {
            $class_sids = (new ClassStudent())->where('cid', $this->cid)
                ->where('status', ClassStudent::STATUS_NORMAL)->column('sid');
            $sids = array_merge($sids, $class_sids);
        }

        if(is_string($this->sids)) {
            $sids = array_merge($sids, explode(',', $this->sids));
        }

        $this->sid > 0 && $sids[] = $this->sid;

        return $sids;
    }

    ///删除备课
    public function delPreparation($cp_id)
    {
        /** @var CoursePrepare $preparation */
        $preparation = $this->find($cp_id);
        if(empty($preparation)) return $this->user_error('备课不存在');

        try {
            $this->startTrans();

            if($preparation->ca_id > 0) {
                $course = CourseArrange::get($preparation->ca_id);
                if(!empty($course)) {
                    $course->is_prepare = 0;
                    $course->prepare_file_nums = 0;
                    $rs = $course->allowField('is_prepare,prepare_file_nums')->isUpdate(true)->save();
                    if($rs === false) throw new FailResult('更新排课失败');
                }
            }

            $preparation->coursePrepareAttachment()->delete();
            $preparation->delete();

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //编辑
    public function edit($data, $attachment_data = [])
    {

        if(empty($this->getData())) return $this->user_error('备课数据为空');

        try {
            $this->startTrans();
            if(!empty($data['ca_id'])) {
                $old_preparation = CoursePrepare::get(['ca_id' => $data['ca_id']]);
                if(!empty($old_preparation) && $old_preparation['cp_id'] != $this->cp_id) return $this->user_error('排课已经备过课');

                if($this->ca_id > 0 && $this->ca_id != $data['ca_id']) {
                    (new CourseArrange())->where('ca_id', $this->ca_id)->update(['is_prepare' => 0, 'prepare_file_nums' => 0]);
                }

                $course = CourseArrange::get($data['ca_id']);
                $data = array_merge($course->toArray(), $data);
            }
            $rs = $this->allowField(true)->isUpdate(true)->save($data);
            if ($rs === false) throw new FailResult('更新失败');

            //if (!empty($attachment_data) && is_array($attachment_data)) {
                $old_file_ids = $this->coursePrepareAttachment()->column('file_id');
                $new_file_ids = array_column($attachment_data, 'file_id');
                $del_file_ids = array_diff($old_file_ids, $new_file_ids);
                $add_file_ids = array_diff($new_file_ids, $old_file_ids);

                $rs = $this->coursePrepareAttachment()->where('file_id', 'in', $del_file_ids)->delete();
                if($rs === false) throw new FailResult('删除原附件失败');

                $m_file = new File();
                $m_cpa = new CoursePrepareAttachment();
                foreach ($add_file_ids as $per_file_id) {
                    $file = $m_file->find($per_file_id);
                    if(empty($file)) throw new FailResult('文件不存在');

                    $row_data['cp_id'] = $this->cp_id;
                    $row_data = array_merge($row_data, $file->toArray());
                    $rs = $m_cpa->data([])->allowField(true)->isUpdate(false)->save($row_data);
                    if ($rs === false) throw new FailResult('添加备课附件失败');

                }
            //}

            if(!empty($data['ca_id'])) {
                $course = !empty($course) && ($course instanceof CourseArrange) ? $course : CourseArrange::get($data['ca_id']);
                $course->is_prepare = 1;
                $course->prepare_file_nums = count($attachment_data);
                $rs = $course->allowField('is_prepare,prepare_file_nums')->isUpdate(true)->save();
                if($rs === false) throw new FailResult('更新排课失败');
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }
}