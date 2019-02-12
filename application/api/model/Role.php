<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/21
 * Time: 9:30
 */
namespace app\api\model;

use think\Db;

class Role extends Base
{
    const ROLE_TEACHER_ID = 1;

    public function getRoleNameAttr($value,$data){
        return user_role_name($data['rid'],$value);
    }

    public function employee()
    {
        return $this->belongsToMany('Employee', 'EmployeeRole', 'eid', 'rid');
    }

    
    public function createRole($data)
    {
        $result = $this->allowField(true)->save($data);
        if (false === $result) {
            return false;
        }
        return true;
    }

    public function deleteRole()
    {
        $rid = $this->getData('rid');
        if($rid < 11){
            return $this->user_error('系统内置角色不允许删除!');
        }
        $this->startTrans();
        try {
            $this->delete(true);
            Db::name('employee_role')->where('rid', $rid)->delete();
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
        $this->commit();
        return true;
    }

    public function getPersAttr($value, $data)
    {
        $rid  = $this->getData('rid');
        $pers = trim($value, ',');
        if(empty($pers)){
            $pers = $this->getDefaultRolePers($rid);
        }
        if($rid < 11){
            //加盟商的系统角色权限
            $og_id = gvar('og_id');
            if($og_id > 0){
                $user_org_role = user_config('org_role');
                foreach ($user_org_role as $k => $r) {
                    if ($r['rid'] == $rid && isset($r['pers']) && !empty($r['pers'])) {
                        $pers = $r['pers'];
                        break;
                    }
                }
            }
        }
        if(!is_array($pers)){
            $pers = explode(',',$pers);
        }
        return $pers;
    }

    public function setPersAttr($value)
    {
        return join(',', $value);
    }

    public function getMobilePersAttr($value, $data)
    {
        return explode(',', trim($value, ',')) ;
    }

    public function setMobilePersAttr($value)
    {
        return join(',', $value);
    }

    /*获取角色为老师的员工*/
    public static function getTeachers()
    {
        $role = self::get(Role::ROLE_TEACHER_ID);
        return $role['employee'];
    }

    protected function getDefaultRolePers($rid){
        $roles = config('org_role');
        $pers  = '';
        foreach($roles as $role){
            if($rid == $role['rid']){
                $pers = $role['pers'];
                break;
            }
        }
        return $pers;
    }
}