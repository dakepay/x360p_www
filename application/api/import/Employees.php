<?php
namespace app\api\import;

use app\common\Import;
use app\api\model\Employee;

class Employees extends Import
{
    protected $res = 'employee';
    protected $start_row_index = 3;
    protected $pagesize = 20;

    protected $role_map = [];

    protected $fields = [
        ['field'=>'ename','name'=>'姓名','must'=>true],
        ['field'=>'sex','name'=>'性别','must'=>true],
        ['field'=>'mobile','name'=>'手机号码','must'=>true],
        ['field'=>'email','name'=>'邮箱','must'=>true],
        ['field'=>'bids','name'=>'所属校区','must'=>true],
        ['field'=>'rids','name'=>'所属角色','must'=>true],
        ['field'=>'birth_time','name'=>'出生日期'],
        ['field'=>'account','name'=>'账号'],
        ['field'=>'password','name'=>'密码'],
    ];

    protected function init_role_map(){
        if(!empty($this->role_map)){
            return;
        }
        $w_og['og_id'] = gvar('og_id');
        $role_list = model('role')->where($w_og)->whereOr('rid','lt',11)->select();

        foreach($role_list as $k=>$r){
            $key = md5($r['role_name']);
            $this->role_map[$key] = $r;
        }

    }

    protected function get_fields()
    {
        return $this->fields;
    }

    protected function convert_sex($value)
    {
        $map = ['男' => 1, '女' => 2];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return 0;
    }

    protected function convert_mobile($value)
    {
        if(!is_mobile($value)) exception('格式不正确');
        return $value;
    }

    protected function convert_email($value)
    {
        if(!is_email($value)) exception('格式不正确');
        return $value;
    }

    protected function convert_birth_time($value)
    {
        // UNIX_DATE = (EXCEL_DATE - 25569) * 86400
        $timestamp = ($value - 25569) * 86400;
        return date('Y-m-d', $timestamp);
    }

    protected function init_branch(){
        if(empty($this->all_branchs)){
            $w['og_id'] = gvar('og_id');
            $branch_list = get_table_list('branch',$w);
            foreach($branch_list as $b){
                $this->all_branchs[$b['bid']] = $b;
            }
        }
    }


    protected function convert_bids($value)
    {
        $branch_name = $value;
        $this->init_branch();
        if($branch_name == '所有校区' || $branch_name == '所有'){
            return implode(',',array_keys($this->all_branchs));
        }
        $bids = [];
        if(strpos($branch_name,',') === false) {
            foreach ($this->all_branchs as $k => $b) {
                if ($b['branch_name'] == $branch_name || $b['branch_name'] == $branch_name) {
                    array_push($bids,$k);
                    break;
                }
            }
        }else{
            $branch_names = explode(',',$branch_name);
            foreach($branch_names as $bn){
                foreach ($this->all_branchs as $k => $b) {
                    if ($b['branch_name'] == $bn || $b['branch_name'] == $bn) {
                        array_push($bids,$k);
                        break;
                    }
                }
            }
        }
        $bids = implode(',',$bids);
        return $bids;
    }

    protected function convert_rids($value)
    {
        $this->init_role_map();
        $value = explode('|', $value);
        $rids = [];
        foreach($value as $role_name){
            $key = md5($role_name);
            if(isset($this->role_map[$key])){
                $rids[] = $this->role_map[$key]['rid'];
            }
        }

        if (empty($rids)) {
            throw new \Exception('员工的角色信息错误');
        }
        return $rids;
    }

    protected function import_row(&$row,$row_no){
        $fields = $this->get_fields();
        $add = [];

        foreach($fields as $index => $f){
            $field = $f['field'];
            $cell = $row[$index];
            if(is_object($cell)){
                $value = $cell->getPlainText();
            }else{
                $value = $cell;
            }

            $func = 'convert_'.$field;

            if(empty($value)){
                if(isset($f['must']) && $f['must'] === true){
                    $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']没有填写!';
                    return 2;
                }
            }else{
                $add[$field] = trim($value);
                if(method_exists($this, $func)){
                    try {
                        $add[$field] = $this->$func($value, $add, $row);
                    } catch (\Exception $e) {
                        $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']' . $e->getMessage();
                        return 2;
                    }
                }
            }
        }
        return $this->add_data($add,$row_no);
    }

    /**
     * 添加数据到数据库
     * @param [type] $data   [description]
     * @param [type] $row_no [description]
     * @return  0 成功
     * @return  2 失败
     * @return  1 重复
     */
    protected function add_data($data,$row_no){
        $input = [];
        $openAccount = false;
        if (!empty($data['account']) && !empty($data['password'])) {
            $input['user']['account'] = $data['account'];
            $input['user']['password'] = $data['password'];
            $openAccount = true;
        }
        unset($data['account'], $data['password']);
        $input['employee'] = $data;
        $w['ename'] = $data['ename'];
        $w['mobile'] = $data['mobile'];
        $m_employee = new Employee();
        $exists_employee = $m_employee->where($w)->find();

        //update
        if ($exists_employee) {
            $result = $m_employee->editEmployee($exists_employee['eid'], $input['employee']);
            if (!$result) {
                $this->import_log[] = '第'.$row_no.'行的员工资料有更新，但是更新失败,SQL:' . $m_employee->getLastSql() . print_r( $input['employee'],true);
                return 1;
            }
            if (isset($result['affect_rows']) && !$result['affect_rows']) {
                $this->import_log[] = '第'.$row_no.'行的数据有重复!';
                return 1;
            }
            $this->import_log[] = '第'.$row_no.'行的员工资料有更新，更新成功!';
            return 1;
        }

        //add
        $result = $m_employee->createEmployee($input, $openAccount);
        if (!$result) {
            $this->import_log[] = '第' . $row_no . '行的数据写入数据库失败:' . $m_employee->getError();
            return 2;
        }
        return 0;
    }
}