<?php
/**
 * Author: luo
 * Time: 2018-01-04 15:34
**/

namespace app\sapi\model;

class ReviewFile extends Base
{
    public function file()
    {
        return $this->hasOne('File', 'file_id', 'file_id');
    }

}