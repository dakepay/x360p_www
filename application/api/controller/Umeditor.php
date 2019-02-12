<?php

namespace app\api\controller;

use think\Request;
use think\config;
use util\umeditor\uploader;



/**
 * Class Open
 * @title 百度Ue编辑器后台统一接口
 * @url /
 * @desc  需要验证登录的公共接口
 * @version 1.0
 * @readme 
 */
class Umeditor extends Base
{
	public $noRest = true;


	public function get_list(Request $request){

		$json_file = ROOT_PATH.'config'.DS.'ueditor.json';

		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($json_file)), true);

		$action = input('get.action');

		if(empty($action)){
			$action = 'config';
		}

		$sub_func = 'action_'.$action;

		if(method_exists($this,$sub_func)){
			return $this->$sub_func($request,$CONFIG);
		}

		return $this->action_config($request,$CONFIG);
	}


	/**
	 * 获得配置文件
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function action_config(Request $request,$config){
		return $this->response($config,200,[]);
	}

	public function post(Request $request){
		$action = htmlspecialchars(input('get.action'));
		$json_file = ROOT_PATH.DS.'config'.DS.'ueditor.json';
		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($json_file)), true);

		if($action == 'catchimage'){
			return $this->action_catchimage($request,$CONFIG);
		}
		/* 上传配置 */
		$base64 = "upload";
		$config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = 'file';


		/* 生成上传实例对象并完成上传 */
		$up = new uploader($fieldName, $config, $base64);

		/**
		 * 得到上传文件所对应的各个参数,数组结构
		 * array(
		 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
		 *     "url" => "",            //返回的地址
		 *     "title" => "",          //新文件名
		 *     "original" => "",       //原始文件名
		 *     "type" => ""            //文件类型
		 *     "size" => "",           //文件大小
		 * )
		 */

		/* 返回数据 */
		$result = $up->getFileInfo();

		return $this->response($result,200,[]);
	}

}