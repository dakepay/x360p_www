<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/19
 * Time: 19:11
 */
namespace app\sapi\model;

use Overtrue\Pinyin\Pinyin;
use think\Exception;
use think\Db;

class Employee extends Base
{
    protected $type = [
        'birth_time' => 'timestamp'
    ];
    protected $autoWriteTimestamp = true;

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


}