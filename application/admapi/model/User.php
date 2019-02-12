<?php

namespace app\admapi\model;

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

    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $insert = ['salt'];

    protected $append = ['token'];


    //登录信息
    protected $login_info = null;

    public function employee()
    {
        return $this->hasOne('Employee','uid','uid','LEFT');
    }

    public function getTokenAttr($value, &$data)
    {
        if(isset($data['token'])){
            return $data['token'];
        }
        $option = [
            'admin',
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

    public function userRole()
    {
        //return $this->hasMany('UserRole', 'uid', 'uid');
        return $this->belongsToMany('Role', 'user_role', 'rid', 'uid');
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

        $user = self::where($w)->find();

        if(!$user){
            if(is_mobile($account)){
				$w = ['mobile'=>$account, 'is_mobile_bind'=>1, 'user_type' => $user_type];
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

        //更新登录信息
        $user->updateLastLoginInfo();
        // $this->login_info 是一个数组
        $login_info = $user->toArray();

        //获得权限列表和菜单列表 pers navs
		self::setUserPer($login_info, $client_type);

		$login_expire = config('api.login_expire');
		$login_info['login_server_time'] = request()->time();
		$login_info['expired'] = $login_expire;
		$login_info['employee'] = Employee::get(['uid' => $user->uid]);

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
 	 * 写入登录用户权限信息
 	 * @param [type] &$login_info [description]
 	 */
 	static protected function setUserPer(&$login_info, $client_type = '')
    {
 		
 		$per_prefix   = 'admin';

 		$per_item_key = $per_prefix.'_per_item';

 		if($client_type != ''){
 			$per_item_key = $client_type.'_'.$per_item_key;
 		}


 		$all_pers = config($per_item_key);

 		$all_per_items  = self::getNavPers($all_pers);

 		$user_per_items = self::getUserSavedPers($login_info['uid'], $client_type);
 		
 		if($login_info['is_admin'] == 1){
 			$user_per_items = $all_per_items;
 		}


 		$user_navs = self::getUserNavs($all_pers,$user_per_items);

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
 	 * @return [type]        [description]
 	 */
 	static protected function getUserNavs(&$navs,&$pers)
    {
 		$user_navs = [];

        if (!empty($navs)) {
            foreach($navs as $k=>$nav){
                if(is_numeric($k)){
                    if(isset($nav['ismenu']) && !$nav['ismenu']){
                        continue;
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
                            $nav_item['sub'] = self::getUserNavs($nav['sub'],$pers);
                        }
                        array_push($user_navs,$nav_item);
                    }
                }else{
                    $user_navs[$k] = self::getUserNavs($nav,$pers);
                }

            }
        }
 		return $user_navs;
 	}


 	/**
 	 * 获得用户保存的权限
 	 * @param  [type] $uid [description]
 	 * @return [type]      [description]
 	 */
 	static protected function getUserSavedPers($uid, $client_type = '')
    {
 		$arr_rids = [];
 		$per_items = [];
 		$pers_field = 'pers';
        if (!empty($client_type)) {
            $pers_field = $client_type . '_' . $pers_field;
        }
 		
 		$w['uid'] = $uid;
 		$user_role_list = db('user_role')->where($w)->select();
 		if($user_role_list){
 			foreach($user_role_list as $r){
 				array_push($arr_rids,$r['rid']);
 			}
 		}
 		
 		if(!empty($arr_rids)){
 			$w_role_per['rid'] = ['in',$arr_rids];
 			$role_per_list = db('role')->where($w_role_per)->select();
 			if($role_per_list){
 				foreach($role_per_list as $p){
 					$items = explode(',',$p[$pers_field]);
 					$per_items = array_merge($per_items,$items);
 				}
 			}
 		}
 		return $per_items;
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
        }

        return true;
    }

 	public function addOneUser($data)
    {
        $rs = $this->validateData($data, 'User.post');
        if($rs !== true) return $this->user_error($this->getErrorMsg());

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return $this->user_error("开通账户失败");

        $uid = $this->getAttr('uid');
        return $uid;
    }

    //修改账户信息
    public function updateUser(User $user, $data)
    {
        $old_rids = $user->userRole()->alias('role')->column('role.rid');
        $old_rids = $old_rids ? $old_rids : [];
        $new_rids = isset($data['rids']) ? $data['rids'] : $old_rids;

        $update_user_fields  = ['account','mobile','email','name','sex','avatar','status','is_admin'];

        $del_rids = array_diff($old_rids,$new_rids);
        $add_rids = array_diff($new_rids,$old_rids);

        $this->startTrans();
        try {
            //更新员工信息
            $rs = $user->allowField($update_user_fields)->isUpdate(true)->save($data);
            if($rs === false) exception('更新失败');

            //添加角色员工表
            if(!empty($add_rids)){
                $add_rids = array_reduce($add_rids, function($arr,$val){
                    $arr[]['rid'] = $val;
                    return $arr;
                });
                $rs = $user->userRole()->saveAll($add_rids);
                if($rs === false) exception('增加角色失败');
            }

            //删除角色员工表
            if(!empty($del_rids)){
                $rs = $user->userRole()->where('rid', 'in', $del_rids)->delete();
                if($rs === false) exception('删除角色失败');
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //删除一个帐号
    public function delOneUser($user)
    {
        $this->startTrans();
        try {
            $rs = (new UserRole())->where('uid', $user->uid)->delete();
            if($rs === false) exception('删除帐号权限失败');

            $rs = $user->delete();
            if($rs === false) exception('删除帐号失败');

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //帐号添加角色
    public function addRoles($uid, $rids)
    {
        $this->startTrans();
        try {
            $m_ur = new UserRole();
            foreach ($rids as $rid) {
                $record = $m_ur->where('uid', $uid)->where('rid', $rid)->find();
                if (!empty($record)) continue;

                $rs = $m_ur->save(['rid' => $rid, 'uid' => $uid]);
                if ($rs === false) exception("帐号添加角色失败");
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //帐号批量增加权限
    public function addBatchRole($rid, $uids)
    {
        $this->startTrans();
        try {
            foreach ($uids as $uid) {

                $user = $this->find($uid);
                if(empty($user)) continue;

                $m_ur = new UserRole();
                $is_exist = (new UserRole())->where('rid', $rid)->where('uid', $uid)->find();
                if(!empty($is_exist)) continue;

                $rs = $m_ur->allowField(true)->isUpdate(false)->save(['rid' => $rid, 'uid' => $uid]);
                if($rs === false) exception('帐号增加角色失败');
            }
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    /**
     * 账号状态
     */
    public function toggleAccountStatus($uid, $status) {
        $rs = $this->where('uid', $uid)->update(['status' => $status]);
        if($rs === false) return $this->user_error('切换帐号状态失败');

        return true;
    }

}
