<?php
/**
 * Author: luo
 * Time: 2018/8/15 10:19
 */

namespace app\api\model;


class DebitCard extends Base
{
    protected $append = ['create_employee_name'];

    protected $hidden = [
        'create_time',
        'update_time',
        'is_delete',
        'delete_time'
    ];

    protected $type = [
        'discount_define' => 'json'
    ];

    protected function setDptIdsAttr($value)
    {
        return !empty($value) && is_array($value) ? implode(',', $value) : $value;
    }

    protected function setBidsAttr($value)
    {
        return !empty($value) && is_array($value) ? implode(',', $value) : $value;
    }

    protected function getDptIdsAttr($value)
    {
        $value = is_string($value) && !empty($value) ? explode(',', $value) : $value;
        return empty($value) ? [] : $value;
    }

    protected function getBidsAttr($value)
    {
        $value = is_string($value) && !empty($value) ? explode(',', $value) : $value;
        return empty($value) ? [] : $value;
    }

    public function addCard($post)
    {
        if(empty($post['card_name']) || empty($post['amount'])) {
            return $this->user_error('卡名和额度错误');
        }

        $rs = $this->allowField(true)->save($post);
        return $rs;
    }

    public function updateCard($post)
    {
        $rs = $this->allowField(true)->save($post);
        return $rs;
    }


}