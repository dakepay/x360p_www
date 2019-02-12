<?php

namespace app\api\controller;

use think\Request;
use think\config;
use util\ueditor\uploader;



/**
 * Class Open
 * @title 百度Ue编辑器后台统一接口
 * @url /
 * @desc  需要验证登录的公共接口
 * @version 1.0
 * @readme 
 */
class Ueditor extends Base
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


	public function action_listimage(Request $request,$CONFIG){
		$allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
        $path = $CONFIG['imageManagerListPath'];

       	return $this->listfile($allowFiles,$listSize,$path);
	}


	public function action_listfile(Request $request,$CONFIG){
		$allowFiles = $CONFIG['fileManagerAllowFiles'];
        $listSize = $CONFIG['fileManagerListSize'];
        $path = $CONFIG['fileManagerListPath'];

        return $this->listfile($allowFiles,$listSize,$path);
	}

	public function action_catchimage(Request $request,$CONFIG){
		/* 上传配置 */
		$config = array(
		    "pathFormat" => $CONFIG['catcherPathFormat'],
		    "maxSize" => $CONFIG['catcherMaxSize'],
		    "allowFiles" => $CONFIG['catcherAllowFiles'],
		    "oriName" => "remote.png"
		);
		$fieldName = $CONFIG['catcherFieldName'];

		/* 抓取远程图片 */
		$list = array();
		if (isset($_POST[$fieldName])) {
		    $source = $_POST[$fieldName];
		} else {
		    $source = $_GET[$fieldName];
		}
		foreach ($source as $imgUrl) {
		    $item = new uploader($imgUrl, $config, "remote");
		    $info = $item->getFileInfo();
		    array_push($list, [
		        "state" => $info["state"],
		        "url" => $info["url"],
		        "size" => $info["size"],
		        "title" => htmlspecialchars($info["title"]),
		        "original" => htmlspecialchars($info["original"]),
		        "source" => htmlspecialchars($imgUrl)
		    ]);
		}

		/* 返回抓取数据 */
		return $this->response([
		    'state'=> count($list) ? 'SUCCESS':'ERROR',
		    'list'=> $list
		],200,[]);
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
		switch ($action) {
		    case 'uploadimage':
		        $config = array(
		            "pathFormat" => $CONFIG['imagePathFormat'],
		            "maxSize" => $CONFIG['imageMaxSize'],
		            "allowFiles" => $CONFIG['imageAllowFiles']
		        );
		        $fieldName = $CONFIG['imageFieldName'];
		        break;
		    case 'uploadscrawl':
		        $config = array(
		            "pathFormat" => $CONFIG['scrawlPathFormat'],
		            "maxSize" => $CONFIG['scrawlMaxSize'],
		            "allowFiles" => $CONFIG['scrawlAllowFiles'],
		            "oriName" => "scrawl.png"
		        );
		        $fieldName = $CONFIG['scrawlFieldName'];
		        $base64 = "base64";
		        break;
		    case 'uploadvideo':
		        $config = array(
		            "pathFormat" => $CONFIG['videoPathFormat'],
		            "maxSize" => $CONFIG['videoMaxSize'],
		            "allowFiles" => $CONFIG['videoAllowFiles']
		        );
		        $fieldName = $CONFIG['videoFieldName'];
		        break;
		    case 'uploadfile':
		    default:
		        $config = array(
		            "pathFormat" => $CONFIG['filePathFormat'],
		            "maxSize" => $CONFIG['fileMaxSize'],
		            "allowFiles" => $CONFIG['fileAllowFiles']
		        );
		        $fieldName = $CONFIG['fileFieldName'];
		        break;
		}


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


	protected function listfile($allowFiles,$listSize,$path){
		$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
		/* 获取参数 */
		$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
		$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
		$end = $start + $size;

		/* 获取文件列表 */
		$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
		$files = [];
		$files = $this->getfiles($path, $allowFiles,$files);
		
		if (!count($files)) {
		    return $this->response([
		        "state" => "no match file",
		        "list" => [],
		        "start" => $start,
		        "total" => count($files)
		    ],200,[]);
		}

		/* 获取指定范围的列表 */
		$len = count($files);
		for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
		    $list[] = $files[$i];
		}
		
		/* 返回数据 */
		$result = [
		    "state" => "SUCCESS",
		    "list" => $list,
		    "start" => $start,
		    "total" => count($files)
		];



		return $this->response($result,200,[]);
	}


	protected function getfiles($config_path, $allowFiles, &$files = array()){
		//$path = ROOT_PATH.'public'.DS.'data'.$config_path;
		$path = $config_path;
		

	    if (!is_dir($path)) return null;
	    if(substr($path, strlen($path) - 1) != '/') $path .= '/';
	    $handle = opendir($path);
	  
	    while (false !== ($file = readdir($handle))) {

	        if ($file != '.' && $file != '..') {
	            $path2 = $path . $file;
	           
	            if (is_dir($path2)) {
	                $this->getfiles($path2, $allowFiles, $files);
	            } else {
	                if (preg_match("/\.(".$allowFiles.")$/i", $file)) {
	                    $files[] = array(
	                        'url'=> $this->storage_url(substr($path2, strlen($_SERVER['DOCUMENT_ROOT']))),
	                        'mtime'=> filemtime($path2)
	                    );
	                }
	            }
	        }
	    }
	    return $files;
	}


	protected function storage_url($path){
		$storage = config('storage.engine');
		$storage_config = config('storage.'.$storage);

		if($storage == 'local'){
			return $path;
		}

		return $storage_config['domain'].$storage_config['prefix'].substr($path,1);
	}

}