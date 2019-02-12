<?php

namespace app\vipapi\controller;

use think\Hook;
use think\Request;
use util\sms;
use app\vipapi\model\User as UserModel;

/**
 * Class Open
 * @title 公共接口
 * @url /
 * @desc  需要验证登录的公共接口
 * @version 1.0
 * @readme 
 */
class Index extends Base
{
    public $noRest = true;

    public function profile(Request $request){
        $input = $request->post();
        if(empty($input['action'])){
            return $this->sendError('400','input_param_error');
        }
        $user = $request->user;
        $result = $user->saveProfile($input, $input['action']);
        if(!$result){
            return $this->sendError(400, $user->getError());
        }
        return $this->sendSuccess('ok');
    }


     /**
     * @title 退出登录
     * @url logout
     * @desc 退出系统时调用
     */
    public function logout(Request $request){
        $user = $this->getUserInfo();
        UserModel::logout($user['token']);
        return $this->sendSuccess('ok');
    }

    /**
     * @title 获得全局变量
     * @url   global/:name
     * @desc  获得系统的全局变量
     * @return [type] [description]
     */
    public function glob(Request $request,$name){
        $data = $this->getGlobalVars($name);
        if($name == 'branchs'){
            $data = $request->user->getPermissionBranchs($data);
        }

        return $this->sendSuccess($data);
    }

}
