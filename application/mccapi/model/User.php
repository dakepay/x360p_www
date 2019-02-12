<?php
namespace app\mccapi\model;

class User extends Base
{
	/**
	 * 根据openid 获得 用户对象
	 * @param  [type] $openid [description]
	 * @return [type]         [description]
	 */
	public function getUserByOpenid($openid){
		$w['wxapp_open_id'] = $openid;

		$user = $this->where($w)->find();

		if($user){
			return $user;
		}

		$uid = $this->data($w)->save();

		if(!$uid){
			return $this->sql_add_error('user');
		}

		$user = $this->where($w)->find();

		return $user;
	}

	/**
	 * 更新微信资料
	 * @param  [type] $post [description]
	 * @return [type]       [description]
	 */
	public function updateWxProfile($post){
		$map_fields = [
			'nickName'	=> 'nickname',
			'avatarUrl'	=> 'avatar',
			'gender'	=> 'sex',
			'city'		=> 'city',
			'province'	=> 'province',
			'country'	=> 'country',
			'language'	=> 'language'
		];

		foreach($post as $k=>$v){
			if(isset($map_fields[$k])){
				$this->data($map_fields[$k],$v);
			}
		}

		$result = $this->isUpdate(true)->allowField(true)->save();

		if(false === $result){
			return $this->sql_save_error('user');
		}

		return $this;
	}

	/**
	 * 更新用户角色类型
	 * @param  [type] $user_type [description]
	 * @return [type]            [description]
	 */
	public function updateUserType($user_type){
		$user_type = intval($user_type);
		if($user_type > 2 || $user_type < 1){
			$user_type = 1;
		}
		if($this->user_type > 0 && $this->user_type < 3){
			$this->data('user_type',$this->user_type + $user_type);
		}else{
			$this->data('user_type',$user_type);
		}
		
		$result = $this->isUpdate(true)->allowField(true)->save();

		if(false === $result){
			return $this->sql_save_error('user');
		}

		return $this;
	}

	/**
	 * 更新Token信息
	 * @return [type] [description]
	 */
	public function updateToken(){
		if(!$this->token){
			return false;
		}
		$cache_key = cache_key($this->token);
    	cache($cache_key,$this->toArray(),$this->expired);
    	return $this;
	}
}