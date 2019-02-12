<?php
/** 
 * Author: luo
 * Time: 2017-10-11 11:09
**/

namespace app\sapi\model;

class Dictionary extends Base
{

    protected $type = [
    ];
    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

}