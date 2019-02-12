<?php
/**
 * Author: luo
 * Time: 2017-12-29 10:57
**/

namespace app\api\model;


class TallyFile extends Base
{

    public function file()
    {
        return $this->hasOne('File', 'file_id', 'file_id');
    }


}