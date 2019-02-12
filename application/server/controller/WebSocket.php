<?php
namespace app\server\controller;


use think\Log;
use think\swoole\Server;

use app\server\EventHandle;
use app\server\Users;

class WebSocket extends Server
{
	protected $serverType = 'socket';
	protected $port   = 8899;
	protected $option = [ 
		'worker_num' => 1,
		'daemonize'	 => false,
		'backlog'	 => 128
	];
	protected $redis;

	protected $redis_host = '127.0.0.1';

	protected $redis_port = 6379;

    public function __construct()
    {
        $this->port = config('ws_port');
        $this->redis = new \Redis();
        $this->redis->pconnect($this->redis_host, $this->redis_port);
        $this->delete_expired_redis_keys();
        parent::__construct();

    }

    protected function init()
    {
    	Users::setServer($this->swoole);
    	Users::setRedis($this->redis);
    }

    /**
     * 当websocket进程重启的时候把redis中与websocket相关的数据删除
     */
    protected function delete_expired_redis_keys()
    {
        $remove_keys = $this->redis->keys("ws:client:*");
        Log::record($remove_keys, 'debug');
        array_push($remove_keys, 'ws:fd_user_hash');
        array_push($remove_keys, 'ws:identified_fd_set');
        if (!empty($remove_keys)) {
            $this->redis->delete($remove_keys);
        }
    }

//	$eventList = ['Open', 'Message', 'Close', 'HandShake'];

    /**
     * 当WebSocket客户端与服务器建立连接并完成握手后会回调此函数。
     *
     * function onOpen(swoole_websocket_server $svr, swoole_http_request $request);
     * $request 是一个Http请求对象，包含了客户端发来的握手请求信息
     * onOpen事件函数中可以调用push向客户端发送数据或者调用close关闭连接
     * onOpen事件回调是可选的
     *
     * @param $server
     * @param $request
     */
	public function onOpen($server, $request)
	{
		Log::record("server: handshake success with fd{$request->fd}", 'debug');
		Log::record($request, 'debug');
		$this->sendEvent($request->fd,'connected', '已连接......');
	}

    /**
     * 当服务器收到来自客户端的数据帧时会回调此函数。
     *
     * function onMessage(swoole_server $server, swoole_websocket_frame $frame)
     * $frame 是 swoole_websocket_frame对象，包含了客户端发来的数据帧信息
     * onMessage回调必须被设置，未设置服务器将无法启动,客户端发送的ping帧不会触发onMessage，底层会自动回复pong包
     * @param $server
     * @param $frame
     */
	public function onMessage($server, $frame)
	{
		Log::record("receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}", 'debug');
    	$data = json_decode($frame->data, true);
    	if(!$data || !isset($data['event'])){
    	    //不符合格式要求的直接关闭连接
            $this->sendEvent($frame->fd, 'error', ['msg' => 'invalid format']);
    		$server->close($frame->fd, true);
    	}

    	$event_name = $data['event'];
        if(($pos = strpos($event_name, ':')) !== false){
            $cls = ucwords(substr($event_name, 0, $pos));
            $event = substr($event_name, $pos+1);
        }else{
            $cls = 'User';
            $event = $event_name;
        }

        if ($event !== 'online') {
            $identified_fd_set_key = "ws:identified_fd_set";
            $is_identified = $this->redis->sIsMember($identified_fd_set_key, $frame->fd);
            if (!$is_identified) {
                $this->sendEvent($frame->fd, 'error', ['msg' => 'auth required']);
                $server->close($frame->fd, true);
                return;
            }
        }

        $sk = '';
    	if(isset($data['sk'])){
    		$sk = $data['sk'];
    	}

    	if (!in_array($cls, ['System', 'User'])) {
            $this->sendEvent($frame->fd, 'error', ['msg' => 'event not exists']);
        }

    	$class = 'app\\server\\handle\\'.$cls;

    	try {
    		$eh = new $class($server, $frame->fd);
    		$result = $eh->handle($event, $data['data'], $sk);
            if ($result == false) {

            }
    	} catch (\Exception $e) {
    		Log::record($e->getMessage());
            $this->sendEvent($frame->fd, 'exception', ['msg' => $e->getMessage()]);
    	}
	}

    /**
     * 关闭连接
     * @param $server
     * @param $fd
     * @param $from_id
     */
	public function onClose($server, $fd)
	{
        Log::record('WebSocket->onClose', 'debug');
		Log::record("client {$fd} closed", 'debug');
		Users::userOffline($fd);
	}

    /**
     * WebSocket建立连接后进行握手。WebSocket服务器已经内置了handshake，如果用户希望自己进行握手处理，可以设置onHandShake事件回调函数。
     *
     * function onHandShake(swoole_http_request $request, swoole_http_response $response);
     * onHandShake事件回调是可选的
     * 设置onHandShake回调函数后不会再触发onOpen事件，需要应用代码自行处理
     * onHandShake函数必须返回true表示握手成功，返回其他值表示握手失败
     * 内置的握手协议为Sec-WebSocket-Version: 13，低版本浏览器需要自行实现握手
     *
     * @param $server
     * @param $response
     */
//	public function onHandShake($server,$response)
//	{
//
//	}

    /**
     * @param $fd
     * @param $event
     * @param $data
     */
	public function sendEvent($fd, $event, $data){
		$json = json_encode([
			'event'=>$event,
			'data'=>$data
		]);
		$this->swoole->push($fd,$json);
	}
}