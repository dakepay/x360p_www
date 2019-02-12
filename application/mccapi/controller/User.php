<?php
namespace app\mccapi\controller;

use think\Log;
use think\Request;


class User extends Base{
    public $noRest  = true;

    //更新微信资料
    public function Upwxprofile(Request $request){
    	$post = input('post.');

    	$user = $request->user;

    	$result = $user->updateWxProfile($post);

    	if(false === $result){
    		return $this->sendError(400,$user->getError());
    	}

        $user->updateToken();

    	return $this->sendSuccess($user);
    }

    //更新用户类型 1：老师，2：家长
    public function Uputype(Request $request){
        $user_type = input('post.user_type/d');

        $user = $request->user;

        $result = $user->updateUserType($user_type);

        if(false === $result){
            return $this->sendError(400,$user->getError());
        }

        $user->updateToken();
        
        return $this->sendSuccess($user);
    }
}