<?php
namespace app\api\controller;

use Curl\Curl;
use think\Log;
use think\Request;
use app\api\model\User;
use app\api\model\File;
use Qiniu;
use util\Image;

define('b64key','_b64file');
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

	protected $storage_config = [];
	protected $storage = 'local';
	protected $allowMethod = ['post'];
	protected $res_rel_base_dir = '';			//文件实际存储目录
	protected $res_www_base_dir = '';			//文件WWW可以访问目录
	protected $res_rel_file_path = '';			//文件实际存储路径
	protected $res_www_file_url  = '';			//文件WWW可以访问URL
	protected $save_file_name = '';				//保存文件名

	protected $allow_file_ext = ['ppt','pptx','doc','docx','xls','xlsx','jpg','png','gif','jpeg','mp3','mp4',
        'zip','rar','doc','pdf','7z','mov','MOV','MP4'];
	protected $img_file_ext   = ['jpg','png','gif','jpeg','JPG','PNG'];


	public function _init(){
		parent::_init();
		$this->storage = user_config('storage.engine');
		$this->storage_config = user_config('storage.'.$this->storage);
		defined('DATA_ROOT_PATH') || define('DATA_ROOT_PATH',ROOT_PATH.'public/data/');
	}

    public function get_list(Request $request){
    	$get = $request->get();
    	if(isset($get['storage']) && $get['storage'] == 'alioss'){
    		return $this->get_list_alioss();
    	}

        $storage = config('storage.engine');
        $config  = storage_config($storage);
        $config = $config['qiniu'];
        $auth = new Qiniu\Auth($config['access_key'], $config['secret_key']);

        // 要上传的空间
        $bucket = $config['bucket'];
        $domain = $config['domain'];
        $prefix = $config['prefix'];
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        return json(['uptoken'=>$token,'domain' => $domain,'prefix'=>$prefix]);
    }

    public function get_list_alioss()
    {
    	$config = storage_config('alioss');
    	// print_r($config);exit;
    	$id   = $config['access_id'];
    	$key  = $config['access_key'];
    	$host = $config['host'];
    	$bucket = $config['bucket'];
        $prefix = $config['prefix'];


    	$now = time();
    	$expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
    	$end = $now + $expire;
        $expiration = gmt_iso8601($end);

        $dir = '';
        //最大文件大小.用户可以自己设置
	    $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
	    $conditions[] = $condition; 

	    //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
	    $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
	    $conditions[] = $start; 


	    $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
	    // echo json_encode($arr);exit;
	    //return;
	    $policy = json_encode($arr);
	    $base64_policy = base64_encode($policy);
	    $string_to_sign = $base64_policy;
	    $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

	    $response = array();
	    $response['accessid'] = $id;
	    $response['host'] = $host;
	    $response['bucket'] = $bucket;
	    $response['prefix'] = $prefix;
	    $response['policy'] = $base64_policy;
	    $response['signature'] = $signature;
	    $response['expire'] = $end;
	    //这个参数是设置用户上传指定的前缀
	    $response['dir'] = $dir;
	    // print_r(json_encode($response));exit;
	    return json($response);

    }

    

	public function post(Request $request){
		$input = $request->param();
        if(isset($input['js_upload']) && $input['js_upload'] == 1){
			$model = new File;
	    	$data['og_id'] = gvar('og_id');
	    	$data['storage'] = isset($input['storage']) ? $input['storage'] : 'qiniu';
	    	$data['file_url'] = $input['file_url'];
	    	$data['file_type'] = $input['file_type'];
	    	$data['file_name'] = $input['file_name'];
	    	$data['file_size'] = $input['file_size'];
	    	$data['media_type'] = $this->get_file_type($data['file_type']);
	    	$file = $this->getFileDuration($data);
	    	$res = $model->addFile($file);
	    	if(!$res){
	    	    return $this->sendError(400,$model->getError());
	    	}
	    	return $this->sendSuccess($res);
        }else{
	    	if (!empty( $_FILES ) ) {
			   return $this->file_upload();
			}else{
				$input = input('post.');
				if(isset($input[b64key]) && substr($input[b64key],0,11) == 'data:image/'){
					return $this->base64_upload();
				}else{
					return $this->sendError('400','no input file'); 
				}
			}
        }

	}

    /**
     * @desc  NOT A API
     * @author luo
     */
	protected function file_upload(){
		$key = 'file';
		$headers = request()->header();

		if(isset($headers['x-file-key'])){
			$key = $headers['x-file-key'];
		}

		if(!isset($_FILES[$key])){
			return $this->sendError(400,'file key is empty!');
		}
		
		$tempPath  = $_FILES[$key]['tmp_name'];
	    $file_name = $_FILES[$key]['name'];
	    $file_ext  = $this->get_file_ext($file_name);
	   
	    $this->pre_save_file($file_name,$file_ext);

	    move_uploaded_file( $tempPath, $this->res_rel_file_path );
        if(in_array($file_ext, ['jpg','png','gif','jpeg'])) {
            $rs = Image::rotate($this->res_rel_file_path);
            if($rs !== true) return $this->sendError(400, $rs);
        }

	    return $this->after_save_file($file_name,$file_ext);
	}


	protected function base64_upload(){
		$post = input('post.');
		if(!preg_match('/^(data:\s*image\/(\w+);base64,)/', $post[b64key], $result)){
			return $this->sendError(400,'数据格式错误!');
		}
		$file_ext  = $result[2];
		$file_name = isset($post['_name'])?$post['_name']:random_str().'.'.$file_ext;

	    $this->pre_save_file($file_name,$file_ext);

		$file_content = str_replace($result[1], '', str_replace('#','+',$post[b64key]));
		file_put_contents($this->res_rel_file_path,base64_decode($file_content));

        if(in_array($file_ext, ['jpg','png','gif','jpeg'])) {
            $rs = Image::rotate($this->res_rel_file_path);
            if($rs !== true) return $this->sendError(400, $rs);
        }

		return $this->after_save_file($file_name,$file_ext);
	}


	protected function pre_save_file($file_name,$file_ext){
		if(!in_array($file_ext,$this->allow_file_ext)){
	    	return $this->sendError('400','上传的文件类型不允许!允许上传的文件类型为:('.implode(',',$this->allow_file_ext).')');
	    }

		$rel_dir = strtolower(input('post.mod'));

		$date_dir = date('y/m/d',request()->time());

		$user = $this->getUserInfo();

	    $user_dir  = $user['uid'];

	    $this->res_www_base_dir = $rel_dir.'/'.$user_dir.'/'.$date_dir.'/';
	    $this->res_rel_base_dir = DATA_ROOT_PATH.'uploads/'.$rel_dir.'/'.$user_dir.'/'.$date_dir.'/';
	    mkdirss($this->res_rel_base_dir);
	    $save_file_name = $this->make_save_file_name($file_name,$file_ext);
	    $this->save_file_name = $save_file_name;
	    $this->res_rel_file_path = $this->res_rel_base_dir.$save_file_name;
	    $this->res_www_file_url  = '/data/uploads/'.$this->res_www_base_dir.$save_file_name;
	}

	protected function after_save_file($file_name,$file_ext){
		$mod = strtolower(input('post.mod'));
		
	    $file_data['mod']   		= $mod;
	    $file_data['file_type'] 	= $file_ext;
	    $file_data['media_type'] 	= $this->get_file_type($file_ext);

	    $file_data['file_name']			= $file_name;

	    $file_data['file_size']			= filesize($this->res_rel_file_path);
	    $file_data['local_file']        = $this->res_rel_file_path;

	    //存储引擎处理数据  todo:
	    $storage = $this->storage;
	    $storage_func = 'storage_'.$storage.'_save';

	    if(method_exists($this, $storage_func)){
	    	$this->$storage_func($file_data);
	    }else{
	    	$file_data['file_url']		= $this->res_www_file_url;
	    }
	    $file_data['storage']    	= $storage;

	    //业务模块处理文件引用等
	    $save_func = 'save_'.$mod;
	    if($save_func != 'save_' && method_exists($this,$save_func)){
	    	$save_ret = $this->$save_func($file_data);
	    }

	    //写入数据库记录
	    if(!isset($file_data['file_id'])){
	        $file_data = $this->getFileDuration($file_data);
	    	$file_data['file_id'] = model('file')->addFile($file_data);
	    }

	    //后处理
	    if($file_data['media_type'] == 'image'){
	    	$this->after_process_image($file_data);
	    }
	    
	    if($mod == 'editor'){			//编辑器返回接口
	    	return json([
		    	"originalName" => $file_data['name'] ,
	            "name" => $file_data['name']  ,
	            "url" => $file_data['file_url'] ,
	            "size" => $file_data['size'] ,
	            "type" => '.'.$file_ext ,
	            "state" => 'SUCCESS'
            ]);
            
	    }



	    return $this->sendSuccess($file_data);
	}

    protected function getFileDuration($file)
    {
        if(empty($file['media_type']) || empty($file['file_url'])) return $file;

        if($file['media_type'] == 'voice' || $file['media_type'] == 'video') {
            $curl = new Curl();
            $curl->get($file['file_url'] . '?avinfo');
            if ($curl->error) {
                Log::record($curl->error_message, 'error');
            } else {
                $voice_info = json_decode($curl->response, true);
                $file['duration'] = $voice_info['streams'][0]['duration'];
            }
        }

        return $file;
    }

	/**
	 * 从file_data删除旧文件
	 * @param  [type] &$file_data [description]
	 * @return [type]             [description]
	 */
	protected function delete_file_data_file(&$file_data){
		//删除原来文件
	   	$w_file['mod']	  = $file_data['mod'];
	   	$w_file['rel_id'] = $file_data['rel_id'];

	   	$old_file_rs = model('file')->where($w_file)->find();
	   	if($old_file_rs){
	   		$this->delete_old_file($old_file_rs);
	   	}
	}

	/**
	 * 删除旧文件
	 * @param  [type] $rs [description]
	 * @return [type]     [description]
	 */
	protected function delete_old_file($rs){
		@unlink($rs['local_file']);
		$storage_delete_func = 'storage_'.$rs['storage'].'_delete';
		if($rs['file_url'] != '' && $rs['storage'] != 'local' && method_exists($this,$storage_delete_func)){
			$this->$storage_delete_func($rs['file_url']);
		}
		$w_file['file_id'] = $rs['file_id'];
		model('file')->where($w_file)->delete();
	}

	protected function make_save_file_name($file_name,$file_ext){
		return md5(date('ymdhis',request()->time()).$file_name).'.'.$file_ext;
	}


	/**
	 * 本地存储引擎
	 * @param  [type] &$file [description]
	 * @return [type]        [description]
	 */
	protected function storage_local_save(&$file){
		$file['file_url'] 	 = $this->res_www_file_url;
		$file['storage'] = 'local';
	}


	/**
	 * 七牛云存储上传后处理
	 * @param  [type] &$file [description]
	 * @return [type]        [description]
	 */
	protected function storage_qiniu_save(&$file){
		$config = $this->storage_config;

		// 构建鉴权对象
		$auth = new Qiniu\Auth($config['access_key'], $config['secret_key']);

		// 要上传的空间
		$bucket = $config['bucket'];

		// 生成上传 Token
		$token = $auth->uploadToken($bucket);

		// 要上传文件的本地路径
		$filePath = $file['local_file'];

		// 上传到七牛后保存的文件名
		$key = $config['prefix'].$this->res_www_base_dir.$this->save_file_name;

		// 初始化 UploadManager 对象并进行文件的上传。
		$uploadMgr = new Qiniu\Storage\UploadManager();

		// 调用 UploadManager 的 putFile 方法进行文件的上传。
		list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

		if ($err !== null) {
		    //todo:记录日志
		   
		    $file['file_url']    = $this->res_www_file_url;
		    $file['storage'] = 'local';
		} else {
		   	$file['file_url']  = $config['domain'].$key;
		   	$file['storage'] = 'qiniu';
		}

	}


	protected function storage_qiniu_delete($path){
		$config = $this->storage_config;

		$file_key = str_replace($config['domain'],'',$path);

		// 构建鉴权对象
		$auth = new Qiniu\Auth($config['access_key'], $config['secret_key']);

		// 要上传的空间
		$bucket = $config['bucket'];

		// 生成上传 Token
		$token = $auth->uploadToken($bucket);

		//初始化BucketManager
		$bucketMgr = new Qiniu\Storage\BucketManager($auth);

		$err = $bucketMgr->delete($bucket,$file_key);

		if ($err !== null) {
		   //todo:记录日志
		   
		} 
		return true;
	}

	/**
	 * 获得指定文件扩展名
	 * @param  [type] $file_path [description]
	 * @return [type]            [description]
	 */
	protected function get_file_ext($file_path){
		return strtolower(preg_replace('/^.*\./','',$file_path));
	}


	/**
	 * 获得文件类型
	 * @param  [type] $ext [description]
	 * @return [type]      [description]
	 */
	protected function get_file_type($ext){
		if(in_array($ext,$this->img_file_ext)){
			return 'image';
		}
		if($ext == 'mp3') return 'voice';
		if($ext == 'mp4' || $ext == 'mov' || $ext == 'MOV' || $ext == 'MP4' || $ext == 'flv' || $ext == 'wmv' ) return 'video';
		return 'file';
	}


	/**
	 * 个人图像处理
	 * @param  [type] &$file_data [description]
	 * @return [type]             [description]
	 */
	protected function save_avatar(&$file_data){
		$user_info = gvar('user');
		$user 	   = new User($user_info);
		$input 	   = ['avatar'=>$file_data['file_url']];
		$user->saveProfile($input,'changeAvatar');
		$file_data['rel_id']		= $user_info['uid'];

		$this->delete_file_data_file($file_data);
	}

	/**
	 * 学员图像处理
	 * @param  [type] &$file_data [description]
	 * @return [type]             [description]
	 */
	protected function save_student_avatar(&$file_data){
		$sid = input('post.sid',0);
		if(!$sid){
			@unlink($this->res_rel_file_path);
			return $this->sendError('400','缺少参数 sid!');
		}

		$student = model('student')->find($sid);

		if(!$student){
			@unlink($this->res_rel_file_path);
			return $this->sendError('400','参数错误 sid!');
		}

		$student->photo_url = $file_data['file_url'];
		$student->save();

		$file_data['rel_id'] = $sid;

		// 添加一条学员更换头像日志
		\app\api\model\StudentLog::addEditStudentAvatarLog($student);
	}

	/**
	 * 员工图像
	 * @param  [type] &$file_data [description]
	 * @return [type]             [description]
	 */
	protected function save_employee_avatar(&$file_data){
		$eid = input('post.eid/d',0);

		if(!$eid){
			@unlink($this->res_rel_file_path);
			return $this->sendErrror('400','缺少参数 eid!');
		}

		$employee = model('employee')->find($eid);

		if(!$employee){
			@unlink($this->res_rel_file_path);
			return $this->sendError('参数错误 oe_id!');
		}

		$w['eid'] = $eid;

		$update['photo_url'] = $file_data['file_url'];
		model('employee')->where($w)->update($update);

		if($employee['uid'] > 0){
			$update_user['avatar'] = $file_data['file_url'];
			model('user')->where(array('uid'=>$employee['uid']))->update($update_user);
		}

		$file_data['rel_id'] = $eid;
        $this->delete_file_data_file($file_data);
	}

    /**
     * 用户头像
     * @param  [type] &$file_data [description]
     * @return [type]             [description]
     */
    protected function save_user_avatar(&$file_data){
        $uid = input('post.uid/d',0);

        if(!$uid){
            @unlink($this->res_rel_file_path);
            return $this->sendErrror('400','缺少参数 eid!');
        }

        if($uid > 0){
            $update_user['avatar'] = $file_data['file_url'];
            model('user')->where(array('uid'=>$uid))->update($update_user);
            model('employee')->where('uid', $uid)->update(['photo_url' => $file_data['file_url']]);
        }

        $file_data['rel_id'] = $uid;
        $this->delete_file_data_file($file_data);
    }

	protected function after_process_image(&$file_data){
		$size = getimagesize($file_data['local_file']);
		$file_data['width']  = $size[0];
		$file_data['height'] = $size[1];
	}
}