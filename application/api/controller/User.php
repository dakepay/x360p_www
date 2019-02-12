<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2018/1/18
 * Time: 11:40
 */

namespace app\api\controller;
use think\Request;

/**
 * 处理登录的当前用户相关的操作
 * Class User
 * @package app\api\controller
 */
class User extends Base
{
    /*绑定mobile和email*/
    public function bind(Request $request)
    {
        $input = $request->only(['type', 'value', 'code']);
        if (empty($input)) {
            return $this->sendError(400, '缺少参数');
        }
        $rule = [
            ['type|绑定类型', 'require'],
            ['value|绑定类型值', 'require'],
            ['code|验证码', 'require'],
        ];
        $check = $this->validate($input, $rule);
        if ($check !== true) {
            return $this->sendError(400, $check);
        }
        $bindAction = $input['type'] == 'mobile' ? 'bindPhone' : 'bindEmail';
        $result = check_verify_code($input['value'], $input['code'], $bindAction);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }
        $user = $request->user;
        unset($user['employee']);
        if ($input['type'] == 'mobile') {
            $user->mobile = $input['value'];
            $user->is_mobile_bind = 1;
            $user->allowField('uid,mobile,is_mobile_bind')->isUpdate(true)->save();
            if ($user->employee) {
                $user->employee->mobile = $input['value'];
                $user->employee->save();
            }
        } elseif ($input['type'] == 'email') {
            $user->email = $input['value'];
            $user->is_email_bind = 1;
            $user->allowField('uid,email,is_email_bind')->isUpdate(true)->save();
            if ($user->employee) {
                $user->employee->email = $input['value'];
                $user->employee->save();
            }
        }
        return $this->sendSuccess();
    }
}