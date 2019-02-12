<?php

namespace app\api\controller;

use think\Request;
use app\api\model\Org;
use app\api\model\User;
use think\Config;
use think\Db;

class Fshop extends Base
{
    public $withoutAuthAction = ['franchisees'];
    public $noRest = true;

    /**
     * post /api/fshop/franchisees
     * {host:smtjy,key:xxxxxxxx}
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function franchisees(Request $request)
    {
        $client = gvar('client');
        $input = $request->only(['account', 'password', 'key']);

        $pos = strrpos($input['account'], '@');

        if ($pos === false) {
            return $this->sendError(400, _('account_does_not_exists'));
        }else{
            $sub_host = substr($input['account'], $pos + 1);

            if (!$client['defined'] || $client['domain'] != $sub_host) {
                $client = load_client_by_host($sub_host);
                gvar('client', $client);
                gvar('og_id', $client['og_id']);
            }

            if(!$client['defined']) {
                return $this->sendError(400, _('account_does_not_exists'));
            }
            $input['account'] = substr($input['account'], 0, -(strlen($sub_host) + 1));
        }
        $client_type = '';
        $user = User::login($input['account'], $input['password'], 1, $client_type);
        if(!$user) {
            return $this->sendError(400, User::$ERR);
        }
        $rs = $this->verify($user,$sub_host,$input['key']);
        if (!$rs){
            return $this->sendError(400, 'key 或 账号密码错误');
        }

        $mOrg = new Org();
        $sql = 'select o.mobile,o.org_name,u.account,u.name,u.salt,u.password FROM x360p_org 
                as o LEFT JOIN x360p_user as u ON o.og_id = u.og_id WHERE u.is_admin = 1';
        $user_list = $mOrg->query($sql);
        return $this->sendSuccess($user_list);
    }

    /**
     * 商城系统用户导入key
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_key(){
        $user = gvar('user');
        $client = gvar('client');

        if (!$user || !$client || empty($user['password']) || empty($client['info']['host'])){
            return $this->sendError(400,'user or client not exists');
        }
        $key = md5($user['password'].$client['info']['host']);

        return $this->sendSuccess($key);
    }

    /**
     * 商城信息验证
     * @param $host
     * @param $key
     */
    protected function verify($user,$host,$key)
    {
        $token = Md5($user['password'].$host);
        if ($token != $key){
            return false;
        }

        return true;
    }

}