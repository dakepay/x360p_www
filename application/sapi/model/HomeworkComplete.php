<?php
/**
 * Author: luo
 * Time: 2018/3/22 15:03
 */

namespace app\sapi\model;

use app\common\exception\FailResult;
use think\Exception;

class HomeworkComplete extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function getRejectedTimeAttr($value)
    {
        if ($value > 0){
            return date('Y-m-d H:i:s',$value);
        }else{
            return 0;
        }
    }

    public function homeworkAttachment()
    {
        return $this->hasMany('HomeworkAttachment', 'hc_id', 'hc_id');
    }

    public function homeworkTask()
    {
        return $this->hasOne('HomeworkTask', 'ht_id', 'ht_id')
            ->field('ht_id,eid,lesson_type,deadline');
    }

    public function homeworkReply()
    {
        return $this->hasOne('HomeworkReply', 'hc_id', 'hc_id');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes','cid','cid');
    }

    //作业提交
    public function addComplete($data, $attachment_data = [])
    {
        if(empty($data['ht_id']) || empty($data['sid'])) return $this->user_error('作业id错误或者学生id');

        $homework = HomeworkTask::get($data['ht_id']);
        if(empty($homework)) return $this->user_error('作业任务不存在');

        $w = [
            'ht_id' => $data['ht_id'],
            'sid' => $data['sid']
        ];
        $rs = $this->where($w)->find();
        if (!empty($rs)){
            return $this->user_error('作业已提交请勿重复提交');
        }

        try {
            $this->startTrans();
            $data = array_merge($homework->toArray(),$data);
            $rs = $this->allowField(true)->data($data)->isUpdate(false)->save($data);
            if ($rs === false) throw new FailResult('提交作业失败');

            $hc_id = $this->hc_id;

            if (!empty($attachment_data)) {
                $m_ha = new HomeworkAttachment();
                foreach ($attachment_data as $row) {
                    if (!isset($row['file_id'])) throw new FailResult('file_id error');
                    $file = File::get($row['file_id']);
                    $file = $file->toArray();
                    $file['hc_id'] = $hc_id;
                    $file['att_type'] = $m_ha::ATT_TYPE_COMPLETE;
                    $rs = $m_ha->data([])->allowField(true)->isUpdate(false)->save($file);
                    if ($rs === false) throw new FailResult('附件保存失败');
                }
            }
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //作业删除
    public function delHomework($hc_id,$sid){
        if(empty($hc_id)) return $this->user_error('hc_id is null');

        $m_hc = self::get($hc_id);
        if(empty($m_hc)) return $this->user_error('作业不存在或已删除');

        $mHomeworkReplyr = new HomeworkReply();
        $w['hc_id'] = $hc_id;
        $m_hr = $mHomeworkReplyr->where($w)->find();
        if(!empty($m_hr)) return $this->user_error('作业老师批阅不能删除');

        if($m_hc['sid'] != $sid) return $this->user_error('不能删除别人作业');

        try {
            $rs = $m_hc->delete();
            if (!$rs) {
                $this->rollback();
                return $this->user_error('作业删除失败' . $this->getError());
            }
            $mHomeworkAttachment = new HomeworkAttachment();
            $w = [
                'att_type' => 1,
                'hc_id' => $hc_id
            ];
            $ha_list = $mHomeworkAttachment->where($w)->select();
            if ($ha_list){
                foreach ($ha_list as $k => $v){
                    $rs = $v->delete();
                }
                if (!$rs) {
                    $this->rollback();
                    return $this->user_error('作业附件删除失败' . $this->getError());
                }
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }

}