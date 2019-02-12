<?php

namespace app\api\controller;

use app\api\model\Branch;
use app\api\model\Customer;
use app\api\model\Classroom;
use app\api\model\Employee;
use app\api\model\Event;
use app\api\model\Lesson;
use app\api\model\Org;
use app\api\model\Student;
use app\api\model\User;
use think\Hook;
use think\Request;
use util\sms;
use app\common\Import;
use app\common\Export;
use app\api\model\User as UserModel;
use app\api\model\StudentLessonStop;
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
     * 获得用户信息
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml\
     */
    public function userinfo(Request $request){
        return $this->sendSuccess($this->getUserInfo());
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
     * @desc  是否初始设置完成
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function is_setup(Request $request)
    {
        //课程
        $lesson_num = (new Lesson())->count();
        if($lesson_num <= 0) return $this->sendSuccess(['is_setup' => 0]);

        //教室
        $classroom_num = (new ClassRoom())->count();
        if($classroom_num <= 0) return $this->sendError(['is_setup' => 0]);

        //员工大于2
        $employee_num = (new Employee())->count();
        if($employee_num <= 0) return $this->sendError(['is_setup' => 0]);

        return $this->sendSuccess(['is_setup' => 1]);
    }

    /**
     * @desc  机构信息
     * @author luo
     * @method GET
     */
    public function get_organization_info()
    {
        $client_info = gvar('client') ? gvar('client')['info'] : [];
        $w_student['status'] = ['LT',90];
        $branch_num = Branch::useGlobalScope(false)->count();
        $student_num = Student::useGlobalScope(false)->where($w_student)->count();
        $user_num = User::useGlobalScope(false)->where('user_type', 1)->count();
        $org_num = Org::useGlobalScope(false)->count();

        $client_status = [
            'branch_num' => $branch_num,
            'student_num' => $student_num,
            'user_num' => $user_num,
            'org_num' => $org_num,
        ];

        $expire_time = isset($client_info['expire_day']) ? strtotime($client_info['expire_day']) : 0;
        $is_expire = time() > $expire_time ? true : false;

        return $this->sendSuccess(['client_info' => $client_info, 'client_status' => $client_status, 'is_expire' => $is_expire]);
    }

    /**
     * @title 获得全局变量
     * @url   global/:name
     * @desc  获得系统的全局变量
     * @return [type] [description]
     */
    public function glob(Request $request,$name){
        if($request->isMobile()) {
            $data = $this->getGlobalVarsOfMobile($name);
        } else {
            $data = $this->getGlobalVars($name);
        }
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
            $fd['get'] = input('get.');
            $instance = Import::Load($func,$fd);
            $result   = $instance->import();
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
            // var_dump('error');exit;
            return $this->sendError(400,$e->getMessage());
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
     * 获得客户信息
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function clientinfo(Request $request){
        $client = gvar('client');
        return $this->sendSuccess($client['info']);
    }

    /**
     * 执行当天的任务，1天只执行1次
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function todaytask(Request $request){
        $client = gvar('client');

        if(!$client || empty($client['info'])){
            return $this->sendSuccess();
        }

        $og_id = 0;
        $user = gvar('user') ? gvar('user') : null;
        if($user){
            $og_id = $user['og_id'];
        }

        $cache_key = get_today_task_key($client['info']['cid'],$og_id);

        $is_today_task_executed = cache($cache_key);

        if($is_today_task_executed){
            return $this->sendSuccess('done!!');
        }

        //Customer::AutoTransferPublicSea();      //自动转入公海
        StudentLessonStop::updateTodayStudentLessonStatus($og_id);
        
        Event::AutoCheckStatus();   //把过期的活动结束

        cache($cache_key,1,86400);
        
        return $this->sendSuccess('done!');

    }
}

