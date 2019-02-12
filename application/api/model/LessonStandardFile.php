<?php
/**
 * Author: luo
 * Time: 2018/5/11 18:16
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class LessonStandardFile extends Base
{

    public function lessonStandardFileItem()
    {
        return $this->hasMany('LessonStandardFileItem', 'lsf_id', 'lsf_id');
    }

    public function addLessonStandardFile($post)
    {
        if(empty($post['lid']) && empty($post['tb_id'])) return $this->user_error('教材或课程必须选择1个');


        if($post['tb_id'] > 0 && $post['chapter_index'] == 0 && isset($post['sectionSort'])){
            $post['chapter_index'] = $post['sectionSort'];
        }
        $this->startTrans();
        try {

            $rs = $this->data([])->isUpdate(false)->allowField(true)->save($post);
            if($rs === false) return false;

            $lsf_id = $this->getAttr('lsf_id');
            $items_data = isset($post['lesson_standard_file_item']) && is_array($post['lesson_standard_file_item'])
                ? $post['lesson_standard_file_item'] : [];

            if (!empty($items_data)) {
                $m_file = new File();
                $m_lsfi = new LessonStandardFileItem();
                foreach($items_data as $per_file) {
                    if(!isset($per_file['file_id'])) continue;
                    $file = $m_file->find($per_file['file_id']);
                    $file = $file ? $file->toArray() : [];
                    if(empty($file)) continue;
                    $file['lsf_id'] = $lsf_id;
                    $rs = $m_lsfi->data([])->isUpdate(false)->allowField(true)->save($file);
                    if ($rs === false) throw new FailResult($m_lsfi->getErrorMsg());
                }
            }


        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    public function edit($put_data, $file_items)
    {
        $data = $this->getData();
        if(empty($data)) return $this->user_error('课标数据为空');
        if($put_data['tb_id'] > 0 && isset($put_data['sectionSort'])){
            $put_data['chapter_index'] = $put_data['sectionSort'];
        }
        $this->startTrans();
        try {
            $rs = $this->allowField(true)->isUpdate(true)->save($put_data);
            if ($rs === false) throw new FailResult('更新失败');

            $old_file_ids = $this->lessonStandardFileItem()->column('file_id');
            $new_file_ids = array_column($file_items, 'file_id');
            $del_file_ids = array_diff($old_file_ids, $new_file_ids);
            $add_file_ids = array_diff($new_file_ids, $old_file_ids);

            $rs = $this->lessonStandardFileItem()->where('file_id', 'in', $del_file_ids)->delete();
            if($rs === false) throw new FailResult('删除原附件失败');

            $m_file = new File();
            $m_lsfi = new LessonStandardFileItem();
            foreach ($add_file_ids as $per_file_id) {
                $file = $m_file->find($per_file_id);
                if(empty($file)) throw new FailResult('文件不存在');

                $row_data = [];
                $row_data['lsf_id'] = $this->lsf_id;
                $row_data = array_merge($row_data, $file->toArray());
                $rs = $m_lsfi->data([])->allowField(true)->isUpdate(false)->save($row_data);
                if ($rs === false) throw new FailResult($m_lsfi->getErrorMsg());
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    public function delOne()
    {
        $data = $this->getData();
        if(empty($data)) return $this->user_error('课标数据错误');
        $rs = $this->lessonStandardFileItem()->delete();
        if($rs === false) return false;

        $rs = $this->delete();
        if($rs === false) return false;

        return true;
    }

    public function delBatch($lsf_ids)
    {
        if(!is_array($lsf_ids)) return $this->user_error('lsf_ids参数错误');
        $this->startTrans();
        try {
            foreach($lsf_ids as $tmp_lsf_id) {
                $lesson_standard_file = $this->find($tmp_lsf_id);
                if(empty($lesson_standard_file)) continue;
                $rs = $lesson_standard_file->delOne();
                if($rs === false) throw new FailResult($lesson_standard_file->getError());
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

}