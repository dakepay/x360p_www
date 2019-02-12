<?php

namespace app\server\handle;

use app\server\Handle;
use app\server\Users;
use think\Log;

class User extends Handle
{
	/**
	 * 用户上线
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function event_online($data)
    {
		$need_fields = ['token','client_type'];

		if(!$this->check_need_fields($data, $need_fields)){
			$this->sendEvent('error','parameter_error');
			return false;
		}
		$result = Users::userOnline($data['token'], $data['client_type'], $this->fd);
		if ($result == true) {
			$this->sendEvent('user:online', ['fd'=>$this->fd, 'uid'=>$result]);
		} else {
            throw new \Exception('invalid token');
        }
	}

	/**
	 * 创建业务会话
	 * 由WEB客户端创建
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function event_create_business_session($data)
    {
        Log::record('event_create_business_session');
		$need_fields = ['token','uid','bs_type'];
		if(!$this->check_need_fields($data,$need_fields)){
			$this->sendEvent('error','parameter_error');
			return false;
		}
        if (empty($data['base_url'])) {
            $data['base_url'] = 'http://qms.xiao360.com';
        }
		$result = Users::createBusinessSession($data['uid'], $data['bs_type'], $data['base_url'], $this->fd);

		return $result;

	}

	/**
	 * 完成业务会话
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function event_finish_business_session($data)
    {
		$need_fields = ['token', 'uid' , 'bs_type'];
		if (!$this->check_need_fields($data,$need_fields)) {
			$this->sendEvent('error','parameter_error');
			return false;
		}

		$result = Users::finishBusinessSession($data['uid'], $data['bs_type'], $this->fd);

		return $result;
	}

	/**
	 * 推送媒体资源
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function event_pushmedia($data)
    {
		$need_fields = ['key','uid','data'];

		if(!$this->check_need_fields($data,$need_fields)){
			$this->sendEvent('error','parameter_error');
			return false;
		}

		$result = Users::pushMedia($data['uid'], $data['data'],$this->fd);

		return $result;
	}
}