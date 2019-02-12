<?php
namespace app\ftapi\model;

use Overtrue\Pinyin\Pinyin;
use think\Exception;
use think\Db;

class Employee extends Base
{

    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function getBidsAttr($value, $data)
    {
        return split_int_array($value);
    }


    public function getBirthTimeAttr($value) {
        return $value ? date('Y-m-d', $value) : $value;
    }

    public function getRidsAttr($value, $data)
    {
        return split_int_array($value);
    }

    public function getLidsAttr($value, $data)
    {
        return split_int_array($value);
    }

    public function getSjIdsAttr($value, $data)
    {
        return split_int_array($value);
    }

    public function branches()
    {
        return $this->belongsToMany('Branch', 'branch_employee', 'bid', 'eid');
    }

    public function roles()
    {
        return $this->belongsToMany('Role', 'employee_role', 'rid', 'eid');
    }

    public function subjects()
    {
        return $this->belongsToMany('Subject', 'employee_subject', 'sj_id', 'eid');
    }

    public function user()
    {
        return $this->belongsTo('User', 'uid', 'uid');
    }

    public function profile()
    {
        return $this->hasOne('EmployeeProfile', 'eid', 'eid');
    }


    public function updateEmployee(Employee $employee, $data)
    {
        $rs = $employee->isUpdate(true)->allowField(true)->save($data);
        if($rs === false) exception('Modify the failure');

        return true;
    }


}