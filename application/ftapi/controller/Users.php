<?php

namespace app\ftapi\controller;

use app\ftapi\model\User as UserModel;
use think\Cache;
use think\Request;

class Users extends Base
{

    /**
     * 修改邮箱
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function edit_email(Request $request)
    {
        $email = input();
        if (empty($email['email'])){
            return $this->sendError(400,'params is error');
        }
        $mUser = new UserModel();
        $w['email'] = $email['email'];
        $user_info = $mUser->where($w)->find();
        if ($user_info){
            return $this->sendError(400,'This mailbox is already in use');
        }
        $rs = $mUser->edit_email($email['email']);
        if (!$rs){
            return $this->sendError(400,$mUser->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * 修改手机号码
     * @param Request $request
     * @return Redirect|bool|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function edit_mobile(Request $request)
    {
        $input = input();

        if (empty($input['old_mobile']) || empty($input['new_mobile']) || empty($input['vcode'])){
            return $this->sendError(400,'params is error');
        }

        $mUser = new UserModel();
        $w_old['mobile'] = $input['old_mobile'];
        $old_user_info = $mUser->where($w_old)->find();
        if (!$old_user_info){
            return $this->sendError(400,'The phone number does not exist');
        }

        $rs = $mUser->check_vcode($input['old_mobile'],$input['vcode']);
        if (!$rs){
            return $this->sendError(400,'code is error');
        }

        $w_new['mobile'] = $input['new_mobile'];
        $new_user_info = $mUser->where($w_new)->find();
        if ($new_user_info){
            return $this->sendError(400,'The phone number has been bound');
        }

        $rs = $mUser->edit_mobile($input['new_mobile']);
        if (!$rs){
            return $this->sendError(400,$mUser->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * 获取用户关注公众号并且绑定账号的二维码
     */
    public function wechat_qrcode(Request $request)
    {
        $uid = login_info('uid') ? login_info('uid') : 0;
        $user = UserModel::get($uid);
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
        $uid = login_info('uid') ? login_info('uid') : 0;
        $user = UserModel::get($uid);
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