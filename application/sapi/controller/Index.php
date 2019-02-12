<?php

namespace app\sapi\controller;

use think\Hook;
use think\Request;
use util\sms;
use app\common\Import;
use app\common\Export;
use app\sapi\model\User as UserModel;

/**
 * Class Open
 * @title 公共接口
 * @url /
 * @desc  需要验证登录的公共接口
 * @version 1.0
 * @readme 
 */
class Index extends Base
{
    public $noRest = true;

    public function profile(Request $request){
        $input = $request->post();
        if(empty($input['action'])){
            return $this->sendError('400','input_param_error');
        }
        $user = $request->user;
        $result = $user->saveProfile($input, $input['action']);
        if(!$result){
            return $this->sendError(400, $user->getError());
        }
        return $this->sendSuccess('ok');
    }
    
    /**
     * @title 屏幕解锁
     * @url unlock
     * @desc 锁屏之后需要验证密码进行解锁
     * @param Request $request [description]
     * @method POST
     */
    public function unlock(Request $request){
    	$password = input('post.password/s','');
    	$user_info= $this->getUserInfo();

    	$user = new UserModel($user_info);

    	$result   = $user->verifyPassword($password);

    	if(!$result){
    		return $this->sendError(400,'password_wrong');
    	}
    		
    	return $this->sendSuccess('ok');
    }

     /**
     * @title 退出登录
     * @url logout
     * @desc 退出系统时调用
     */
    public function logout(Request $request){
        $user = $this->getUserInfo();
        UserModel::logout($user['token']);
        return $this->sendSuccess('ok');
    }

    /**
     * @title 获得全局变量
     * @url   global/:name
     * @desc  获得系统的全局变量
     * @return [type] [description]
     */
    public function glob(Request $request,$name){
        $data = $this->getGlobalVars($name);
        if($name == 'branchs'){
            $data = $request->user->getPermissionBranchs($data);
        }

        return $this->sendSuccess($data);
    }


    public function switchbid(Request $request,$id){
        $request->user->switchBid($id);
        return $this->sendSuccess();
    }


    /**
     * @desc  导入数据
     */
    public function import(Request $request){
        /**
         * 1, 下载模板 get
         * 2，上传数据 post
         * 3，请求处理数据
         */
        $method = $request->method();
        $func = 'import_'.strtolower($method);
        if(method_exists($this,$func)){
            return $this->$func($request);
        }
        return $this->sendError(404,'import_handler_not_exists!');
    }

    /**
     * 导入GET方法处理
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    protected function import_get(Request $request){

        $tpl  = input('get.tpl');
        if($tpl){
            return Import::DownloadImportTpl($tpl);
        }

        $func = input('get.func');
        $fk   = input('get.fk');

        $fd   = cache($fk);

        if(!$fd){
            return $this->sendError('上传文件已经过期，请重新上传!');
        }
        
        try{
            $instance = Import::Load($func,$fd);
            $result = $instance->import();
            if(!$result){
                return $this->sendError(400,$instance->getError());
            }
            return $this->sendSuccess($result);
        }catch(\Exception $e){
            return $this->sendError(400,'import_error:'.$e->getMessage());
        }
    }

    /**
     * 导入上传文件处理
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    protected function import_post(Request $request){
        try{
            $result = Import::Upload($request);
            return $this->sendSuccess($result);
        }catch(\Exception $e){
            return $this->sendError(400,'upload_error'.$e->getMessage());
        }
    }
    
    /**
     * 导入删除文件处理
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    protected function import_delete(Request $request){
        try{
           $result = Import::CleanUploadedFile(); 
           return $this->sendSuccess($result);
        }catch(\Exception $e){
            return $this->sendError(400,'clean_uploaded_file_error'.$e->getMessage());
        }
    }

    /**
     * 导出入口
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function export(Request $request){
        $input  = input('post.');
        $res    = $input['resource'];
        try{
            $instance = Export::Load($res,$input);
            return $instance->export();
        }catch(\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /*pc端绑定手机邮箱*/
    public function sendCode(Request $request)
    {
        $type = $request->param('type'); /*mobile|email 绑定类型*/
        $value = $request->param('value');/*手机号码|email地址*/
        if (empty($type) || empty($value)) {
            return $this->sendError(400, '参数不合法');
        }
        if ($type == 'mobile') {
            $input['mobile'] = $value;
            $input['type'] = 'bindPhone';
            $input['code'] = rand(1000, 9999);
            $result = Hook::listen('sms_before_send', $input);
            if (!$result[0]) {
                return $this->sendError(400, '短信请求频率限制，请稍后再试');
            }

            /*获取模板文件准备发送短信*/
            $sms_tpl = config('sms_tpl.verify');
            $tpl_data['code'] = $input['code'];
            $tpl_data['minute'] = 5;
            $sms_content = tpl_replace($sms_tpl, $tpl_data);
            $result = sms::Send($input['mobile'], $sms_content);
            if ($result == '0') {
                $result = true;
            }
            Hook::listen('sms_after_send', $input);

        } elseif ($type == 'email') {
            $email = $value;
            $code = rand(1000, 9999);
            $result = send_email_vcode($email, $code);
        }
        if (true === $result) {
            return $this->sendSuccess();
        } else {
            return $this->sendError(400, $result);
        }
    }

    /**
     * 根据ID获取数据表一行记录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function datarow(Request $request){
        $id = $request->get('id');
        $table = $request->get('res');

        if(!$table){
            return $this->sendError(400,'parameter_error');
        }

        if(!$id){
            return $this->sendError(400,'parameter_error');
        }

        $forbidden_table = ['user'];

        if(in_array($table,$forbidden_table)){
            return $this->sendError(401,'forbidden');
        }

        $row = m($table)->find($id);

        if(!$row){
            return $this->sendError(400,'ID为'.$id.'的记录不存在');
        }

        return $this->sendSuccess($row);
    }

    /**
     * 获得UI配置
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function uiconfig(Request $request){
        $ui_config = get_ui_config('student');
        return $this->sendSuccess($ui_config);
    }
}
