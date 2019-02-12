<?php
/**
 * Author: luo
 * Time: 2017-12-04 16:52
**/

namespace app\admapi\model;

class Role extends Base
{
    protected function setPersAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    protected function setMobilePersAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    protected function getPersAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }

    protected function getMobilePersAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }

    public function delOneRole($rid, Role $role = null) {
        if(($role instanceof Role) == false) {
            $role = $this->find($rid);
        }

        $m_ur = new UserRole();
        $is_exist = $m_ur->where('rid', $rid)->limit(1)->find();
        if($is_exist) return $this->user_error("角色有相关的账户，不能删除");

        $rs = $role->delete();
        if($rs === false) return $this->user_error("删除失败");

        return true;
    }

}