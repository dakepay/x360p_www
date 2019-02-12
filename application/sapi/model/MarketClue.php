<?php
/**
 * Author: luo
 * Time: 2018/6/27 9:38
 */

namespace app\sapi\model;


class MarketClue extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];
    protected $auto = ['birth_year', 'birth_month', 'birth_day'];

    protected function setBirthTimeAttr($value)
    {
        if($value == '1970-01-01'){
            return 0;
        }
        return !is_numeric($value) || strlen($value) <= 8 ? strtotime($value) : $value;
    }

    protected function setBirthYearAttr($value, $data) {
        if(isset($data['birth_time']) && $data['birth_time'] > 0) {
            $data['birth_time'] = is_int($data['birth_time']) ? $data['birth_time'] : strtotime($data['birth_time']);
            $value = (int)date('Y', $data['birth_time']);
        }
        return $value ? $value : 0;
    }

    protected function setBirthMonthAttr($value, $data) {
        if(isset($data['birth_time']) && $data['birth_time']>0) {
            $data['birth_time'] = is_int($data['birth_time']) ? $data['birth_time'] : strtotime($data['birth_time']);
            $value = (int)date('m', $data['birth_time']);
        }
        return $value ? $value : 0;
    }

    protected function setBirthDayAttr($value, $data) {
        if(isset($data['birth_time']) && $data['birth_time']>0) {
            $data['birth_time'] = is_int($data['birth_time']) ? $data['birth_time'] : strtotime($data['birth_time']);
            $value = (int)date('d', $data['birth_time']);
        }
        return $value ? $value : 0;
    }

    protected function getBirthTimeAttr($value) {
        return $value > 0 ? date('Y-m-d', $value) : $value;
    }

    public function addClue($data)
    {
        if(empty($data['name']) || empty($data['tel'])) return $this->user_error('姓名或者电话没填');
        $data['get_time'] = time();

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return false;

        return true;
    }

}