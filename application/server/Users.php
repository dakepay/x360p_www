<?php

namespace app\server;
use app\common\notification\TransferMedia;
use think\Cache;
use app\api\model\User as UserModel;
use think\Log;

class Users
{
	//用户列表
	protected static $users = [];
	//fd_uid 列表
	protected static $fd_uids = [];

	protected static $server;

	protected static $redis;


	public static function setServer($server)
    {
		self::$server = $server;
	}

	public static function setRedis($redis)
    {
		self::$redis = $redis;
	}

	/**
	 * 用户上线
	 * @param  [type] $token       [description]
	 * @param  [type] $fd          [description]
	 * @param  [type] $client_type [description]
	 * @return [type]              [description]
	 */
	public static function userOnline($token, $client_type, $fd)
    {
	    $cache_key = cache_key($token);
	    $redis = self::$redis;
        $login_info = $redis->get($cache_key);
        if (!$login_info) {
            return false;
        }
        $user_info = json_decode($login_info, true);
        unset($user_info['pers'], $user_info['navs'], $user_info['employee']);

        $client_info = $user_info['client'];
        $redis_client_key = "ws:client:{$client_info['cid']}:user_hash";
        $redis_client_hash_key = "user:{$user_info['uid']}";

        $new_terminal = [
            'uid'	=> $user_info['uid'],
            'fd'	=> $fd,
            'client_type'	=> $client_type,
            'online_time'	=> time()
        ];

        /*判断该客户在redis中的web_socket关联的key是否存在*/
        if (!$redis->exists($redis_client_key)) {
            $user_info['terminals'][$fd] = $new_terminal;
            $user_info['business_session'] = [];
            $redis->hSet($redis_client_key, $redis_client_hash_key, json_encode($user_info));
        } else {

            /*当前用户是否在hash结构中, 如果不在里面就加入*/
            if (!$redis->hExists($redis_client_key, $redis_client_hash_key)) {
                $user_info['terminals'][$fd] = $new_terminal;
                $user_info['business_session'] = [];
                $redis->hSet($redis_client_key, $redis_client_hash_key, json_encode($user_info));
            } else {
                $redis_user_info = $redis->hGet($redis_client_key, $redis_client_hash_key);
                $redis_user_info = json_decode($redis_user_info, true);
                $redis_user_info['terminals'][$fd] = $new_terminal;
                $redis->hSet($redis_client_key, $redis_client_hash_key, json_encode($redis_user_info));
            }
        }

        /*fd与cid_uid的对应*/
        $temp['cid'] = $client_info['cid'];
        $temp['uid'] = $user_info['uid'];
        $fd_user_key = 'ws:fd_user_hash';
        $redis->hSet($fd_user_key, 'fd:' . $fd, json_encode($temp));

        /*客户的fd集合*/
        $redis_client_fd_key = "ws:client:{$client_info['cid']}:fd_set";
        $redis->sAdd($redis_client_fd_key, $fd);

        /*把fd加入到已认证的fd列表中*/
        $identified_fd_set_key = "ws:identified_fd_set";
        $redis->sAdd($identified_fd_set_key, $fd);

        return true;
	}

	/**
	 * 用户下线
	 * @param  [type] $fd [description]
	 * @return [type]     [description]
	 */
	public static function userOffline($fd)
    {
        Log::record('userOffline', 'debug');
		$redis = self::$redis;
		$data = $redis->hGet('ws:fd_user_hash', 'fd:' . $fd);
        if (!$data) {
            //todo 异常
            return true;
        }
        $data = json_decode($data, true);
        Log::record($data, 'debug');
        $redis_client_key = "ws:client:{$data['cid']}:user_hash";
        $redis_client_hash_key = "user:{$data['uid']}";
        $redis_user_info = $redis->hGet($redis_client_key, $redis_client_hash_key);
        if (!$redis_user_info) {
            //todo 异常
            return true;
        }

        $redis_user_info = json_decode($redis_user_info, true);
        unset($redis_user_info['terminals'][$fd]);

        if (empty($redis_user_info['terminals'])) {
            $redis->hDel($redis_client_key, $redis_client_hash_key);
        } else {
            $redis->hSet($redis_client_key, $redis_client_hash_key, json_encode($redis_user_info));
        }
        $redis->hDel('ws:fd_user_hash', 'fd:' . $fd);

        /*删除客户fd集合中的当前fd*/
        $redis_client_fd_key = "ws:client:{$redis_user_info['client']['cid']}:fd_set";
        $redis->sRemove($redis_client_fd_key, $fd);

        /*删除已认证的fd集合中的当前fd*/
        $identified_fd_set_key = "ws:identified_fd_set";
        $redis->sRemove($identified_fd_set_key, $fd);
        return true;
	}


	/**
	 * 创建业务会话session
	 * @param  [type] $uid     [description]
	 * @param  [type] $bs_type [description]
	 * @param  [type] $fd      [description]
	 * @return [type]          [description]
	 */
	public static function createBusinessSession($uid, $bs_type, $base_url, $fd)
    {
        $redis   = self::$redis;
        $fd_user = $redis->hGet('ws:fd_user_hash', 'fd:' . $fd);
        if (!$fd_user) {
            //todo 异常
            return false;
        }
        $fd_user = json_decode($fd_user, true);
        $redis_client_key      = "ws:client:{$fd_user['cid']}:user_hash";
        $redis_client_hash_key = "user:{$fd_user['uid']}";
        $redis_user_info       = $redis->hGet($redis_client_key, $redis_client_hash_key);
        if (!$redis_user_info) {
            //todo 异常
            return false;
        }
        $redis_user_info = json_decode($redis_user_info, true);

        $cache_key = '';
        if ($bs_type == 'push_media') {
            if (empty($redis_user_info['openid'])) {
                //todo 异常
                return false;
            }
            $cache_key = "ws:client:{$fd_user['cid']}:{$redis_user_info['openid']}:{$bs_type}";
        }

        $bs_list       = $redis_user_info['business_session'];
        $terminal_list = $redis_user_info['terminals'];
        if (isset($bs_list[$bs_type])) {
            if ($bs_list[$bs_type]['client_type'] == $terminal_list[$fd]['client_type']) {

                $bs_list[$bs_type]['fd'] = $fd;
                $bs_list[$bs_type]['create_time'] = time();
                if($bs_list[$bs_type]['status'] > 0){
                    //补发
                    self::push($fd,'push_media:status', $bs_list[$bs_type]);
                }
            }
        } else {
            $new_business_session = [
                'fd'	        => $fd,
                'user_type'		=> $redis_user_info['user_type'],
                'client_type'	=> $terminal_list[$fd]['client_type'],
                'base_url'		=> $base_url,
                'msg'			=> '',
                'data'	        => [],
                'create_time'	=> time(),
                'status'		=> 0
            ];
            $bs_list[$bs_type] = $new_business_session;
        }
        $redis_user_info['business_session'] = $bs_list;
        $redis->hSet($redis_client_key, $redis_client_hash_key, json_encode($redis_user_info));
        if($cache_key != ''){
            $temp = [
                'bs_type' => $bs_type,
                'cid'     => $fd_user['cid'],
                'uid'     => $fd_user['uid'],
            ];
            Cache::set($cache_key, array_merge($bs_list[$bs_type], $temp));
        }
        if ($bs_list[$bs_type]['status'] == 0) {
//            $data['uid']    = $fd_user['uid'];
//            $data['cid']    = $fd_user['cid'];
//            $data['bid']    = $fd_user['bid']; //todo
//            $data['openid'] = $redis_user_info['openid'];


//            $redis_user_info['bid'] = $bid //todo
            $notification   = new TransferMedia($redis_user_info);
            $notification->run();
        }
        return true;
	}

	public static function updateBusinessSession($uid, $bs_type, $status = 1, $data = [], $cid)
    {
        $redis = self::$redis;
        $redis_client_key = "ws:client:{$cid}:user_hash";
        $redis_client_hash_key = "user:{$uid}";
        $redis_user_info       = $redis->hGet($redis_client_key, $redis_client_hash_key);
        if (!$redis_user_info) {
            //todo 异常
            return false;
        }
        $redis_user_info = json_decode($redis_user_info, true);
        $bs_list = $redis_user_info['business_session'];
        if (!isset($bs_list[$bs_type])) {
            //todo
            return false;
        }
        $bs_list[$bs_type]['status'] = $status;
        if ($status == 100) {
            $bs_list[$bs_type]['data'] = $data;
        } else {
            $bs_list[$bs_type]['msg']  = $data;
        }
        $redis_user_info['business_session'] = $bs_list;
        $redis->hSet($redis_client_key, $redis_client_hash_key, json_encode($redis_user_info));
        self::push($bs_list[$bs_type]['fd'],'push_media:status',$bs_list[$bs_type]);

        /*同时修改redis中的会话状态*/
        $cache_key = "ws:client:{$cid}:{$redis_user_info['openid']}:{$bs_type}";
        $cache_value = Cache::get($cache_key);
        $cache_value['status'] = $status;
        Cache::set($cache_key, $cache_value);
	}

	/**
	 * 结束业务会话session
	 * @param  [type] $uid     [description]
	 * @param  [type] $bs_type [description]
	 * @param  [type] $fd      [description]
	 * @return [type]          [description]
	 */
	public static function finishBusinessSession($uid, $bs_type, $fd)
    {
        $redis_user_info = self::getUserInfoByFd($fd);
        if (!$redis_user_info) {
            //todo
            return false;
        }
        unset($redis_user_info['business_session'][$bs_type]);

        $redis_client_key      = "ws:client:{$redis_user_info['client']['cid']}:user_lists";
        $redis_client_hash_key = "user:{$redis_user_info['uid']}";
        $redis = self::$redis;
        $redis->hSet($redis_client_key, $redis_client_hash_key, json_encode($redis_user_info));

        if ($bs_type == 'push_media') {
            $cache_key = "ws:client:{$redis_user_info['client']['cid']}:{$redis_user_info['openid']}:{$bs_type}";
            Cache::rm($cache_key);
        }
        return true;
	}

	/**
	 * 推送系统消息
	 * @param  [type] $cid [description]
     * @param  [type] $uid [description]
	 * @param  [type] $msg [description]
	 * @return [type]      [description]
	 */
	public static function pushSystemMsg($cid, $uid, $msg)
    {
        $redis = self::$redis;
        $redis_client_key      = "ws:client:{$cid}:user_hash";
        $redis_client_hash_key = "user:{$uid}";
        $redis_user_info       = $redis->hGet($redis_client_key, $redis_client_hash_key);
        if (!$redis_user_info) {
            //todo 异常
            return false;
        }
        $redis_user_info = json_decode($redis_user_info, true);

        $json = json_encode([
            'event' => 'system:msg',
            'data'	=> [
                'msg' => $msg
            ]
        ]);

        foreach ($redis_user_info['terminals'] as $terminal) {
            self::$server->push($terminal['fd'], $json);
        }
        return true;
	}

	/**
	 * 获得用户
	 * @param  [type] $cid [description]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	protected static function getUser($cid, $uid)
    {
        $redis_client_key      = "ws:client:{$cid}:user_hash";
        $redis_client_hash_key = "user:{$uid}";
        $redis_user_info       = self::$redis->hGet($redis_client_key, $redis_client_hash_key);
        if (!$redis_user_info) {
            //todo 异常
            return false;
        }
        $redis_user_info = json_decode($redis_user_info, true);
        return $redis_user_info;
	}

	/**
	 * 推送消息给指定socket
	 * @param  [type] $fd   [description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	protected static function push($fd, $event, $data)
    {
		$push_data['event'] = $event;
		$push_data['data']  = $data;
		self::$server->push($fd, json_encode($push_data));
	}


	protected function rePushBsResult($uid, $bs_type, $data, $fd)
    {
		switch($bs_type){
			case 'push_media':
				return self::pushMedia($uid, $data, $fd);
				break;
		}

		return false;
	}

    protected static function getUserInfoByFd($fd)
    {
        $redis   = self::$redis;
        $fd_user = $redis->hGet('ws:fd_user_hash', 'fd:' . $fd);
        if (!$fd_user) {
            //todo 异常
            return false;
        }
        $fd_user = json_decode($fd_user, true);
        $redis_client_key      = "ws:client:{$fd_user['cid']}:user_hash";
        $redis_client_hash_key = "user:{$fd_user['uid']}";
        $redis_user_info       = $redis->hGet($redis_client_key, $redis_client_hash_key);
        if (!$redis_user_info) {
            //todo 异常
            return false;
        }
        $redis_user_info = json_decode($redis_user_info, true);
        return $redis_user_info;
    }

    protected function get_client_key($cid)
    {
        if (empty($cid)) {
            throw new \Exception('client_id is empty!');
        }
        return "ws:client:{$cid}:user_hash";
    }

    protected function get_client_hash_key($uid)
    {
        if (empty($uid)) {
            throw new \Exception('uid is empty!');
        }
        return "user:{$uid}";
    }
}