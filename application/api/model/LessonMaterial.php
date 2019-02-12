<?php
/**
 * Author: luo
 * Time: 2017-12-07 09:56
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class LessonMaterial extends Base
{
    protected $skip_og_id_condition = true;

    //添加一个课程物品
    public function addOneLessonMaterial($data)
    {
        $rs = $this->validateData($data, 'LessonMaterial');
        if($rs !== true) return $this->user_error($this->getErrorMsg());

        $is_exist = $this->where('lid', $data['lid'])->where('mt_id', $data['mt_id'])->find();
        if(!empty($is_exist)) return $this->user_error("物品已经关联过");

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return $this->user_error("课程关联物品失败");

        return true;
    }

    //添加多个课程物品
    public function addBatchLessonMaterial($data)
    {
        if(empty($data) || !is_array($data)) return $this->user_error('参数错误');

        $this->startTrans();
        try {
            foreach ($data as $row) {
                $rs = $this->addOneLessonMaterial($row);
                if ($rs === false) throw new FailResult($this->getErrorMsg());
            }
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //更新课程的关联物品
    public function updateMaterialOfLesson($data)
    {
        if(empty($data) || !is_array($data)) return $this->user_error('参数错误');

        $this->startTrans();
        try {
            $new_mt_ids = [];

            foreach ($data as $row) {
                $rs = $this->validateData($row, 'LessonMaterial');
                if ($rs !== true) throw new FailResult($this->getErrorMsg());

                $lid = $row['lid'];
                $new_mt_ids[$lid][] = $row['mt_id'];

                //--1-- 如果以前关联过，则更新， 否则添加
                $old_record = $this->where('lid', $row['lid'])->where('mt_id', $row['mt_id'])->find();
                if (!empty($old_record)) {
                    $update_data = ['default_num' => $row['default_num']];
                    $rs = $this->where('lm_id', $old_record['lm_id'])->update($update_data);
                    if($rs === false) throw new FailResult('更新失败');
                } else {
                    $rs = $this->addOneLessonMaterial($row);
                    if($rs === false) throw new FailResult($this->getErrorMsg());
                }
            }

            //--2-- 把以前关联的删除
            foreach ($new_mt_ids as $lid => $new_row) {
                $rs = $this->where('lid', $lid)->where('mt_id', 'not in', $new_row)->delete();
                if ($rs === false) throw new FailResult('删除以前关联的物品失败');
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        return true;
    }


}