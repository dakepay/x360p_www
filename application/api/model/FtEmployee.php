<?php
namespace app\api\model;



class FtEmployee extends Base
{

    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function employee(){
        return $this->hasOne('Employee','eid','eid');
    }

    public function createFtEmployee($eid,$origin_country){

        $employee_info = get_employee_info($eid);
        if (empty($employee_info)){
            return $this->user_error('员工信息不存在');
        }

        $ft_employee_info = $this->where(['eid' => $eid])->find();
        if (!empty($ft_employee_info)){
            return $this->user_error('员工'.$employee_info['ename'].'已开通外教账号');
        }

        $data = [
            'eid' => $eid,
            'origin_country' => $origin_country
        ];
        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if (!$rs){
            return $this->user_error('ft_employee');
        }

        return true;
    }


    public function editCountry($id,$country){
        $fe_info = $this->get($id);
        if (!$fe_info){
            return $this->user_error('员工信息不存在');
        }
        $w['fe_id'] = $id;
        $update['origin_country'] = $country;
        $rs = $this->save($update,$w);
        if (!$rs){
            return $this->sql_error('ft_employee','edit');
        }

        return true;
    }

    public function deleteFtEmployee($id){
        $fe_info = $this->get($id);
        if (!$fe_info){
            return $this->user_error('员工信息不存在');
        }
        $rs = $fe_info->delete();
        if (!$rs){
            return $this->sql_error('ft_employee','del');
        }

        return true;
    }


}