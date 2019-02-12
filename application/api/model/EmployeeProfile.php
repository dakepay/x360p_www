<?php
/**
 * luo
 */
namespace app\api\model;

class EmployeeProfile extends Base
{
    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];
}