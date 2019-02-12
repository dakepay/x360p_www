<?php
/**
 * Author: luo
 * Time: 2017-12-23 11:14
**/

namespace app\sapi\controller;

use app\common\job\TransferMedia;
use app\common\Wechat;
use app\sapi\model\File;
use app\sapi\model\UserStudent;
use app\sapi\model\User as UserModel;
use think\Log;
use think\Request;

class Users extends Base
{

    /**
     * @desc  切换默认学生
     * @author luo
     * @method POST
     */
    public function switch_sid(Request $request)
    {
        $uid = input('uid/d');
        $sid = input('sid/d');

        $is_exist = UserStudent::get(['uid' => $uid, 'sid' => $sid]);
        if(empty($is_exist)) return $this->sendError(400, '学员没有与帐号绑定');

        $user = UserModel::get($uid);
        $rs = $user->updateUser($user, ['default_sid' => $sid]);
        if($rs === false) return $this->sendError(400, $user->getErrorMsg());

        return $this->sendSuccess();
    }

    public function edit_profile(Request $request)
    {
        $action_list = ['resetpwd', 'edit_name', 'edit_mobile'];
        $action = input('action');
        if (!in_array($action, $action_list)) {
            return $this->sendError(400, 'invalid action');
        }

        if (method_exists($this, $action)) {
            return $this->$action($request);
        } else {
            return $this->sendError('method not exists');
        }
    }

    /**
     * @desc  修改昵称
     * @author luo
     * @method GET
     */
    private function edit_name()
    {
        $uid = login_info('uid') || 0;
        $name = input('name');

        if(empty($name) || empty($uid)) return $this->sendError(400, '参数错误');

        $user = UserModel::get($uid);
        $rs = $user->updateUser($user, ['name' => $name]);
        if($rs === false) return $this->sendError(400, $user->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  修改电话
     * @author luo
     * @param Request $request
     * @method GET
     */
    private function edit_mobile(Request $request)
    {
        $uid = login_info('uid');
        $user = UserModel::get($uid);
        $vcode = input('vcode');
        $mobile = input('mobile');
        $result = check_verify_code($mobile, $vcode, 'change');
        if($user['mobile'] == $mobile) return $this->sendError(400, '手机号一致，无需修改');

        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $rs = $user->updateUser($user, ['mobile' => $mobile]);
        if($rs === false) return $this->sendError(400, $user->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  修改密码
     * @author luo
     * @param Request $request
     * @method GET
     */
    private function resetpwd(Request $request)
    {

        $password = input('password');
        if(empty($password)) return $this->sendError(400, '参数错误');

        $uid = login_info('uid');
        $user = UserModel::get($uid);
        $rs = $user->updateUser($user, ['password' => $password]);
        if($rs === false) return $this->sendError(400, $user->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 在微信h5页面调用jssdk上传图片到微信服务器后通过media_id保存用户的图片到七牛
     * @param Request $request
     */
    public function wx_upload_image(Request $request)
    {
        $input = $request->post();
        if (empty($input['serverId'])) {
            return $this->sendError(400, '参数不合法!');
        }
        $data['MediaId'] = $input['serverId'];
        //$data['MsgType'] = $input['image'];
        $data['MsgType'] = 'image';
        $data['uid'] = $request->user['uid'];
        $wx_util = new TransferMedia();
        $res = $wx_util->sync_transfer($data);
        if ($res === false) {
            return $this->sendError(500, $wx_util->getError());
        }
        return $this->sendSuccess($res);
    }
}