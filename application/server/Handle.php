<?php
namespace app\server;

use app\server\Users;


class Handle
{

	public $server;

	protected $fd;

	public function __construct($server, $fd)
    {
    	$this->server = $server;
    	$this->fd     = $fd;
    }

    /**
     * hanle入口
     * @param  [type] $event [description]
     * @param  [type] $data  [description]
     * @param  string $sk    安全码
     * @return [type]        [description]
     */
	public function handle($event, $data, $sk = '')
    {
		$func = 'event_'.$event;
        if (method_exists($this, $func)) {
            return call_user_func_array([$this,$func],[$data]);
        } else {
            return false;
        }

	}

	/**
	 * 检查必须制度按
	 * @param  [type] $data   [description]
	 * @param  [type] $fields [description]
	 * @return [type]         [description]
	 */
	protected function check_need_fields($data, $fields)
    {
		$result = true;
		foreach($fields as $f){
			if(!isset($data[$f])){
				$result = false;
				break;
			}
		}
		return $result;
	}


	/**
	 * 发送事件到客户端
	 * @param  [type] $event [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	protected function sendEvent($event, $data)
    {
		$json = json_encode([
			'event' =>$event,
			'data'	=> $data
		]);
		$this->server->push($this->fd, $json);
	}

}