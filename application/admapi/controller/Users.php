<?php

namespace app\admapi\controller;

use app\admapi\model\User;
use app\admapi\model\UserRole;
use app\api\model\Employee;
use think\Request;

class Users extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_user = new User();
        $ret = $m_user->with('userRole')->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $m_user = new User();
        $rs = $m_user->addOneUser($input);
        if($rs === false) return $this->sendError(400, $m_user->getErrorMsg());

        return $this->sendSuccess();
    }

    public function get(Request $request){
		$key_search_fields = ['account','mobile','email','name'];
		$model = model('user');
		$w = [];
		$input = request()->get();

		foreach($input as $k=>$v){
			if(in_array($k,$key_search_fields)){
				$w[$k] = ['like','%'.$v];
			}
		}

		$page  = input('get.page',1,'intval');
		$pagesize = input('get.pagesize',config('default_pagesize'),'intval');

		$total = $model->where($w)->count();

		$ret['total'] = $total;
		$ret['page']  = $page;
		$ret['pagesize'] = $pagesize;
		$ret['data'] = [];
		if($total > 0){
			$data = $model->where($w)->page($page,$pagesize)->select();
			if($data){
				$ret['data'] = $data;
			}
		}
	
		return $this->sendSuccess($ret);
	}

    public function put(Request $request)
    {
        $uid = input('id/d');
        $input = $request->put();
        $user = User::get(['uid' => $uid]);
        if(empty($user)) return $this->sendError(400, '帐户不存在');

        $rs = $user->updateUser($user, $input);
        if(!$rs) return $this->sendError(400, $user->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $uid = input('id/d');
        $user = User::get(['uid' => $uid]);
        if(empty($user)) return $this->sendError(400, '帐号不存在');

        if(Employee::get(['uid' => $uid])) {
            return $this->sendError('关联了员工不能删除');
        }

        $rs = $user->delOneUser($user);
        if(!$rs) return $this->sendError(400, $user->getErrorMsg());

        return $this->sendSuccess();
    }

    public function post_roles(Request $request)
    {
        $uid = input('id/d');
        $input = $request->post();
        $rids = $input['rids'];

        $user = User::get(['uid' => $uid]);
        if(empty($user)) return $this->sendError(400, '客户不存在');

        $user->addRoles($uid, $rids);

        return $this->sendSuccess();
    }

    /**
     * @desc  移除用户权限
     * @author luo
     * @method GET
     */
    public function delete_role(Request $request)
    {
        $uid = input('id/d');
        $rid = input('subid/d');

        $rs = (new UserRole())->where('uid', $uid)->where('rid', $rid)->delete();
        if($rs === false) return $this->sendError(400, '移除失败');

        return $this->sendSuccess();
    }

    /**
     * @desc  禁用帐号
     * @author luo
     * @method GET
     */
    public function do_disable(Request $request, $id) {

        $user = User::get($id);
        if(empty($user)) return $this->sendError(400, '用户不存在');

        $rs = $user->toggleAccountStatus($id, 0);

        if(!$rs) return $this->sendError(400,$user->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  启用帐号
     * @author luo
     * @method GET
     */
    public function do_active(Request $request, $id) {
        $user = User::get($id);
        if(empty($user)) return $this->sendError(400, '用户不存在');

        $rs = $user->toggleAccountStatus($id, 1);

        if(!$rs) return $this->sendError(400,$user->getErrorMsg());

        return $this->sendSuccess();
    }

}