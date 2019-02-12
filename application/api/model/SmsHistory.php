<?php
/**
 * Author: luo
 * Time: 2018/6/1 17:14
 */

namespace app\api\model;


use app\common\exception\FailResult;
use util\sms\EasySms;

class SmsHistory extends Base
{
    protected $type = [
        'tpl_data' => 'json',
    ];

    protected $append = ['name', 'create_employee_name'];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected function setTplDataAttr($value)
    {
        return empty($value) || !empty(json_decode($value, true)) ? $value : (is_array($value) ? json_encode($value) : $value);
    }

    public function setBidAttr($value,$data){
        if(is_null($value)){
            return 0;
        }
        $value = intval($value);
        return $value;
    }

    public function getNameAttr($value, $data)
    {
        $name = '';
        if(!empty($data['sid']) && $data['sid'] > 0) {
            $name = get_student_name($data['sid']);
        }elseif(!empty($data['cu_id']) && $data['cu_id'] > 0) {
            $name = get_customer_name($data['cu_id']);

        }elseif(!empty($data['mcl_id']) && $data['mcl_id'] > 0) {
            $name = get_mcl_name($data['mcl_id']);

        }elseif(!empty($data['eid']) && $data['eid'] > 0){
           $name = get_employee_name($data['eid']);
        }

        return $name;
    }

    public function getCreateEmployeeNameAttr($value, $data)
    {
        if(empty($data['create_uid'])) return '';
        $employee = get_employee_info(User::getEidByUid($data['create_uid']));
        return empty($employee) ? '' : $employee['ename'];
    }

    public function addSmsHistory($data)
    {
        $rule = [
            'mobile' => 'require',
            'tpl_id' => 'require',
            'tpl_data' => 'require',
        ];
        $validate = validate();
        $rs = $validate->rule($rule)->check($data);
        if($rs !== true) $this->user_error($rs);

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) throw new FailResult($this->getErrorMsg());

        return true;
    }

}