<?php

namespace app\server\handle;

use app\server\Handle;
use app\server\Users;
use think\Log;

class System extends Handle
{
	/**
     * hanle入口
     * @param  [type] $event [description]
     * @param  [type] $data  [description]
     * @param  string $sk    安全码
     * @return [type]        [description]
     */
	public function handle($event, $data, $sk = '')
    {
		$cfg_sk = config('websocket.sk');

		if($cfg_sk != $sk){
			echo 'seurity key is invalid!';
			return false;
		}

		$func = 'event_'.$event;
		call_user_func_array([$this,$func],[$data]);
	}

	/**
	 * 系统推送
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	protected function event_push($data)
    {
        Log::record($data, 'debug');
		$result = Users::pushSystemMsg($data['cid'], $data['uid'], $data['msg']);
		return $result;
	}

	/**
	 * 转移状态
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
    protected function event_bs_status($data)
    {
        Log::record('event_bs_status', 'debug');
        Log::record($data, 'debug');
    	$msg = isset($data['msg']) ? $data['msg'] : '';
        $result = Users::updateBusinessSession($data['uid'], $data['bs_type'], $data['status'], $msg, $data['cid']);
        return $result;
    }

    /**
     * 转移完成
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function event_bs_success($data)
    {
        Log::record('event_bs_success', 'debug');
        Log::record($data, 'debug');
        $result = Users::updateBusinessSession($data['uid'], $data['bs_type'],100, $data['data'], $data['cid']);
        return $result;
    }

}