<?php
/**
 * Author: luo
 * Time: 2018/1/11 15:58
 */

namespace app\api\model;


class SubjectGrade extends Base
{

    //添加科目级别
    public function addOneGrade($data)
    {
        if(!isset($data['grade']) || !isset($data['sj_id'])) return $this->user_error('param error');
        $grade = $this->where('sj_id', $data['sj_id'])->where('grade', $data['grade'])->find();
        if(!empty($grade)) return $this->user_error('科目组别已存在');

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return $this->user_error('添加科目级别失败');

        return true;
    }
}