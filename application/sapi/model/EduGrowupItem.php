<?php
/**
 * Author: luo
 * Time: 2018/6/22 18:00
 */

namespace app\sapi\model;


class EduGrowupItem extends Base
{

    public function eduGrowupPic()
    {
        return $this->hasMany('EduGrowupPic', 'egi_id', 'egi_id');
    }

}