<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/26 14:00
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------

namespace app\center\auth;

use DawnApi\contract\AuthContract;
use think\Request;
use app\admapi\model\User;

class Auth implements AuthContract
{
    protected $allow_get_token_uri = [];
    protected $allow_post_token_uri = [];
    /**
     * 认证授权通过客户端信息和路由等
     * 如果通过返回true
     * @param Request $request
     * @return bool
     */
    public function authenticate(Request $request)
    {


        $cid = $request->header('x-cid');
        $password = $request->header('x-key');

        if(!$cid || !$password){
            return false;
        }

        $w['cid'] = $cid;
        $w['password'] = $password;

        $client_db_info = db('database_config')->where($w)->find();



        if(!$client_db_info){
            return false;
        }
        unset($w['password']);
        $client = db('client')->where($w)->find();

        gvar('user',$client);

        $request->bind('user',$client);

        return true;
    }

    /**
     * 获取用户信息 接口里可以直接获取用户信息
     * @return mixed
     */
    public function getUser()
    {
        $login_info = gvar('user');
        return $login_info;
    }

    /**
     * 获取客户端信息
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getClient(Request $request){
        return $request->header();
    }

}