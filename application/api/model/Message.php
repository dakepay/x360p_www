<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/8/24
 * Time: 13:10
 */
namespace app\api\model;
use app\common\Wechat;

class Message extends Base
{
    protected $type = [
//        'content' => 'json',
        'tpl_data' => 'json',
    ];

    protected $append = ['name', 'create_employee_name'];

    protected $send_wechat = true;
    protected $send_sms    = true;
    protected $send_mobiles = [];           //发送的手机号

    protected $hidden = [
        'update_time',
        'is_delete',
        'delete_time',
        'delete_uid'
    ];

    public static $BUSINESS_TYPE = [
        1 => 'remind_before_class',
        2 => 'attendance_inform',
        3 => 'before_class_push',
        4 => 'after_class_push',
        5 => 'alter_class_time',
        6 => 'pay_success',
        7 => 'review_push',
        8 => 'attend_school_push',
        9 => 'course_remind'
    ];

    public static  function init()
    {
        parent::init();
        Message::afterInsert(function($message) {
            if(!empty($message['sid'])) {
                $business_id = 0;
                if(isset($message['business_id'])){
                    $business_id = intval($message['business_id']);
                }
                $data = [
                    'id'    => $message['id'],
                    'title' => $message['title'],
                    'context' => $message['content'],
                    'sid' => $message['sid'],
                    'message_type' => array_search($message['business_type'], self::$BUSINESS_TYPE) ?
                        array_search($message['business_type'], self::$BUSINESS_TYPE) : 0,
                    'relation_id' => $business_id
                ];
                callback_queue_push('message_push_callback_url', $data);
            }
        });
    }

    public function getNameAttr($value, $data)
    {
        $name = '';
        if(!empty($data['sid']) && $data['sid'] > 0) {
            $name = get_student_name($data['sid']);
        }

        if(!empty($data['cu_id']) && $data['cu_id'] > 0) {
            $name = (new Customer())->where('cu_id', $data['cu_id'])->value('name');
        }

        if(!empty($data['mcl_id']) && $data['mcl_id'] > 0) {
            $name = (new MarketClue())->where('mcl_id', $data['mcl_id'])->value('name');
        }

        if(!empty($data['eid']) && $data['eid'] > 0) {
            // $name = (new Employee())->where('eid', $data['eid'])->value('ename');
            $name = get_employee_name($data['eid'],true);
        }

        return $name;
    }



    public function changeStatus()
    {
        $this->data('status', 1);
        $this->save();
    }

    /**
     * 获得模板消息预览数据
     * @param $bs_type
     * @param $bs_data
     * @param int $send_mode
     */
    public function geTplMsgPreviewData($bs_type,$bs_data)
    {
        $ret['sms']    = ['send'=>1,'mobile'=>[],'content'=>''];
        $ret['wechat'] = ['send'=>1,'openid'=>[],'title'=>'','data'=>[],'url'=>''];

        $tpl_define = tplmsg_config($bs_type);
        if(is_null($tpl_define)){
            $error = '模板消息未定义:'.$bs_type;
            $log_error = $error.print_r($bs_data,true);
            log_write($log_error,'error');
            return $this->user_error($error);
        }

        $tpl_fields = $tpl_define['tpl_fields'];

        $tpl_data_fields    = array_keys($tpl_fields);

        $func = 'get_'.$bs_type.'_tpldata';

        if(method_exists($this,$func)){
            $tpl_data = call_user_func_array(array($this,$func),[$tpl_data_fields,$bs_data]);
        }else{
            $error = '获取模板消息数据的方法未定义:'.$func;
            $log_error = $error.print_r($bs_data,true);
            log_write($log_error,'error');
            return $this->user_error($error);
        }

        $tpl_data['search'] = array_values($tpl_fields);
        $terminal   = $tpl_data['data']['terminal'];

        if($terminal == 'customer'){
            $cu_info = get_customer_info($tpl_data['data']['cu_id']);
            array_push($ret['sms']['mobile'],$cu_info['first_tel']);
            if(!empty($cu_info['openid'])) {
                array_push($ret['wechat']['openid'],$cu_info['openid']);
            }
        }elseif($terminal == 'student'){
            $w_s = [];
            $w_s['sid'] = $tpl_data['data']['sid'];
            $m_student = $this->m_student->where($w_s)->cache(1)->find();
            $user_list = $m_student->user;
            if ($user_list) {
                foreach ($user_list as $user) {
                    array_push($ret['sms']['mobile'], $user['mobile']);
                    if (!empty($user['openid'])) {
                        array_push($ret['wechat']['openid'], $user['openid']);
                    }
                }
            }
        }else {

            $w_u['uid'] = $tpl_data['data']['uid'];
            $user = $this->m_user->where($w_u)->find();
            if ($user) {
                array_push($ret['sms']['mobile'], $user['mobile']);
                if (!empty($user['openid'])) {
                    array_push($ret['wechat']['openid'], $user['openid']);
                }
            }
        }

        if(empty($ret['sms']['mobile'])){
            $ret['sms']['send'] = 0;
        }

        if(empty($ret['wechat']['openid'])){
            $ret['wechat']['send'] = 0;
        }

        if(!empty($tpl_define['sms']['apply_tpl'])){
            if($tpl_define['sms']['std_id'] == 0) {
                $ret['sms']['send'] = 0;
                $ret['sms']['content'] = '短信模板未配置，无法发送';
            } else {
                $ret['sms']['content'] = $this->get_sms_preview_content($tpl_define, $tpl_data);
            }
        }

        $ret['wechat']['title'] = $tpl_define['weixin']['tpl_title'];
        $ret['wechat']['data']  = $this->get_wechat_preview_data($tpl_define,$tpl_data);

        return $ret;
    }

    private function get_sms_preview_content($tpl_define,$tpl_data)
    {
        $tpl_sms_define = $tpl_define['sms'];
        $tpl = $tpl_sms_define['apply_tpl'];
        $var_define = [];
        if($tpl_sms_define['std_id'] > 0){
            $std_info = get_std_info($tpl_sms_define['std_id']);
            if($std_info){
                $tpl = $std_info['apply_tpl'];
                $var_define = $std_info['tpl_define'];
            }
        }
        $var_tables = $this->get_sms_tpl_data($var_define,$tpl_data);

        $content = tpl_replace($tpl,$var_tables);

        return $content;

    }

    private function get_wechat_preview_data($tpl_define,$tpl_data)
    {
        $ret = [];
        $wechat_data = $tpl_define['weixin']['data'];
        foreach($wechat_data as $k=>$r){
            if(strpos($k,'keyword') !== false){
                $label   = str_replace(']','',str_replace('[','',$r[0]));
                $r[0] = tplmsg_content($r[0],$tpl_data['search'],$tpl_data['replace']);
                $r[2] = $label;
                $ret[$k] = $r;
            }else{
                $r[0] = tplmsg_content($r[0],$tpl_data['search'],$tpl_data['replace']);
                $ret[$k] = $r;
            }
        }
        return $ret;
    }

    /**
     * 发送模板消息
     * @param  [type] $bs_type [业务类型]
     * @param  [type] $bs_data [业务数据]
     * @param  [type] $mobiles [手机号]
     * @param  [type] $send_mode [发送模式」
     * @return [type]          [description]
     */
    public function sendTplMsg($bs_type,$bs_data,$mobiles=[],$send_mode = 4, $delay = 0)
    {
        if($send_mode == 4){
            $this->send_wechat = true;
            $this->send_sms    = true;
        }else if($send_mode == 2){
            $this->send_wechat = true;
            $this->send_sms    = false;
        }else if($send_mode == 1){
            $this->send_wechat = false;
            $this->send_sms    = true;
        }
        if(!is_array($mobiles)){
            $this->send_mobiles = explode(',',$mobiles);
        }else{
            $this->send_mobiles = $mobiles;
        }

        $tpl_define = tplmsg_config($bs_type);
        if(is_null($tpl_define)){
            $error = '模板消息未定义:'.$bs_type;
            $log_error = $error.print_r($bs_data,true);
            log_write($log_error,'error');
            return $this->user_error($error);
        }

        $tpl_fields = $tpl_define['tpl_fields'];

        
        $tpl_data_fields    = array_keys($tpl_fields);

        $func = 'get_'.$bs_type.'_tpldata';

        if(method_exists($this,$func)){
            $tpl_data = call_user_func_array(array($this,$func),[$tpl_data_fields,$bs_data]);
        }else{
            $error = '获取模板消息数据的方法未定义:'.$func;
            $log_error = $error.print_r($bs_data,true);
            log_write($log_error,'error');
            return $this->user_error($error);
        }

        $tpl_data['search'] = array_values($tpl_fields);
        $terminal   = $tpl_data['data']['terminal'];
        $msg = [];
        $msg['business_type'] = $bs_type;
        if(isset($bs_data['url']) && substr($bs_data['url'],0,4) == 'http'){
            $msg['url'] = $bs_data['url'];
        }else{
            $msg['url']     = tplmsg_url($tpl_define['weixin']['url'],array_merge($bs_data,$tpl_data),$terminal);
        }
        $msg['title']   = tplmsg_content($tpl_define['message']['title'],$tpl_data['search'],$tpl_data['replace']);
        $msg['content'] = tplmsg_content($tpl_define['message']['content'],$tpl_data['search'],$tpl_data['replace']);
        $msg['send_mode'] = 0;

        array_copy($msg,$tpl_data['data'],['og_id','bid','sid','cu_id','cid','business_id','uid']);

        $is_push = true;
        $request = input('request.');

        if(isset($request['is_push']) && !$request['is_push']){
            $is_push = false;
        }

        if($is_push){
            if($terminal == 'student') {
                $w_s = [];
                $w_s['sid'] = $tpl_data['data']['sid'];
                $m_student = (new Student)->where($w_s)->cache(1)->find();
                $user_list = $m_student->user;
                if ($user_list) {
                    foreach ($user_list as $user) {
                        $result = $this->push_msg_to_user($msg, $user, $tpl_define, $tpl_data, $bs_type,$delay);
                    }
                }
            }elseif($terminal == 'customer'){
                $cu_info = get_customer_info($tpl_data['data']['cu_id']);
                $user = [];
                $user['uid'] = 0;
                $user['cu_id'] = $cu_info['cu_id'];
                $user['mobile'] = $cu_info['first_tel'];
                $user['openid'] = $cu_info['openid'];
                if($cu_info){
                    $this->push_msg_to_user($msg,$user,$tpl_define,$tpl_data,$bs_type,$delay);
                }
            }else{
                if($msg['uid'] > 0){
                    $w_u['uid'] = $msg['uid'];
                    $user = $this->m_user->where($w_u)->find();
                    if($user){
                        $this->push_msg_to_user($msg,$user,$tpl_define,$tpl_data,$bs_type,$delay);
                    }
                }
            }
        }

        if($msg['uid'] == 0){
            $this->data([])->isUpdate(false)->save($msg);
        }

        return true;
    }

    /**
     * 手机号是否在发送手机号名单里
     * @param $mobile
     */
    protected function mobile_in_send_mobiles($mobile)
    {
        if(empty($this->send_mobiles)){
            return true;
        }
        return in_array($mobile,$this->send_mobiles);
    }


    /**
     * 推送消息给用户
     * @param $msg
     * @param $user
     * @param $tpl_define
     * @param $tpl_data
     * @param $bs_type
     * @return bool
     */
    protected function push_msg_to_user(&$msg,&$user,&$tpl_define,&$tpl_data,$bs_type,$delay = 0)
    {
        if($this->send_sms && $tpl_define['sms_switch'] && $tpl_define['sms']['std_id'] > 0 && !empty($user['mobile'])){
            /*
            $sms_message = tplmsg_content($tpl_define['sms']['tpl'],$tpl_data['serach'],$tpl_data['replace']);
            queue_push('SendSmsMsg', [$user['mobile'], $sms_message]);
            $msg['send_mode'] = $msg['send_mode'] + 1;
            */
            $std_id = $tpl_define['sms']['std_id'];
            $std_info = get_std_info($std_id);
            if($std_info && $this->mobile_in_send_mobiles($user['mobile'])){
                /*
                $data = [
                    'mobile' => 18316227457,
                    'data' => '',
                    'tpl_id' => 'SMS_136384668',
                    'tpl_data' => ['name' => 'll', 'key' => 'ss'],
                    'service_name' => null,
                ];
                */
                $job_data = [];
                $job_data['mobile'] = $user['mobile'];
                $job_data['tpl_id'] = $std_info['tpl_id'];
                $job_data['service_name'] = $std_info['service_name'];
                $job_data['content'] = '';
                $job_data['tpl_data'] = $this->get_sms_tpl_data($std_info['tpl_define'],$tpl_data);
                $job_data['class'] = 'SendSms';
                queue_push('Base', $job_data);
                $msg['send_mode'] = $msg['send_mode'] + 1;

            }

        }

        if($this->send_wechat && $tpl_define['weixin_switch'] && !empty($user['openid']))
        {
            $m_wxmp_fans = new WxmpFans();
            $wx_message_prepare = $this->get_wxmessage_prepare($tpl_define['weixin'],$tpl_data,$bs_type);
            if($wx_message_prepare && !empty($wx_message_prepare['template_id'])){
                $is_fans_subscribe = $m_wxmp_fans->isOpenidSubscribe($wx_message_prepare['appid'],$user['openid']);
                if($is_fans_subscribe){
                    $wx_message_prepare['url']      = $msg['url'];
                    $wx_message_prepare['openid']   = $user['openid'];
                    queue_push('SendWxTplMsg',$wx_message_prepare,'SendWxTplMsg',$delay);
                    $msg['send_mode'] = $msg['send_mode'] + 2;
                }
            }
        }

        $msg['uid'] = $user['uid'];

        if($msg['send_mode'] == 3){
            $msg['send_mode'] = 4;
        }
        $result = $this->data([])->isUpdate(false)->save($msg);
        if(false === $result){
            log_write($this->sql_add_error('message'),'error');
        }
        return true;
    }

    public function push_msg_to_mobile($bs_data, $mobile, $bs_type)
    {
        $tpl_define = tplmsg_config($bs_type);
        if(is_null($tpl_define)){
            $error = '模板消息未定义:'.$bs_type;
            $log_error = $error.print_r($bs_data,true);
            log_write($log_error,'error');
            return $this->user_error($error);
        }

        $tpl_fields = $tpl_define['tpl_fields'];
        $tpl_data_fields    = array_keys($tpl_fields);

        $func = 'get_'.$bs_type.'_tpldata';
        if(method_exists($this,$func)){
            $tpl_data = call_user_func_array(array($this,$func),[$tpl_data_fields,$bs_data]);
        }else{
            $error = '获取模板消息数据的方法未定义:'.$func;
            $log_error = $error.print_r($bs_data,true);
            log_write($log_error,'error');
            return $this->user_error($error);
        }

        $tpl_data['search'] = array_values($tpl_fields);

        if($tpl_define['sms_switch'] && $tpl_define['sms']['std_id'] > 0){
            $std_id = $tpl_define['sms']['std_id'];
            $std_info = get_std_info($std_id);
            if($std_info){
                /*
                $data = [
                    'mobile' => 18316227457,
                    'data' => '',
                    'tpl_id' => 'SMS_136384668',
                    'tpl_data' => ['name' => 'll', 'key' => 'ss'],
                    'service_name' => null,
                ];
                */
                $job_data = [];
                $job_data['mobile'] = $mobile;
                $job_data['tpl_id'] = $std_info['tpl_id'];
                $job_data['content'] = '';
                $job_data['tpl_data'] = $this->get_sms_tpl_data($std_info['tpl_define'],$tpl_data);
                queue_push('SendSms', $job_data);
            }
        }
    }

    /**
     * 获取短信模板定义变量数据
     * @param $sms_tpl_define
     * @param $tpl_data
     */
    protected function get_sms_tpl_data($sms_tpl_define,$tpl_data)
    {
        $ret = [];
        foreach($sms_tpl_define as $v){
            $key = $v['r'];
            $ret[$key] = tplmsg_content($v['l'],$tpl_data['search'],$tpl_data['replace']);
        }
        return $ret;
    }


    /**
     * 获得微信模板消息的定义
     * @param $wx_tpl_define
     * @param $tpl_data
     * @param $scene
     * @return mixed
     * @throws \think\Exception
     */
    public function get_wxmessage_prepare($wx_tpl_define,$tpl_data,$scene)
    {

        $wechat = Wechat::getInstance();

        $message['appid'] = $wechat->appid;
        $message['template_id'] = '';

        if($wechat->default){
            $message['template_id'] = $wx_tpl_define['template_id'];
        }else{
            $message['template_id'] = (new WxmpTemplate())->getTemplateIdByAppidAndScene($wechat->appid,$scene);
        }

        if(empty($message['template_id'])){
            return $message;
        }
        $message['data'] = $wx_tpl_define['data'];
        foreach($message['data'] as $k=>$v){
            $message['data'][$k] = tplmsg_content($v,$tpl_data['search'],$tpl_data['replace']);
        }
        return $message;
    }

    //预览发送内容
    public function getPreviewMsg($bs_type,$bs_data)
    {
        $tpl_define = tplmsg_config($bs_type);
        if(is_null($tpl_define)){
            $error = '模板消息未定义:'.$bs_type;
            $log_error = $error.print_r($bs_data,true);
            log_write($log_error,'error');
            return $this->user_error($error);
        }

        $tpl_fields = $tpl_define['tpl_fields'];
        $tpl_data_fields    = array_keys($tpl_fields);

        $func = 'get_'.$bs_type.'_tpldata';
        if(method_exists($this,$func)){
            $tpl_data = call_user_func_array(array($this,$func),[$tpl_data_fields,$bs_data]);
        }else{
            $error = '获取模板消息数据的方法未定义:'.$func;
            $log_error = $error.print_r($bs_data,true);
            log_write($log_error,'error');
            return $this->user_error($error);
        }

        $tpl_data['search'] = array_values($tpl_fields);

        $wechat_msg['data'] = $tpl_define['weixin']['data'];
        foreach($wechat_msg['data'] as $k=>$v){
            $wechat_msg['data'][$k] = tplmsg_content($v,$tpl_data['search'],$tpl_data['replace']);
        }

        $sms_message = tplmsg_content($tpl_define['sms']['tpl'],$tpl_data['search'],$tpl_data['replace']);
        return ['wechat_msg' => $wechat_msg, 'sms' => $sms_message];
    }

    /**
     * 获得考勤通知的模板数据
     * @param  [type] $fields  [description]
     * @param  [type] $bs_data [description]
     * @return [type]          [description]
     */
    protected function get_attendance_inform_tpldata($fields,$bs_data)
    {
        $replace = [];
       
        $data['terminal'] = 'student';
        $data['business_id'] = $bs_data['satt_id'];
        $data['send_mode']   = 0;

        $satt_info = get_satt_info($bs_data['satt_id']);

        $student = get_student_info($bs_data['sid']);

        $lesson_start_time   = int_day_hour_to_time($bs_data['int_day'],$bs_data['int_start_hour']);
        $data['time'] = date('Y-m-d H:i',$lesson_start_time);
        $data['org_name'] = $this->get_field_org_name($bs_data['og_id']);
        $data['address']  = $this->get_field_branch_name($bs_data['bid']);
        //$data['student_name'] = $this->get_field_student_name($bs_data['sid']);
        $data['student_name'] = $student['student_name'];
        $data['uid']            = 0;
        $data['student_lesson_hours'] = $student['student_lesson_hours'];
        $data['student_lesson_remain_hours'] = $student['student_lesson_remain_hours']  - $satt_info['consume_lesson_hour'];
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }
        
        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 获得到离校通知的模板消息数据
     * @param  [type] $fields  [description]
     * @param  [type] $bs_data [description]
     * @return [type]          [description]
     */
    protected function get_attend_school_push_tpldata($fields,$bs_data)
    {
        $replace = [];

        $map = [
            'attend' =>'到校',
            'leave'  =>'离校'
        ];
       
        $data['terminal']    = 'student';
        $data['business_id'] = $bs_data['sasl_id'];
        $data['send_mode']   = 0;

        $data['action_name']  = $map[$bs_data['action']];
        $data['student_name'] = $this->get_field_student_name($bs_data['sid']);
        $data['create_time']  = date('Y-m-d',time());
        $data['create_day']   = date('H:i',time());
        $data['branch_name']  = $this->get_field_branch_name($bs_data['bid']);
        
        $data['uid']            = 0;
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }
        
        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 订单支付成功通知
     * @param $fields
     * @param $bs_data
     * @return array
     */
    protected function get_order_purchase_success_tpldata($fields,$bs_data)
    {
        $replace = [];

        $m_order = new Order($bs_data);

        $data['terminal'] = 'student';
        $data['business_id'] = $bs_data['oid'];
        $data['send_mode']   = 0;

        $data['order_no'] = $bs_data['order_no'];
        $data['detail']   = $m_order->getTplDetail(true);
        $data['create_time'] = date('Y-m-d H:i',time());
        $data['org_name'] = $this->get_field_org_name($bs_data['og_id']);
        $data['address']  = $this->get_field_branch_name($bs_data['bid']);
        $data['student_name'] = $this->get_field_student_name($bs_data['sid']);
        $data['uid']            = 0;
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 问卷调查分析通知
     * @param $fields
     * @param $bs_data
     * @return array
     */
    protected function get_study_situation_tpldata($fields,$bs_data)
    {
        $replace = [];
        $terminal = 'student';
        if($bs_data['cu_id'] > 0){
            $terminal = 'customer';
            $cu_info = get_customer_info($bs_data['cu_id']);
            $student_name = $cu_info['name'];
        }else{
            $student_name = $this->get_field_student_name($bs_data['sid']);
        }
        $data['cu_id'] = $bs_data['cu_id'];
        $data['sid']   = $bs_data['sid'];
        $data['terminal'] = $terminal;
        $data['business_id'] = $bs_data['ss_id'];
        $data['send_mode']   = 0;

        $data['student_name'] = $student_name;

        $key_prefix_list = config('wxopen.key_reply_prefix');


        $data['key']    = $key_prefix_list[0].$bs_data['short_id'];
        $data['remark'] = substr($bs_data['remark'], 0, 99);
        $data['uid']            = 0;
        $data['ss_id']    = $bs_data['ss_id'];

        //判断如果没有配置默认公众号需要在key上带上  @host
        $w_wxmp['is_default'] = 1;
        $wxmp_info = get_wxmp_info($w_wxmp);
        if(!$wxmp_info){
            $client = gvar('client');
            $data['key'] = $data['key'].'@'.$client['domain'];
        }

        $data = array_merge($bs_data,$data);


        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 市场名单分配通知员工
     * @param  [type] $fields  [description]
     * @param  [type] $bs_data [description]
     * @return [type]          [description]
     */
    protected function get_clue_to_employee_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'employee';
        $data['business_id'] = $bs_data['eid'];
        $data['send_mode']   = 0;


        if(!empty($bs_data['mcl_ids']) && is_array($bs_data)) {
            $market_clue = MarketClue::get($bs_data['mcl_ids'][0]);
            $data['name'] = !empty($market_clue)
                ? (count($bs_data['mcl_ids']) > 1 ? $market_clue['name'] . '等' : $market_clue['name']) : '';
        }

        $data['uid'] = Employee::getUidByEid($bs_data['eid']);
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 市场名单分配通知家长
     * @param  [type] $fields  [description]
     * @param  [type] $bs_data [description]
     * @return [type]          [description]
     */
    protected function get_clue_to_student_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'student';
        $data['business_id'] = $bs_data['mcl_id'];
        $data['send_mode']   = 0;


        $market_clue = MarketClue::get($bs_data['mcl_id']);
        $data['name'] = !empty($market_clue) ? $market_clue['name'] : '';
        $data['uid'] = Employee::getUidByEid($bs_data['eid']);
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 待办任务通知
     * @param $fields
     * @param $bs_data
     * @return array
     */
    protected function get_to_do_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'student';
        $data['business_id'] = $bs_data['spt_id'];
        $data['send_mode']   = 0;

        $data['subject'] = isset($bs_data['subject']) ? $bs_data['subject'] : '系统推送了新的事项';
        $data['date'] = date('Y-m-d', time());
        $data['remark'] = substr($bs_data['remark'], 0, 99);
        $data['uid']            = 0;
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 工作台待办提醒通知
     * @param $fields
     * @param $bs_data
     */
    protected function get_back_log_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'back_log';
        $data['business_id'] = $bs_data['bl_id'];
        $data['subject'] = isset($bs_data['subject']) ? $bs_data['subject'] : '您有一条待办事项提醒';
        $data['uid'] = $bs_data['uid'];
        $data['content'] = $bs_data['content'];
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }
        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 公告信息提醒
     * @param $fields
     * @param $bs_data
     * @return array
     */
    protected function get_broadcast_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'broadcast';
        $data['business_id'] = $bs_data['bc_id'];
        $data['send_mode']   = 0;

        $data['subject'] = isset($bs_data['subject']) ? $bs_data['subject'] : '您已收到一条公告信息';
        $data['content'] = isset($bs_data['content']) ? $bs_data['content'] : '您已收到一条公告信息';
        $data['date'] = date('Y-m-d H:i:s', time());
        $data['uid'] = $bs_data['uid'];
        $data['url'] = $bs_data['url'];

        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 外教端翻译汇总
     * @param $fields
     * @param $bs_data
     * @return array
     */
    protected function get_ft_review_remind_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'ft_review_remind';
        $data['business_id'] = $bs_data['ftrp_id'];
        $data['send_mode']   = 0;

        $data['subject'] = isset($bs_data['subject']) ? $bs_data['subject'] : '您已收到今日翻译汇总';
        $data['content'] = isset($bs_data['content']) ? $bs_data['content'] : '您已收到今日翻译汇总';
        $data['date'] = date('Y-m-d H:i:s', time());
        $data['uid'] = $bs_data['uid'];

        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 老师授课通知
     * @param $fields
     * @param $bs_data
     * @return array
     */
    protected function get_remind_teacher_tpldata($fields,$bs_data){
        $replace = [];

        $data['terminal'] = 'teacher';
        $data['business_id'] = $bs_data['ca_id'];
        $data['send_mode']   = 0;

        $data['subject'] = isset($bs_data['subject']) ? $bs_data['subject'] : '系统推送了新的事项';
        $data['content'] = isset($bs_data['content']) ? $bs_data['content'] : '系统推送了新的事项';
        $data['date'] = date('Y-m-d', time());
        $data['uid'] = $bs_data['uid'];
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 课评通知
     * @param $fields
     * @param $bs_data
     * @return array
     */
    protected function get_review_push_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'review';
        $data['business_id'] = $bs_data['rvw_id'];
        $data['send_mode']   = 0;

        $data['subject'] = isset($bs_data['subject']) ? $bs_data['subject'] : '系统推送了新的事项';
        $data['content'] = isset($bs_data['content']) ? $bs_data['content'] : '系统推送了新的事项';
        $data['date'] = date('Y-m-d', time());
        $data['uid'] = $bs_data['uid'];
        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 上课时间段调整通知
     * @param $fields
     * @param $bs_data
     */
    protected function get_alter_class_time_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'student';
        $data['ca_id'] = $bs_data['ca_id'];
        $data['send_mode']   = 0;

        $data['student_name'] = get_student_name($bs_data['sid']);

        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 作业驳回通知
     * @param $fields
     * @param $bs_data
     * @return array
     */
    protected function get_homework_rejected_tpldata($fields,$bs_data)
    {
        $replace = [];

        $data['terminal'] = 'student';
        $data['hc_id'] = $bs_data['hc_id'];
        $data['send_mode']   = 0;

        $data['subject'] = isset($bs_data['subject']) ? $bs_data['subject'] : '作业驳回通知';
        $data['remark'] = isset($bs_data['remark']) ? $bs_data['remark'] : '';
        $data['student_name'] = $this->get_field_student_name($bs_data['sid']);

        $data = array_merge($bs_data,$data);

        foreach($fields as $f){
            if(isset($data[$f])){
                array_push($replace,$data[$f]);
            }else{
                array_push($replace,'');
            }
        }

        return ['replace'=>$replace,'data'=>$data];
    }

    /**
     * 获取学生姓名字段
     * @param  [type] $sid [description]
     * @return [type]      [description]
     */
    protected function get_field_student_name($sid){
        $w['sid'] = $sid;
        $student_info = $this->m_student->where($w)->cache(1)->find();
        if($student_info){
            return $student_info['student_name'];
        }
        return '';
    }

    /**
     * 获取机构名称字段
     * @param  [type] $og_id [description]
     * @return [type]        [description]
     */
    protected function get_field_org_name($og_id){
        if($og_id == 0){
            return user_config('params.org_name');
        }
        $w_og['og_id'] = $og_id;
        $og_info = $this->m_org->where($w_og)->find();
        if($og_info){
            return $og_info['org_name'];
        }
        return user_config('params.org_name');
    }
    /**
     * 获取校区名字字段
     * @param  [type] $bid [description]
     * @return [type]      [description]
     */
    protected function get_field_branch_name($bid){
        $w_b['bid'] = $bid;
        $branch_info = $this->m_branch->where($w_b)->find();
        $surfix = '校区';
        $name = 'xx';
        if($branch_info){
            if(!empty($branch_info['short_name'])){
                $name = $branch_info['short_name'];
            }else{
                $name = $branch_info['branch_name'];
            }
        }
        if(strpos($name,$surfix) === false){
            $name .= $surfix;
        }
        return $name;
    }

    protected function get_field_uid($sid){
        $w['sid'] = $sid;
        $us_info = $this->m_user_student->where($w)->cache(1)->find();
        if($us_info){
            return $us_info['uid'];
        }
        return 0;
    }

    public function addMessage($data)
    {
        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        return $rs;
    }


}