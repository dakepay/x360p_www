<?php

namespace app\sapi\model;

use think\Db;
use think\Exception;
use think\Model;
use think\Cache;
use think\helper\Str;

class User extends Base
{
	static public $ERR = '';

    const EMPLOYEE_ACCOUNT = 1;
    const STUDENT_ACCOUNT  = 2;

    protected $type = [
		'last_login_time' => 'timestamp',
	];

    protected $insert = ['salt'];

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
	static public function login($account, $password, $user_type = 2, $client_type = null)
	{
		$w['account'] = $account;
		$w['user_type'] = $user_type;
		$user = self::where($w)->find();
        unset($w);
        if(!$user){
            if(is_mobile($account)){
				$w = ['mobile'=>$account];
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

        if ($user['user_type'] == 2) {
		    //帐号相关的学生
            $mUserStudent = new UserStudent();
            $mStudent = new Student();
            $w_us['uid'] = $user['uid'];
            $us_list = $mUserStudent->where($w_us)->skipOgid()->select();
            $students = [];
            if(empty($us_list)){
                if($user['default_sid'] > 0){
                    //修复用户学员关系
                    $us['uid'] = $user['uid'];
                    $us['sid'] = $user['default_sid'];
                    $mUserStudent->save($us);
                }
                $m_student = $mStudent->find($user['default_sid']);
                $students[] = $m_student->toArray();
            }else{
                foreach($us_list as $us){
                    $m_student = $mStudent->find($us['sid']);
                    if($m_student){
                        $students[] = $m_student->toArray();
                    }
                }
            }

            if($user['default_sid'] == 0 && !empty($students)){
                $user['default_sid'] = $students[0];
                $user->allowField('default_sid')->save();
            }
            $user['students'] = $students;
        }

        //更新登录信息
        $user->updateLastLoginInfo();
		$user->addLoginLog();
        $login_info = $user->toArray();

        //获得权限列表和菜单列表 pers navs
		self::setUserPer($login_info, $client_type);

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

    public function addLoginLog()
    {
        $og_id = gvar('og_id');
        $sid   = $this->default_sid;
        if(!$sid){
            return false;
        }
        $sinfo = get_student_info($sid);
        if(!$sinfo){
            return false;
        }
        $bid = $sinfo['bid'];
        $request = request();
        $data = [
            'og_id' => $og_id,
            'bid'   => $bid,
            'uid' => $this->uid,
            'sid' => $sid,
            'ip' => $request->ip(),
            'user_agent' => substr($request->header('user-agent'),0, 253),
            'login_time' => $request->time(),
        ];

        $result = (new MobileLoginLog())->allowField(true)->isUpdate(false)->save($data);
        return $result;
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

 	public function getRolesAndPers($id)
 	{
 		$user = self::get($id);
 		$role_names = [];
 		$role_permissions = [];
 		foreach ($user->roles as $role) {
 			$role_names[] = $role->data['name'];
 			$temp = explode(',', $role->data['permission']);
 			$role_permissions = array_merge($role_permissions, $temp);
 		}
 		return [
 			'role_names' => $role_names,
 			'role_permissions' => $role_permissions
 		];
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
 	 * 写入登录用户权限信息
 	 * @param [type] &$login_info [description]
 	 */
 	static protected function setUserPer(&$login_info, $client_type = '')
    {
 		$user_type_map = [
 			1 		=> 'org',
 			2 		=> 'student',
 		];

 		$per_prefix   = $user_type_map[$login_info['user_type']];

 		$per_item_key = $per_prefix.'_per_item';

 		if($client_type != ''){
 			$per_item_key = $client_type.'_'.$per_item_key;
 		}


 		$all_pers = config($per_item_key);

 		$all_per_items  = self::getNavPers($all_pers);
        $user_per_items = [];
 		$user_navs = self::getUserNavs($all_pers,$user_per_items,$login_info);

 		$login_info['pers'] = $user_per_items;
 		$login_info['navs'] = $user_navs;

 	}


 	/**
 	 * 获得导航项目的权限
 	 * @param  [type] &$navs [description]
 	 * @return [type]        [description]
 	 */
 	static protected function getNavPers(&$navs)
    {
 		$pers = [];
        if (!empty($navs)) {
            foreach($navs as $k=>$nav){
                if(is_numeric($k)){
                    if(!empty($nav['uri'])){
                        array_push($pers,$nav['uri']);
                    }
                    if(isset($nav['sub']) && !empty($nav['sub'])){
                        $sub_pers = self::getnavPers($nav['sub']);

                        $pers = array_merge($pers,$sub_pers);
                    }
                }else{
                    $pers = array_merge($pers,self::getNavpers($nav));
                }
            }
        }
 		return array_unique($pers);
 	}


 	/**
 	 * 获得用户导航
 	 * @param  [type] &$navs [description]
 	 * @param  [type] &$pers [description]
     * @param  [name] [<description>] 
 	 * @return [type]        [description]
 	 */
 	static protected function getUserNavs(&$navs,&$pers,&$login_info)
    {
 		$user_navs = [];

        if (!empty($navs)) {
            foreach($navs as $k=>$nav){
                if(is_numeric($k)){
                    if(isset($nav['ismenu']) && !$nav['ismenu']){
                        continue;
                    }
                    if(isset($nav['need_user_field'])){
                        $user_fields_match = true;
                        foreach($nav['need_user_field'] as $uf=>$ufv){
                            if($ufv != $login_info[$uf]){
                                $user_fields_match = false;
                                break;
                            }
                        }
                        if(!$user_fields_match){
                            continue;
                        }
                    }
                    if(isset($nav['need_client_field'])){
                        $client = gvar('client');
                        $client_fields_match = true;
                        foreach($nav['need_client_field'] as $cf=>$cfv){
                            if($cfv != $client[$cf]){
                                $client_fields_match = false;
                                break;
                            }
                        }
                        if(!$client_fields_match){
                            continue;
                        }
                    }
                    if(in_array($nav['uri'],$pers)){
                        $nav_item = [
                            'text'=>$nav['text'],
                            'uri'	=> $nav['uri']
                        ];
                        if(isset($nav['class'])){
                            $nav_item['class'] = $nav['class'];
                        }

                        if(isset($nav['sub']) && !empty($nav['sub'])){
                            $nav_item['hidesub'] = isset($nav['hidesub'])?$nav['hidesub']:false;
                            $nav_item['sub'] = self::getUserNavs($nav['sub'],$pers,$login_info);
                        }
                        array_push($user_navs,$nav_item);
                    }
                }else{
                    $user_navs[$k] = self::getUserNavs($nav,$pers,$login_info);
                }

            }
        }
 		return $user_navs;
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

 	/**
 	 * 保存用户资料
 	 * @param  [type] &$input [description]
 	 * @param  [type] $action [description]
 	 * @return [type]         [description]
 	 */
 	public function saveProfile(&$input,$action)
    {
        unset($this['employee']);
 		if($action == 'changePwd'){
 			if(!isset($input['newpassword'])){
 				$this->error = '新密码不能为空!';
 				return false;
 			}
 			if(!isset($input['oldpassword'])){
 				$this->error = '旧密码不能为空!';
 				return false;
 			}

 			if(!$this->verifyPassword($input['oldpassword'])){
 				$this->error = '旧密码不正确!';
 				return false;
 			}

 			$result = $this->data('password',passwd_hash($input['newpassword'],$this->getData('salt')))
 			               ->allowField(['uid','password'])
 			               ->isUpdate(true)
 			               ->save();

 			if(false === $result){

 				return false;
 			}
 		}elseif($action == 'changeName'){
 			$this->data('name',$input['name']);

 			$result = $this->allowField(['uid','name'])->isUpdate(true)->save();

 		
 			if(false === $result ){
 				return false;
 			}
 		}elseif($action == 'changeAvatar'){
 			$this->data('avatar',$input['avatar']);

 			$result = $this->allowField(['uid','avatar'])->isUpdate(true)->save();

 			if(false === $result ){
 				return false;
 			}
 		} elseif ($action == 'changeSign') {
            $employee = $this->getAttr('employee');
            $profile = $employee['profile'];
            if (empty($profile)) {
                $employee->profile()->save(['sign' => $input['sign']]);
            } else {
                $profile->isUpdate(true)->save(['sign' => $input['sign']]);
            }
        } elseif ($action == 'changeBackgroundImg') {
            $employee = $this->getAttr('employee');
            $profile = $employee['profile'];
            if (empty($profile)) {
                $employee->profile()->save(['background_img' => $input['background_img']]);
            } else {
                $profile->isUpdate(true)->save(['background_img' => $input['background_img']]);
            }
        }

 		return true;
 	}


 	/**
 	 * 插入数据前的动作
 	 * @param  [type] &$model [description]
 	 * @return [type]         [description]
 	 */
 	protected static function before_insert(&$model)
    {
		$uid = gvar('uid');
		if(!$uid){
			$uid = 0;
		}
		$model->data['create_uid'] = $uid;
		return true;	
    }

    public function createEmployeeAccount($data)
    {
        $data['user_type'] = 1;
        $rule = [
            //'account|账号' => "require|unique:user,user_type=1&account={$data['account']}",
            //这条规则导致在admapi 下面调用的时候出错
            'password|密码' => 'require|length:6,20',
        ];
        $w_ex['account'] = $data['account'];
        $w_ex['user_type'] = 1;
        $ex = $this->where($w_ex)->find();
        if($ex){
            return $this->user_error('账号:'.$data['account'].'已经存在!');
        }

        $result = $this->validate($rule)->allowField(true)->save($data);
        if(!$result){
        	return false;
        }

        return $this->getLastInsID();
    }

    /**
     * 创建student记录后，在student模型事件after_create中根据字段first_tel和second_tel创建账号
     * @param Student $student
     */
    public function createStudentUserAfterCreateStudent(Student $student)
    {
        $this->startTrans();
        try {

            $tels = [];
            if (!empty($student['first_tel'])) {
                $tels['first'] = $student['first_tel'];
            }
            if (!empty($student['second_tel'])) {
                $tels['second'] = $student['second_tel'];
            }
            $user_list = [];
            foreach ($tels as $key => $tel) {
                $w = [];
                $w['account'] = $tel;
                $w['user_type'] = self::STUDENT_ACCOUNT;
                $exist_user = self::get($w);
                if ($exist_user) {
                    $user_list[] = $exist_user;
                    $remark = [];
                    $remark['og_id'] = $exist_user['og_id'];
                    $exist_user->students()->attach($student['sid'], $remark);
                    if ($key == 'first') {
                        $student->data('first_uid', $exist_user['uid']);
                        $student->data('first_openid', $exist_user['openid']);
                    } else {
                        $student->data('second_uid', $exist_user['uid']);
                        $student->data('second_openid', $exist_user['openid']);
                    }
                } else {
                    $data = [];
                    $data['account'] = $tel;
                    $data['password'] = substr($tel, -6, 6);
                    if (isset($student['og_id'])) {
                        $data['og_id'] = $student['og_id'];
                    }
                    if ($key == 'first') {
                        if (!empty($student['first_family_name'])) {
                            $data['name'] = $student['first_family_name'];
                        } elseif (isset($student['first_family_rel']) && $student['first_family_rel'] == 1) {
                            $data['name'] = $student['student_name'];
                        }
                    }
                    if ($key == 'second') {
                        if (!empty($student['second_family_name'])) {
                            $data['name'] = $student['second_family_name'];
                        } elseif (isset($student['second_family_rel']) && $student['second_family_rel'] == 1) {
                            $data['name'] = $student['student_name'];
                        }
                    }

                    $data['mobile'] = $tel;
                    $data['user_type'] = self::STUDENT_ACCOUNT;
                    $data['default_sid'] = $student['sid'];
                    $new_user = self::create($data);
                    $user_list[] = $new_user;
                    $remark = [];
                    if (isset($student['og_id'])) {
                        $remark['og_id'] = $student['og_id'];
                    }
                    $new_user->students()->attach($student['sid'], $remark);
                    if ($key == 'first') {
                        $student->data('first_uid', $new_user['uid']);
                    } else {
                        $student->data('second_uid', $new_user['uid']);
                    }
                }
            }
            $student->save();

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $user_list;
    }

    public function updateUser(User $user, $data)
    {
        $rs = $user->isUpdate(true)->allowField(true)->save($data);
        if($rs === false) exception('修改失败');

        if(!empty($user['user_type']) && $user['user_type'] == self::STUDENT_ACCOUNT) {
            $student = Student::get($user['default_sid']);
            $callback_user_data = array_merge($user->getData(), $student->getData());
            callback_queue_push('user_modify_callback_url', $callback_user_data);
        }

        return true;
    }

    public function resetpwd($password, $inform_dss = true)
    {
        $data['password']     = $password;

        $this->startTrans();
        try {
            $this->isUpdate(true)->allowField(['password', 'salt', 'ext_password'])->save($data);
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 注册
     * @param  [type] $post [description]
     * @return [type]       [description]
     */
    public function register($post)
    {
        $sub_domain = $this->getSubDomain();
        $conn = Db::connect('center_database');
        $client = $conn->name('client')->where('host', $sub_domain)->find();
        if(empty($client)) return $this->user_error('机构不存在');

        try {
            $this->startTrans();
            if (is_mobile($post['account'])) {
                $post['mobile'] = $post['account'];
                $post['is_mobile_bind'] = 1;
            }
            $result = $this->allowField(true)->save($post);
            if (!$result) return false;

            $rs = $this->addClientUser(
                ['cid' => $client['cid'], 'account' => $post['account'], 'uid' => $this->getAttr('uid')]
            );
            if ($rs === false) return $this->user_error($this->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        }

        $login = $this->login($post['account'],$post['password'], self::STUDENT_ACCOUNT);
        return $login;
    }

    //创建中心数据库客户对应用户
    public function addClientUser($data)
    {
        $conn = Db::connect('center_database');
        $client_user = $conn->name('client_user')->where('account', $data['account'])
            ->where('cid', $data['cid'])->find();
        if(empty($client_user)) {
            $rs = $conn->name('client_user')->insert($data);
            if($rs === false) return $this->user_error('添加失败');
        }

        return true;
    }

    //取得三级客户域名
    public function getSubDomain()
    {
        $request = request();
        $config_domain = config('ui.domain');
        $pos = strrpos($request->host(), $config_domain);
        if(!$pos) return '';

        $pre_domain = substr($request->host(), 0, $pos - 1);
        $pre_domain_arr = explode('.', $pre_domain);
        return array_pop($pre_domain_arr);
    }


    /**
     * 根据token 登录，由后台管理员生成登录token
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    static public function tokenLogin($token)
    {
        $cache_key = cache_key($token);
        $tk_cache_key = $cache_key;

        $login_student = cache($cache_key);
        if(!$login_student){
            self::$ERR = _('token_not_exsists');
            return false;
        }
        
        $sid = $login_student['sid'];
        $student = get_student_info($sid);

        if(empty($student)){
            self::$ERR = _('student_not_exists');
            return false;
        }

        // $user = get_user_info($student['first_uid']);
        $w['uid'] = $login_student['uid'];
        $user = self::where($w)->find();

        if(empty($user)){
            self::$ERR = _('user_not_exists');
            return false;
        }

        $login_info = $user->toArray();
        $login_expire = config('api.login_expire');
        $login_info['login_server_time'] = request()->time();
        $login_info['client']  = gvar('client');
        $login_info['expired'] = $login_expire;
        $login_info['istklogin'] = 1;
        $login_info['visitor'] = 0;

        $cache_key = cache_key($user->token);
        cache($cache_key,$login_info,$login_expire);

        $user->setLoginInfo($login_info);
        cache($tk_cache_key,null);
        return $user;
    }




}
