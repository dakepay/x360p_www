<?php

namespace app\vipapi\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Model;
use think\Cache;
use think\helper\Str;

class User extends Base
{
	static public $ERR = '';

    protected $type = [
		'last_login_time' => 'timestamp',
	];

    protected $hidden = ['password','create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $insert = ['salt'];

    protected $append = ['token'];


    //登录信息
    protected $login_info = null;


    public function getTokenAttr($value, &$data)
    {
        if(isset($data['token'])){
            return $data['token'];
        }
        $option = [
        	'vip',
            isset($data['uid']) ? $data['uid'] : 0,
            request()->time(),
            request()->ip(),
            Str::random(5)
        ];
        $token  = md5(implode('', $option));
        $data['token'] = $token;
        return $token;
    }

    /**
     * [setPasswordAttr description]
     * @param [type] $value [description]
     * @return string $password
     */
    protected function setPasswordAttr($value)
    {
        if(!isset($this->data['salt'])){
            $this->salt = Str::random(6);
        }
        return passwd_hash($value, $this->salt);
    }


    /**
     * 根据token登录
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    static public function tokenLogin($token){
    	$cache_key = cache_key($token);

        $product_user = cache($cache_key);

        if(!$product_user){
           self::$ERR = _('token_not_exsists');
           return false;
        }

        if(!isset($product_user['client'])){
            self::$ERR = _('invalid_token');
            return false;
        }

        //判断用户是否管理员权限
        $has_vip_per = false;
        if($product_user['is_admin'] == 1){
            $has_vip_per = true;
        }elseif(in_array(10,$product_user['employee']['rids'])){        //10为管理员
            $has_vip_per = true;
        }

        if(!$has_vip_per){
            self::$ERR = _('no_power');
            return false;
        }


        $client = $product_user['client'];

        $w['cid'] = $client['cid'];

        $user = self::where($w)->find();

        if(!$user){
            self::$ERR = _('user_not_exists');
            return false;
        }

         //更新登录信息
        $user->updateLastLoginInfo();
        // $this->login_info 是一个数组
        $login_info = $user->toArray();
        $login_info['client'] = Client::getByCid($w['cid']);
        

        $login_expire = config('api.login_expire');
        $login_info['login_server_time'] = request()->time();
        $login_info['expired'] = $login_expire;

        //写入缓存
        $cache_key = cache_key($user->token);

        cache($cache_key, $login_info, $login_expire);

        $user->setLoginInfo($login_info);

        return $user;
    }

    /**
     * 用户登录
     * @param $account
     * @param $password
     * @param $user_type
     * @param $client_type
     * @return array|bool|false|\PDOStatement|string|Model
     */
	static public function login($account, $password,$client_type = null)
	{
		$w['account'] = $account;

        $user = self::get($w);

        if(!$user){
            if(is_mobile($account)){
				$w = ['mobile'=>$account, 'is_mobile_bind'=>1];
				$user = self::where($w)->find();
			} elseif (filter_var($account, FILTER_VALIDATE_EMAIL)) {
                $w = ['email'=>$account, 'is_email_bind'=>1];
                $user = self::where($w)->find();
            }
            if(!$user){
                self::$ERR = _('account_does_not_exists');
                return false;
            }
        }

        $pwd_hash = passwd_hash($password, $user->salt);

		if($pwd_hash != $user->password){
			self::$ERR = _('password_is_wrong');
			return false;
		}

		//判断是否账号禁用
		if($user->status == 0){
			self::$ERR = _('user_is_disabled');
			return false;
		}

        //更新登录信息
        $user->updateLastLoginInfo();
        // $this->login_info 是一个数组
        $login_info = $user->toArray();
        $login_info['client'] = Client::getByCid($login_info['cid']);
        
		$login_expire = config('api.login_expire');
		$login_info['login_server_time'] = request()->time();
		$login_info['expired'] = $login_expire;

		//写入缓存
		$cache_key = cache_key($user->token);

		cache($cache_key, $login_info, $login_expire);

		$user->setLoginInfo($login_info);

		return $user;
 	}

    protected function setLoginInfo($login_info)
    {
 		$this->login_info = $login_info;
 	}

    /**
 	 * 注销登陆
 	 * @return [type] [description]
 	 */
 	static public function logout($token)
    {
 		$key = cache_key($token);
 		Cache::rm($key);
 	}


 	/**
 	 * 创造用户token
 	 * @param  [type] &$user [description]
 	 * @return [type]        [description]
 	 */
 	public function makeAccessToken(&$user)
    {
 		 $option = [
            $user->uid,
            request()->time(),
            request()->ip(),
            Str::random(5)
        ];
        return md5(implode('',$option));
 	}

 	/**
 	 * 获得登录信息
 	 * @return [type] [description]
 	 */
 	public function loginInfo(){
 		return $this->login_info;
 	}


 	/**
 	 * 更新最后登录信息
 	 * @param  [type] &$user [description]
 	 * @return [type]        [description]
 	 */
 	public function updateLastLoginInfo()
 	{
 		$w['uid'] = $this->uid;
 		$update['last_login_time'] = request()->time();
 		$update['last_login_ip']   = request()->ip();
 		$update['login_times'] 	   = $this->login_times+1;

 		$result = db('user')->where($w)->update($update);

 		return $result;
 	}


 	/**
 	 * 验证密码
 	 * @param  [type] $password [description]
 	 * @return [type]           [description]
 	 */
 	public function verifyPassword($password)
    {
 		$salt = $this->getData('salt');

 		$input_password = passwd_hash($password,$salt);
 	
 		if($input_password == $this->getData('password')){
 			return true;
 		}

 		return false;
 	}

}
