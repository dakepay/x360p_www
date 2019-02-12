<?php
namespace app\mccapi\controller;

use Curl\Curl;
use think\Log;
use think\Request;
use Qiniu;

/**
 * Class Open
 * @title 公共接口
 * @url /
 * @desc  需要验证登录的公共接口
 * @version 1.0
 * @readme 
 */
class Upload extends Base
{
	public $restMethodList = 'post|get';

	public function _init(){
		parent::_init();
		defined('DATA_ROOT_PATH') || define('DATA_ROOT_PATH',ROOT_PATH.'public/data/');
	}

	public function post(Request $request){
		$input = input('post.');
		if(empty($_FILES)){
			return $this->sendError('没有上传的文件',400);
		}
		//文件的表单命名
		$file_key = isset($input['fkey'])?$input['fkey']:'file';
		return $this->file_upload($file_key);
	}

	/**
	 * 文件上传处理
	 * @param  [type] $fk [description]
	 * @return [type]     [description]
	 */
	protected function file_upload($fk){
		// 获取表单上传文件 例如上传了001.jpg
		
		$local_dir = DATA_ROOT_PATH.'uploads'. DS .'mcc';

		@mkdirss($local_dir);
	    $file = request()->file($fk);
	    $info = $file->move($local_dir);
	    if($info){
	        $file = $info->getInfo();
	        $this->storage_qiniu_save($file);
	        return $this->sendSuccess($file);
	    }else{
	        return $this->sendError($file->getError(),400);
	    }
	}

	/**
	 * 七牛云存储上传后处理
	 * @param  [type] &$file [description]
	 * @return [type]        [description]
	 */
	protected function storage_qiniu_save(&$file){
		$config = config('storage.qiniu');
		$config['prefix'] = 'mcc/';
		// 构建鉴权对象
		$auth = new Qiniu\Auth($config['access_key'], $config['secret_key']);
		// 要上传的空间
		$bucket = $config['bucket'];
		// 生成上传 Token
		$token = $auth->uploadToken($bucket);
		// 要上传文件的本地路径
		$filePath = $file['local_file'];
		// 上传到七牛后保存的文件名
		$key = $config['prefix'].$file['name'];

		// 初始化 UploadManager 对象并进行文件的上传。
		$uploadMgr = new Qiniu\Storage\UploadManager();

		// 调用 UploadManager 的 putFile 方法进行文件的上传。
		list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

		if ($err !== null) {
		    $file['file_url']    = $file[''];
		    $file['storage'] = 'local';
		} else {
		   	$file['file_url']  = $config['domain'].$key;
		   	$file['storage'] = 'qiniu';
		}

	}
}