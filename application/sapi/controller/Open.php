<?php

namespace app\sapi\controller;

use app\sapi\model\Employee;
use app\sapi\model\FilePackage;
use app\sapi\model\PublicSchool;
use app\sapi\model\Student;
use app\sapi\model\StudySituation;
use think\Exception;
use think\Hook;
use think\Log;
use think\Request;
use think\captcha\Captcha;
use think\config;
use app\sapi\model\User;
use app\sapi\model\CenterClientUser;
use util\sms;

/**
 * Class Open
 * @title 开放接口
 * @url /
 * @desc  不需要验证登录的公共接口
 * @version 1.0
 * @readme /md/api/api_open.md
 */
class Open extends Base
{
    public $apiAuth = false;
    public $noRest  = true;

    public function _init()
    {
        $og_id = gvar('client')['og_id'];
        gvar('og_id', $og_id);
    }

    /**
     * 验证码
     * @return [type] [description]
     */
    public function captcha(Request $request,$name = ''){
        $captcha_config = Config::get('captcha');
       
        if(isset($captcha_config[$name])){
            $captcha_config = $captcha_config[$name];
        }
        $captcha = new Captcha($captcha_config);
        return $captcha->entry($name);
    }
   
    /**
     * 注册
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signup(Request $request){
        $input = $request->post();
        $result = $this->validate($input, 'User.signup');
        if ($result !== true) {
            return $this->sendError(400,$result);
        }

        unset($input['repassword']);

        $m_user = new User();

        $input['user_type'] = User::STUDENT_ACCOUNT;
        $result = $m_user->register($input);

        if (false === $result) {
            return $this->sendError(400,$m_user->getError());
        }else{
           return $this->sendSuccess($m_user);
        }
    }

    /**
     * @desc  扫码录入客户档案
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function customer(Request $request){
        //验证数据
        $post = $request->post();
        $m_customer = new \app\api\model\Customer();
        $rule = [
            'bid|校区' => 'require',
            'name|姓名' => 'require',
            'first_tel|联系电话' => 'require|number|length:11',
        ];
        $rs = $this->validate($post, $rule);
        if($rs !== true) return $this->sendError(400, $rs);

        if(isset($post['school_id'])) {
            $post['school_id'] = PublicSchool::findOrCreate($post['school_id']);
        }

        $post['input_from'] = $m_customer::INPUT_FROM_SCAN_CODE;
        //添加数据
        $customer = $m_customer->where('name', $post['name'])->where('first_tel', $post['first_tel'])
            ->find();
        if(!empty($customer)) {
            $cu_id = $customer->cu_id;
        } else {
            $rs = $m_customer->allowField(true)->isUpdate(false)->save($post);
            if ($rs === false) return $this->sendError('添加信息失败');
            $cu_id = $m_customer->getAttr('cu_id');
        }

        if (false === $cu_id) {
            return $this->sendError(400,$m_customer->getError());
        }else{
            return $this->sendSuccess($cu_id);
        }
    }

    /**
     * 学生家长端登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function signin(Request $request){
        if(!$request->client_time_correct){
            return $this->sendError(400,_('client_time_not_correct'));
        }
        $input = $request->only(['account', 'password', 'captcha']);
        $client = gvar('client');
        $pos = strrpos($input['account'], '@');


        //如果没有通过subhost访问
        if(!$client || $client['defined_way'] == 'debug'){
            if($pos !== false){
                $sub_host = substr($input['account'],$pos+1);
                $client   = load_client_by_host($sub_host);
                gvar('client',$client);
                gvar('og_id',$client['og_id']);
                if($client['defined']){
                    $input['account'] = substr($input['account'], 0, -(strlen($sub_host) + 1));
                }

            }else{
                
                $client    = CenterClientUser::LoadClientByUser($input['account']);
                gvar('client',$client);
                gvar('og_id',$client['og_id']);
                if(!isset($client['info']['host'])) return $this->sendError(400, '帐号不存在');
                $sub_host = $client['info']['host'];
            }
        } else {
            if(!isset($client['info']['host'])) return $this->sendError(400, '帐号不存在');
            $sub_host = $client['info']['host'];

            if($pos !== false){
                $sub_host = substr($input['account'],$pos+1);
                $client   = load_client_by_host($sub_host);
                gvar('client',$client);
                gvar('og_id',$client['og_id']);
                if($client['defined']){
                    $input['account'] = substr($input['account'], 0, -(strlen($sub_host) + 1));
                }

            }
        }

        $result = $this->validate($input, 'User.signin');
        if ($result !== true) return $this->sendError(400, $result);

        $user = User::login($input['account'], $input['password'],2, 'mobile');

        if(!$user) return $this->sendError(400,User::$ERR);

        $ret = $user->loginInfo();
        $ret['x_sub_host'] =  $sub_host;
        $ret['client']     =  $this->open_client_info();
        $ret['base_url']   = $request->domain();
        $ret['product_url'] = $request->scheme().'://'.config('ui.domain');

        return $this->sendSuccess($ret,_('login success'));
    }

    /**
     * @desc  验证码验证
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function check_vcode(Request $request)
    {
        $input = $request->only(['mobile', 'vcode']);
        $rs = check_verify_code($input['mobile'], $input['vcode'], 'student_forget');
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  重置密码
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method POST
     */
    public function reset_pwd(Request $request)
    {
        $input = $request->only(['mobile', 'vcode', 'password', 'repassword']);
        $rule = [
            'mobile|手机号'        => 'require',
            'vcode|验证码'          => 'require',
            'password|新密码'      => 'require|length:6,20',
            'repassword|确认新密码' => 'require|confirm:password',
        ];
        $result = $this->validate($input, $rule);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $w = [];
        $w['mobile'] = $input['mobile'];
        $w['user_type'] = User::STUDENT_ACCOUNT;
        $user = User::get($w);
        if (empty($user)) {
            return $this->sendError(400, 'invalid request!');
        }

        $rs = check_verify_code($input['mobile'], $input['vcode'], 'student_forget');
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }
        $ret = $user->data(['password' => $input['password']], true)->save();
        if ($ret === false) {
            return $this->sendError(400, $user->getError());
        }
        return $this->sendSuccess();
    }

    public function configs(Request $request, $name)
    {
        $allow_name = ['params', 'comment_tags', 'edu', 'mobile_swiper'];
        if(!in_array($name,$allow_name)){
            $cfg = [];
        }else{
            $cfg = user_config($name);
            if (empty($cfg)) {
                $cfg = config($name);
            }
        }
        return $this->sendSuccess($cfg);
    }

    /**
     * 验证码发送
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function vcode(Request $request)
    {
        $input = $request->only(['mobile', 'type']);
        $input['code'] = random(4);
        $rule = [
            'mobile|手机号码' => 'require|regex:/^1[0-9]{10}$/',
            'type|验证类型' => 'require',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $result = Hook::listen('sms_before_send', $input);
        if (!$result[0]) {
            return $this->sendError(400, '短信请求频率限制，请稍后再试');
        }

        if($input['type'] == 'student_forget'){
            $user = m('User')
                ->where('user_type',User::STUDENT_ACCOUNT)
                ->where('mobile', $input['mobile'])
                ->find();
            if(!$user){
                return $this->sendError(400,'该手机号还没有注册账号，请联系管理员添加');
            }

        }

        $sms_tpl = config('sms_tpl.verify');
        $tpl_data['code'] = $input['code'];
        $tpl_data['minute'] = 5;
        $sms_content = tpl_replace($sms_tpl, $tpl_data);
        $result = sms::Send($input['mobile'], $sms_content);

        Log::record($result, 'info');
        Hook::listen('sms_after_send', $input);

        return $this->sendSuccess('发送成功');
    }

    /**
     * 获得公开客户信息
     * @return [type] [description]
     */
    protected function open_client_info(){
        $client = gvar('client');

        return array_merge($client['info'],[
                'domain'    => $client['domain'],
                'subdomain' => $client['subdomain']
                ]);
    }

    public function redirect(Request $request){
        $url = $request->get('url');
        $url = str_replace('$','#',$url);
        header('Location: '.$url);
    }

    public function global(Request $request)
    {
        $name = input('name');
        $name_arr = explode('.', $name);
        $name = $name_arr[0];
        $data = $this->getGlobalVars($name);

        $len = count($name_arr);
        if($len > 1) {
            foreach($name_arr as $key => $val) {
                if($key == 0) continue;
                $data = isset($data[$val]) ? $data[$val] : [];
            }
        }

        return $this->sendSuccess($data);
    }

    //在字典中查找特定的字段
    public function dicts()
    {
        $name = input('name');
        $dict_fields = ['did','og_id','pid','name','title','desc','is_system','sort'];
        $top_dicts   = model('dictionary')->where('name', $name)->field($dict_fields)->order('sort DESC')->select();
        $dict_id_map = [];
        $dicts = [];
        foreach($top_dicts as $d){

            $dict_id_map[$d['did']] = $d['name'];
            $dicts[$d['name']] = [];
        }


        $dict_items = model('dictionary')->where('pid','NEQ',0)->where('display',1)->order('sort DESC')->field($dict_fields)->select();

        foreach($dict_items as $item){
            if(isset($dict_id_map[$item['pid']])){
                $name = $dict_id_map[$item['pid']];
                if($name && isset($dicts[$name])){
                    array_push($dicts[$name],$item);
                }
            }
        }
        return $this->sendSuccess($dicts);
    }

    /**
     * @desc  界面设置
     * @author luo
     * @method GET
     */
    public function interface_config()
    {
        $config = \app\sapi\model\Config::get(['cfg_name' => 'mobile_login_interface']);
        return $this->sendSuccess($config);
    }

    /**
     * @desc UI界面配置
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function ui_config()
    {
        $config = get_ui_config('student');
        return $this->sendSuccess($config);
    }

    public function add_student(Request $request)
    {
        $input = $request->post();
        $input = array_filter($input);
        $rule = [
            ['student_name|学生姓名', 'require|max:32'],
            ['nick_name|英文名', 'max:32'],
            ['sex|性别', 'in:0,1,2'],
            ['photo_url|头像地址', 'max:255'],
            ['birth_time|出生日期', 'date'],
            ['school_grade|学校年级', 'number'],
            ['school_class|学校班级', 'max:32'],
            ['first_tel|联系电话', 'require|number'],
        ];

        $result = $this->validate($input, $rule);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $m_student = new Student();
        $rs = $m_student->createOneStudent($input);
        if($rs === false) return $this->sendError(400, $m_student->getErrorMsg());
        
        return $this->sendSuccess();

    }

    /**
     * @desc  文件包详情
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function file_package(Request $request)
    {
        $short_id = input('short_id');
        if(empty($short_id)) return $this->sendError(400, 'param error');

        $get = $request->get();
        $with = isset($get['with']) ? (is_array($get['with']) ? $get['with'] : explode(',', $get['with'])) : [];

        $file_package = FilePackage::get(['short_id' => $short_id], $with);
        return $this->sendSuccess($file_package);
    }

    /**
     * @desc  学生调研报告
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function study_situation(Request $request)
    {
        $short_id = input('short_id');
        if(empty($short_id)) return $this->sendError(400, 'param error');

        //$get = $request->get();
        //$with = !empty($get['with']) ? (is_string($get['with']) ? explode(',', $get['with']) : $get['with']) : [];

        //if(($key = array_search('content_create_employee', $with)) !== false) {
        //    $with_content_create_employee = true;
        //    unset($with[$key]);
        //}
        $with = [
            'student',
            'customer',
            'create_employee',
            'lesson_buy_suit',
        ];

        $study_situation = StudySituation::get(['short_id' => $short_id], $with);
        if(!empty($study_situation['content'])) {
            $content = [];
            foreach($study_situation['content'] as $row) {
                if(isset($row['create_eid'])) {
                    $employee = (new Employee())->where('eid', $row['create_eid'])
                        ->field('eid,ename,uid,mobile,photo_url')->find();
                    $row['create_employee'] = !empty($employee) ? $employee->toArray() : $employee;
                }
                $content[] = $row;
            }
            $study_situation['content'] = $content;
        }
        return $this->sendSuccess($study_situation);
    }

    /**
     * 通过token登录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    # https://base.pro.xiao360.com/student#/tklogin?token=db7fba00638f5e60e15eff1f1ff0c979
    public function tklogin(Request $request)
    {
        if(!$request->client_time_correct){
            return $this->sendError(400,_('客户端时间不正确！'));
        }

        $token = input('post.token');
        $cache_key = cache_key($token);

        $login_student = cache($cache_key);
        if(!$login_student){
            return $this->sendError(400,_('token invalid!'));
        }

        $db_cfg = get_dbcfg_by_cid($login_student['cid']);

        if(!$db_cfg){
            return $this->sendError(400,_('token is invalid!'));
        }

        $client = $db_cfg['client'];
        $gclient = gvar('client');

        if(!$gclient || $gclient['cid'] != $client['cid']){
            unset($db_cfg['client']);
            $gclient['cid'] = $client['cid'];
            $gclient['og_id'] = $client['og_id'];
            $gclient['parent_cid'] = $client['parent_cid'];
            $gclient['domain'] = $client['host'];
            $gclient['info'] = $client;
            $gclient['database'] = $db_cfg;
            gvar('client',$gclient);
            gvar('og_id',$client['og_id']);
            config('database',$db_cfg);
        }
        $user = User::tokenLogin($token);
        if(!$user){
            return $this->sendError(400,User::$ERR);
        }

        $ret = $user->loginInfo();

        $ret['x_sub_host'] =  $client['host'];
        $ret['client']     =  $this->open_client_info();
        $ret['base_url']   = $request->domain();
        $ret['product_url'] = $request->scheme().'://'.config('ui.domain');

        return $this->sendSuccess($ret,_('login success'));
    }



}
