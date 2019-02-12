<?php
/** 
 * Author: luo
 * Time: 2017-12-19 14:53
**/

namespace app\sapi\model;

class EmployeeProfile extends Base
{
    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];
}