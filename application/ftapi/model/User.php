<?php

namespace app\ftapi\model;

use think\Db;
use think\Exception;
use think\Model;
use think\Cache;
use think\helper\Str;
use think\Request;
use app\common\Wechat;


class User extends Base
{
	static public $ERR = '';

    const EMPLOYEE_ACCOUNT = 1;
    const STUDENT_ACCOUNT  = 2;

    protected $type = [
		'last_login_time' => 'timestamp',
	];

    protected $insert = ['salt'];
    protected $hidden = ['salt', 'password', 'is_admin'];


    protected $append = ['token','bid'];

    /**
     * 获得校区ID 属性
     * @param  [type] $value [description]
     * @param  [type] &$data [description]
     * @return [type]        [description]
     */
    public function getBidAttr($value, &$data)
    {
        if(isset($data['bid'])){
            return $data['bid'];
        }
        $branchs = m('branch')->select();
        $branchs = $this->getPermissionBranchs($branchs);
        if(empty($branchs)){
            $data['bid'] = 0;
        }else{
            $data['bid'] = $branchs[0]['bid'];
        }

        return $data['bid'];
    }

    //登录信息
    protected $login_info = null;

    public function employee()
    {
        return $this->hasOne('Employee','uid','uid','LEFT');
    }

    public function students()
    {
        return $this->belongsToMany('Student', 'UserStudent', 'sid', 'uid');
    }

    public function getTokenAttr($value, &$data)
    {
        if(isset($data['token'])){
            return $data['token'];
        }
        $option = [
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

    public function getPermissionBranchs(&$branchs)
    {
        if (isset($this->data['is_admin']) && $this->getData('is_admin')) {
            return $branchs;
        } else {
            return $this->getAttr('employee')['branches'];
        }
	}

    /**
     * 用户登录
     * @param $account
     * @param $password
     * @param $user_type
     * @param $client_type
     * @return array|bool|false|\PDOStatement|string|Model
     */
	static public function login($account, $password, $user_type = 1, $client_type = null)
	{
		$w['account'] = $account;
		$w['user_type'] = $user_type;
        $user = self::where($w)->find();

        unset($w);
        if(!$user){
            if(is_mobile($account)){
				$w = ['account'=>$account];
				$w['user_type'] = $user_type;
				$user = self::where($w)->find();
            } elseif (filter_var($account, FILTER_VALIDATE_EMAIL)) {
                $w = ['email'=>$account, 'is_email_bind'=>1, 'user_type' => $user_type];
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

		$ft_info = get_ft_employee_by_uid($user->uid);
		if (!$ft_info){
            self::$ERR = _('This account is not open foreign teacher');
            return false;
        }
        $user->eid = $ft_info['eid'];
        gvar('user',$user);


        //更新登录信息
        $user->updateLastLoginInfo();
		$user->addLoginLog($ft_info);
        $login_info = $user->toArray();

        $login_info['ft_employee'] = $ft_info;
        $login_info['employee'] = Employee::get(($ft_info['eid']));
		$login_expire = config('api.login_expire');
		$login_info['login_server_time'] = request()->time();
		$login_info['expired'] = $login_expire;
        $login_info['client'] = gvar('client');

        //写入缓存
		$cache_key = cache_key($user->token);
		cache($cache_key, $login_info, $login_expire);
		$read_cache = cache($cache_key);
		$user->setLoginInfo($login_info);

		return $user;
 	}

    protected function setLoginInfo($login_info)
    {
 		$this->login_info = $login_info;
 	}

    /**
     * 登陆日志
     * @param $ft_info
     * @return bool|false|int
     */
    public function addLoginLog($ft_info)
    {
        $og_id = gvar('og_id');
        $fe_id   = $ft_info['fe_id'];
        if(!$fe_id){
            return false;
        }
        $employee_info = get_employee_info($ft_info['eid']);
        if(!$employee_info){
            return false;
        }
        $request = request();
        $data = [
            'og_id' => $og_id,
            'bid'   => $employee_info['bid'],
            'uid' => $this->uid,
            'fe_id' => $fe_id,
            'ip' => $request->ip(),
            'user_agent' => substr($request->header('user-agent'),0, 253),
            'login_time' => $request->time(),
        ];

        $result = (new FtLoginLog())->allowField(true)->isUpdate(false)->save($data);
        return $result;
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


    public function updateUser(User $user, $data)
    {
        $rs = $user->isUpdate(true)->allowField(true)->save($data);
        if($rs === false) exception('Modify the failure');

        return true;
    }

    /**
     * @desc  验证码验证
     * @param Request $request
     * @method POST
     */
    public function check_vcode($mobile,$vcode)
    {
        $rs = check_verify_code($mobile, $vcode, 'ft');
        if ($rs !== true) {
            return false;
        }

        return true;
    }


    /**
     *  修改邮箱
     * @param $email
     * @return bool
     */
    public function edit_email($email)
    {
        $uid = login_info('uid') ? login_info('uid') : 0;
        $user_info = $this->get($uid);
        if(empty($user_info)) return $this->user_error(400, 'user not exist');

        $this->startTrans();
        try {
            $rs = $this->updateUser($user_info,['email' => $email]);
            if($rs === false) return $this->user_error(400, 'user');

            $mEmployee = new Employee();
            $employee_info = $mEmployee->where(['uid' => $uid])->find();
            $rs = $mEmployee->updateEmployee($employee_info, ['email' => $email]);
            if($rs === false) return $this->user_error(400, 'employee');

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 修改手机号
     * @param $new_mobile
     * @return bool
     */
    public function edit_mobile($new_mobile){

        $uid = login_info('uid') ? login_info('uid') : 0;
        $user_info = $this->get($uid);
        if(empty($user_info)) return $this->user_error(400, 'user not exist');

        $this->startTrans();
        try {
            $rs = $this->updateUser($user_info,['mobile' => $new_mobile]);
            if($rs === false) return $this->user_error(400, 'user');

            $mEmployee = new Employee();
            $employee_info = $mEmployee->where(['uid' => $uid])->find();
            $rs = $mEmployee->updateEmployee($employee_info, ['mobile' => $new_mobile]);
            if($rs === false) return $this->user_error(400, 'employee');

        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();

        return true;
    }

    /**
     * 关注并且绑定公众号的二维码
     * @return string|array
     */
    public function getWechatQrcode($param = [])
    {
        //二维码的自定义参数
        $data = [];
        $data['cid']   = Request::instance()->client['cid'];
        $data['og_id'] = $this->getData('og_id');
        $data['uid']   = $this->getData('uid');
        is_array($param) && !empty($param) && $data = array_merge($param, $data);
        $cache_key = 'user_wechat_bind_status:' . $data['cid'] . ':' . $data['uid'];
        try {
            if ($this->getData('user_type') == self::STUDENT_ACCOUNT) {

                /*学生家长账号*/
                $students = $this->getAttr('students');
                if (empty($students)) {
                    throw new Exception('该家长账号不存在关联的学生，无法确定校区!');
                }
                $data['bid'] = $students[0]['bid'];
                $app = Wechat::getApp($students[0]);
                $qrcode = $app->qrcode;
                $result = $qrcode->temporary(json_encode($data), 30 * 24 * 3600);
                $ticket = $result->ticket;
                $url = $qrcode->url($ticket);
//            return $url;
            } else {
                /*员工账号*/
                if ($this->getData('is_admin')) {
                    $branch_list = Branch::all(['og_id' => gvar('og_id')]);
                } else {
                    $employee = $this->getAttr('employee');
                    $branch_list = $employee['branches'];
                }

                $customer_default = Wxmp::get(['is_default' => 1, 'og_id' => gvar('og_id')]);

                if ($customer_default) {
                    $customer_default_appid = $customer_default['authorizer_appid'];
                }
                $system_default_appid = Authorizer::getSystemDefault()['authorizer_appid'];
                $appid_list = [];
                $url_list = [];
                /*如果员工账号有多个校区，只需要让他绑定其中一个校区的公众号*/
                foreach ($branch_list as $item) {//todo
                    $default = false;
                    $appid = Wechat::getAppid($item);
                    if (in_array($appid, $appid_list)) {
                        continue;
                    }
                    $appid_list[] = $appid;
                    /** @var Qrcode $qrcode */
                    $qrcode = Wechat::getInstance($appid)->app->qrcode;
                    if ($system_default_appid == $appid || (!empty($customer_default_appid) && $customer_default_appid == $appid)) {
                        $default = true;
                    }

                    $data['bid'] = $item['bid'];
                    $result = $qrcode->temporary(json_encode($data), 30 * 24 * 3600);
                    $ticket = $result->ticket;
                    $url    = $qrcode->url($ticket);
                    if ($default) {
                        /*如果是系统默认或客户默认的服务号*/
                        array_unshift($url_list, $url);
                        break;
                    } else {
                        $url_list[] = $url;
                    }
                }
                $url = array_shift($url_list);
            }
            Cache::set($cache_key, 0, 300);
            return $url;
        } catch (\Exception $exception) {
            return $this->user_error($exception->getMessage());
        }
    }

    /*解除一个用户的微信绑定*/
    public function unbindWechat()
    {
        try {
            $this->startTrans();
            $w['openid'] = $this->getData('openid');
            $local_fans = WxmpFans::get($w);
            $data = [];
            $data['cid'] = 0;
            $data['og_id'] = 0;
            $data['bid'] = 0;
            if($this->getData('user_type') == self::EMPLOYEE_ACCOUNT) {
                $data['employee_uid'] = 0;
            } else {
                $data['uid'] = 0;
            }
            if ($local_fans) {
                $local_fans->allowField(true)->save($data);
                if ($local_fans['is_system']) {
                    CenterWechatFans::update($data, $w, true);
                }
            }
            $data = [];
            $data['openid'] = '';
            $data['is_weixin_bind'] = 0;
            $this->save($data);
            $this->commit();

            /*排除刚刚绑定又立马解绑的情况,前端会轮询绑定状态*/
            $cache_key = 'user_wechat_bind_status:' . gvar('client')['cid'] . ':' . $this->getData('uid');
            Cache::rm($cache_key);
            return true;
        } catch (\Exception $exception) {
            $this->rollback();
            return $this->user_error($exception->getMessage());
        }
    }


}
