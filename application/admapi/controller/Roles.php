<?php
/**
 * Author: luo
 * Time: 2017-12-04 16:58
**/

namespace app\admapi\controller;


use app\admapi\model\Role;
use app\admapi\model\User;
use app\admapi\model\UserRole;
use think\Request;

class Roles extends Base
{

    public function get_list(Request $request)
    {
        return parent::get_list($request);
    }

    public function post(Request $request)
    {
        return parent::post($request);
    }

    //权限相关的帐号
    public function get_list_users(Request $request)
    {
        $rid = input('id/d');
        $input = $request->param();

        $m_ur = new UserRole();
        $ret = $m_ur->where('rid', $rid)->getSearchResult($input);
        foreach($ret['list'] as $key => &$row) {
            $user = User::get($row['uid'], ['employee']);
            if(empty($user)) {
                unset($ret['list'][$key]);
                continue;
            }
            $row = array_merge($row, $user->toArray());
        }

        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $rid = input('id/d');
        $role = Role::get(['rid' => $rid]);
        if(empty($role)) return $this->sendError(400, '角色不存在');

        $input = $request->put();
        $rs = $role->allowField(true)->isUpdate(true)->save($input);
        if($rs === false) return $this->sendError(400, '更新失败');

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $rid = input('id/d');
        $role = Role::get(['rid' => $rid]);
        if(empty($role) || $role['is_system'] === 1) return $this->sendError(400, '不能删除');

        $rs = $role->delOneRole($rid, $role);
        if($rs === false) return $this->sendError(400, '删除失败');

        return true;
    }

    /**
     * @desc  帐号批量增加权限
     * @author luo
     * @method GET
     */
    public function post_users(Request $request)
    {
        $rid = input('id/d');
        $uids = input('uids/a');

        $m_user = new User();
        $rs = $m_user->addBatchRole($rid, $uids);
        if($rs === false) return $this->sendError(400, $m_user->getErrorMsg());

        return $this->sendSuccess();
    }


}