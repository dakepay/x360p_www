<?php

namespace app\api\Model;

use app\common\exception\FailResult;
use think\Exception;

class LessonPriceDefine extends Base{

    public function setDeptIdsAttr($value, $data)
    {
        if (is_array($value)) {
            return join(',', $value);
        }
        return $value;
    }

    public function setBidsAttr($value)
    {
        if (is_array($value)) {
            return join(',', $value);
        }
        return $value;
    }


    public function getBidsAttr($value, $data)
    {
        return split_int_array($value);
    }

    public function getDeptIdsAttr($value, $data)
    {
        return split_int_array($value);
    }

}