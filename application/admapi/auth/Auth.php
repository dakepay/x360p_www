<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/26 14:00
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------

namespace app\admapi\auth;

use DawnApi\contract\AuthContract;
use think\Request;
use app\admapi\model\User;

class Auth implements AuthContract
{
    protected $allow_get_token_uri = ['import','ueditor','umeditor'];
    protected $allow_post_token_uri = ['export','ueditor','umeditor'];
    /**
     * 认证授权通过客户端信息和路由等
     * 如果通过返回true
     * @param Request $request
     * @return bool
     */
    public function authenticate(Request $request)
    {
        $uri = str_replace('admapi/','',$request->path());

        $token = $request->header('x-token');
        if(!$token){
            if($request->isGet() && in_array($uri,$this->allow_get_token_uri)){
                $token = $request->get('token');
            }
            if($request->isPost() && in_array($uri,$this->allow_post_token_uri)){
                $token = $request->post('token');
                if(!$token){
                    $token = $request->get('token');
                }
            }
        }

        if(!$token){
            return false;
        }

        $cache_key  = cache_key($token);

        $login_info = cache($cache_key);

        if(!$login_info){
            return false;
        }
        //将缓存时间延长
        $expire = config('api.login_expire');
        cache($cache_key,$login_info,$expire);
        gvar('token',$token);
        gvar('uid',$login_info['uid']);
        gvar('user',$login_info);
        gvar('og_id',-1);

        $user = new User($login_info);

        $request->bind('user',$user);
        return true;
    }

    /**
     * 获取用户信息 接口里可以直接获取用户信息
     * @return mixed
     */
    public function getUser()
    {
        $token = request()->header('x-token');
        $login_info = cache(cache_key($token));
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