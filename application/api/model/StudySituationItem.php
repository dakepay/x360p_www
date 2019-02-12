<?php
/**
 * Author: luo
 * Time: 2018/5/25 9:23
 */

namespace app\api\model;


class StudySituationItem extends Base
{
    protected $type = [
        'answer' => 'json',
    ];

    public function setNextIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function addStudySituationItem($data)
    {
        if(empty($data['ss_id'])) return $this->user_error('ss_id error');

        $rs = $this->data($data)->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return false;

        return true;
    }

}