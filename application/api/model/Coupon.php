<?php
/** 
 * Author: luo
 * Time: 2017-10-12 15:16
**/

namespace app\api\model;

class Coupon extends Base
{
    protected $type = [
        'status' => 'boolean',
        'start_time' => 'timestamp',
        'end_time' => 'timestamp',
    ];

    public function setBranchScopeAttr($value)
    {
        if (is_array($value)) {
            $value = join(',', $value);
        }
        return $value;
    }

    public function getBranchScopeAttr($value)
    {
        return explode(',', $value);
    }

    public function setLessonScopeAttr($value)
    {
        if (is_array($value)) {
            $value = join(',', $value);
        }
        return $value;
    }

    public function getLessonScopeAttr($value)
    {
        return explode(',', $value);
    }

    public function couponCodes()
    {
        return $this->hasMany('CouponCode', 'coupon_id', 'coupon_id');
    }

    protected function make_coupon_code() {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);  // "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;

    }

    public function addRule($input)
    {
        $result = $this->allowField(true)->save($input);
        if ($result === false) {
            return false;
        }
        $data = [];
        for ($i = 1; $i <= $input['number']; $i++) {
            $temp['coupon_code'] = $this->make_coupon_code();
            array_push($data, $temp);
        }
        $this->couponCodes()->saveAll($data);
        return true;
    }

    public function changeStatus($input)
    {
        $this->allowField(['status'])->save($input);
        return true;
    }
}