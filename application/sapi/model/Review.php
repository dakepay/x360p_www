<?php
/**
 * Author: luo
 * Time: 2018-01-04 15:03
**/

namespace app\sapi\model;

class Review extends Base
{

    public function reviewFile()
    {
        return $this->hasMany('ReviewFile', 'rvw_id', 'rvw_id');
    }

    public function reviewTplSetting()
    {
        return $this->hasOne('ReviewTplSetting', 'rts_id', 'rts_id');
    }

}