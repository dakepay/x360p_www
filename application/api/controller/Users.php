<?php

namespace app\api\controller;

use app\api\model\Student;
use app\api\model\User;
use think\Cache;
use think\Request;

/**
 * Class User
 * @title 用户接口
 * @url users
 * @desc  有关于用户的接口
 * @version 1.0
 * @readme /md/api/users.md
 */
class Users extends Base
{
	/**
    * @title 获取用户列表
    * @desc 获取用户列表
    * @url users
    * @readme 
    */
	public function get_list(Request $request){
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
        $m_user = new User();
        $user = $m_user->skipOgId(true)->where(['uid' => $uid])->find();
        if(empty($user)) return $this->sendError(400, '帐户不存在');

        $rs = $user->updateUser($user, $input);
        if(!$rs) return $this->sendError(400, $user->getErrorMsg());
        return $this->sendSuccess();
    }

    /**
     * @desc  删除第二联系人
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $uid = input('id');
        $m_user = new User();

        $rs = $m_user->delStudentUser($uid);
        if($rs === false) return $this->sendError(400, $m_user->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 管理员在后台给org_user重置密码
     */
    public function do_resetpwd(Request $request)
    {
        $uid = $request->param('id');
        $user = User::get($uid);
        if (!$user) {
            return $this->sendError(400, '账号不存在或已删除');
        }

        $password = $request->post('password');
        $data = [];
        $data['password'] = $password;
        $right = $this->validate($data, 'User.resetpwd');
        if (true !== $right) {
            return $this->sendError(400, $right);
        }
        $result = $user->resetpwd($password);
        if (!$result) {
            return $this->sendError(400, $user->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 获取用户关注公众号并且绑定账号的二维码
     */
    public function get_list_wechat_qrcode(Request $request)
    {
        $uid  = $request->param('id');
        $user = User::get($uid);
        if (empty($user)) {
            return $this->sendError(400, '用户不存在或已删除!');
        }
        $code_type = input('code_type', 'bind_account');
        $rs = $user->getWechatQrcode(['code_type'  => $code_type]);
        if ($rs === false) {
            return $this->sendError(400, $user->getError());
        } else {
            $data['url'] = $rs;
            return $this->sendSuccess($data);
        }
    }

    /**
     * 解除一个用户的微信绑定
     * @param Request $request
     */
    public function do_unbind(Request $request)
    {
        $uid = $request->param('id');
        $user = User::get($uid);
        $res = $user->unbindWechat();
        if (!$res) {
            return $this->sendError(400, $user->getError());
        }
        return $this->sendSuccess();
    }

    /*微信绑定状态查询*/
    public function get_bind_status(Request $request)
    {
        $cache_key = 'user_wechat_bind_status:' . gvar('client')['cid'] . ':' . $request->user['uid'];
        $value = Cache::get($cache_key);
        if ($value === false) {
            $data['status'] = -1;
            return $this->sendSuccess($data, '没有绑定会话或长时间未操作会话已过期!');
        } elseif ($value === 0) {
            $data['status'] = 0;
            return $this->sendSuccess($data, '未扫描!');
        } elseif ($value == 1) {
            $data['status'] = 1;
            return $this->sendSuccess($data, '已扫描并绑定！');
        } else {
            $data['status'] = 2;
            return $this->sendSuccess($data, $value);
        }
    }
}