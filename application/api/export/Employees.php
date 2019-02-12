<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 15:34
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\Employee;

class Employees extends Export
{
    protected $res_name = 'employee';

    protected $columns = [
        ['title'=>'员工姓名','field'=>'ename','width'=>20],
        ['title'=>'性别','field'=>'sex','width'=>20],
        ['title'=>'手机号码','field'=>'mobile','width'=>20],
        ['title'=>'邮箱','field'=>'email','width'=>20],
        ['title'=>'所属校区','field'=>'bids','width'=>30],
        ['title'=>'所属角色','field'=>'rids','width'=>30],
        ['title'=>'出生日期','field'=>'birth_time','width'=>20],
        ['title'=>'账号','field'=>'account','width'=>20],
    ];

    protected function get_title(){
        $title = '员工信息表';
        if (isset($this->params['bid']) && $this->params['bid'] !== 0 && $this->params['bid'] !== -1) {
            $branch_name = get_branch_name($this->params['bid']);
            if (!empty($branch_name)) {
                $title .= $branch_name;
            }
        }

        return $title;
    }

    public function get_data()
    {
        if (isset($this->params['bid']) && $this->params['bid'] !== 0 && $this->params['bid'] !== -1) {
            $branch = model('Branch')->find($this->params['bid']);
            $list = $branch->branchEmployees;
        } else {
            $list = Employee::all();
        }
        foreach ($list as $employee) {
            $employee->sex = get_sex($employee->sex);
            $employee->bids = implode('|',$employee->branches()->column('short_name'));
            $employee->rids = implode('|', $employee->roles()->column('role_name'));
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];
    }
}