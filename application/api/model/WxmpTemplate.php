<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/25
 * Time: 10:44
 */
namespace app\api\model;

use app\common\Wechat;
use think\Exception;
use think\Log;

class WxmpTemplate extends Base
{
    public $sync_fail_info = [];
    public $sync_success_info = [];

    protected $skip_og_id_condition = true;

    //20180313 中心数据库要设置全局默认公众号，不然有问题
    public function addTemplate($item, $appid,$tpl_id = '')
    {
        try {
            $notice = Wechat::getApp($appid)->notice;
            static $customer_templates;
            if (empty($customer_templates)) {
                $customer_templates = $notice->getPrivateTemplates()['template_list'];
            }

            //static $default_templates;
            //if (empty($default_templates)) {
            //    $default_templates = Wechat::getSystemDefaultTemplates();
            //}
            //
            //$value = false;
            //foreach ($default_templates as $value) {
            //    if ($value['template_id'] == $item['weixin']['template_id']) {
            //        break;
            //    } else {
            //        $value = false;
            //    }
            //}
            //if (!$value) {
            //    throw new Exception('默认公众号的模板配置不一致!');
            //}
            //unset($value['template_id']);

            foreach ($customer_templates as $v) {
                $temp = $v['template_id'];
                unset($v['template_id']);
                //if ($value == $v) { //如果 $a 和 $b 具有相同的键／值对则为 TRUE。
                //    $template_id = $temp;
                //}
            }

            //正常情况：{"errcode":0,"errmsg":"ok","template_id":"poeP7ife1KqR1T_gccCIDbX19MrxsAok2GTGX5VOknY"}
            //异常：code:45026, message:template num exceeds limit hint: [anvOIa0484vr24]
            $short_id = $item['weixin']['short_id'];
            if($tpl_id == ''){
            
                if (empty($template_id)) {
                    $response = $notice->addTemplate($short_id);
                    if ($response['errcode'] !== 0) {
                        return $this->user_error($response['errmsg']);
                    }
                    $template_id = $response['template_id'];
                }
            }else{
                $template_id = $tpl_id;
            }

            $data['appid']    = Wechat::getInstance()->appid;
            $data['short_id'] = $short_id;
            $data['template_id'] = $template_id;

            $wxmp = Wechat::getInstance()->wxmp;
            if($wxmp){
                $data['og_id']   = $wxmp['og_id'];
                $data['wxmp_id'] = $wxmp['wxmp_id'];
            }
            $model = new self();
            $rs = $model->allowField(true)->isUpdate(false)->save($data);
            if (!$rs) {
                return false;
            }
        } catch (\Exception $exception) {
            Log::record($exception->getCode() . '  '. $exception->getMessage(), 'error');
            if ($exception->getCode() == 45026 && strpos($exception->getMessage(), 'exceeds')) {
                $this->error = '公众号后台消息模板数量已经超过了腾讯规定的可设置的最大数量25个!';
            } else {
                $this->error = $exception->getMessage() . ' code:' . $exception->getCode();
            }
            return false;
        }
        return $model;
    }

    public function removeTemplate()
    {
        $template_id = $this->getData('template_id');
        try {
            $notice = Wechat::getApp()->notice;
            $response = $notice->deletePrivateTemplate($template_id);
            if ($response['errcode'] !== 0) {
                return $this->user_error($response['errmsg']);
            }
            $this->delete(true);
        } catch (\Exception $exception) {
//            throw $exception;
            return $this->user_error($exception->getMessage());
        }

        return true;
    }

    public function test()
    {

    }

    /**
     * 公众号所在行业:  教育-培训:16, IT科技-IT软件与服务:2,
     */
    public function setIndustry($appid)
    {
        $industry_config = config('wxopen.industry');
        $notice   = Wechat::getApp($appid)->notice;
        try {
            $industry = $notice->getIndustry();
        } catch (\Exception $e) {
            $industry = [];
        }

        foreach ($industry as $key => $item) {
            $temp = join('-', $item);
            if (!in_array($temp, $industry_config)) {
                unset($industry[$key]);
            } else {
                $industry[$key] = $temp;
            }
        }
        $industry = is_array($industry) ? array_values($industry) : array_values($industry->toArray());
//        dump($industry);
        if (count($industry) == 2) {
            $this->sync_success_info['industry'] = '公众号当前的行业设置和需要配置的一致！';
            return true;
        } elseif (!request()->confirm) {
            $this->error = '公众号当前的行业设置和需要配置的不一致,是否更新公众号的所在行业设置？';
            $this->error_code = 303;
            return false;
        }

        try {
            list ($industryId1, $industryId2) = array_keys($industry_config);
            $res = $notice->setIndustry($industryId1, $industryId2);
            $this->sync_fail_info['res'] = $res;
        } catch (\Exception $exception) {
            //message:change template too frequently hint: [3n0722vr26]
            //code:43100
            if ($exception->getCode() == 43100 && strpos($exception->getMessage(), 'frequently')) {
                $this->sync_fail_info['industry'] = '公众号所在行业每个月只能设置一次,这是腾讯公司的限制！';
            } else {
                $this->sync_fail_info['industry'] = $exception->getMessage() . ' code:' . $exception->getCode();
            }
            if ($industry) { /*有一个行业设置符合要求*/
                return $industry;
            } else {/*没有设置成功，并且当前的行业与需要配置的行业没有一个是相同的*/
                return false;
            }
        }
        return true;
    }

    public function syncTemplate($appid)
    {
        $user   = gvar('user');
        $client = gvar('client');

        $is_base_admin = false;

        if($user['account'] == 'admin' && $client['domain'] == 'base'){
            $is_base_admin = true;
        }

        $industry = $this->setIndustry($appid);
        if (!$industry) {
            return false;
        }

        $wechat = Wechat::getInstance($appid);
        if ($wechat->default && !$is_base_admin) {
            return $this->user_error('不允许修改默认公众号模板信息!');
        }

        $notice = $wechat->app->notice;
        $customer_templates = $notice->getPrivateTemplates()['template_list'];
        $wechat_tpl_ids = array_column($customer_templates, 'template_id');

        $list = config('tplmsg');
        foreach ($list as $scene => $item) {
            if(empty($item['weixin'])) continue;
            if (is_array($industry) && !in_array($item['weixin']['tpl_industry'], $industry)) {
                $this->sync_fail_info[$scene] = '无法设置该模板对应的行业到该公众号！';
                continue;
            }
            $w = [];
            $w['scene'] = $scene;
            $w['appid'] = $wechat->appid;
            $w['short_id'] = $item['weixin']['short_id'];
            $record = self::get($w);
            if ($record && !in_array($record['template_id'], $wechat_tpl_ids)) {
                /*这种情况是本地数据库有该条模板，而客户公众号微信数据库的模板没了，需要把微信服务器没有的模板删除了*/
                $record->delete();
                $record = false;
            }
            if ($record) {
                $this->sync_success_info[$scene] = '模板已设置!';
                continue;
            } else {
                /*两中情况:1.模板没有设置.2.微信服务器有设置当时在本地数据库没有保存*/
               
                $template = self::get($w);/*多个模板应用场景共用一个模板*/
                if (empty($template)) {
                    $tpl_id = $this->get_template_id($item,$customer_templates);

                    $template = $this->addTemplate($item, $appid,$tpl_id);
                    if (!$template) {
                        $this->sync_fail_info[$scene] = $this->getError();
                        continue;
                    } else {
                        $template->data('scene', $scene)->save();
                    }
                } else {
                    unset($template['wt_id']);
                    $template->data('scene', $scene)->isUpdate(false)->save();
                }
            }
        }
        if (empty($this->sync_fail_info)) {
            if($wechat->wxmp){
                $wechat->wxmp->save(['template_enable' => 1]);
            }
            return true;
        }
        return true;
    }

    protected function get_template_id($item,$tpls){
        $tpl_id = '';
        foreach($tpls as $tpl){
            if($tpl['title'] == $item['weixin']['tpl_title']){
                $tpl_id = $tpl['template_id'];
                break;
            }
        }
        return $tpl_id;
    }

    //推送老师微信通知
    public static function wechat_tpl_notify_employee($scene, $msg_data, $employees)
    {
        $model = new self();

        //--1-- 模板信息链接字段检查
        $default_template_setting = config('tplmsg')[$scene];
        preg_match_all('/\{([^\}]+)\}/',$default_template_setting['weixin']['url'],$matches);
        if(isset($matches[1]) && !empty($matches[1])) {
            foreach($matches[1] as $field) {
                if($field == 'base_url') continue;

                if(!isset($msg_data[$field])) return $model->user_error($field.'模板信息链接字段不能为空');
            }
        }

        //--2-- 模板信息字段检查
        $temp = [];
        foreach($default_template_setting['tpl_fields'] as $field => $val) {
            if(!isset($msg_data[$field])) return $model->user_error($field.'模板信息字段不能为空');
            $temp[$field] = $msg_data[$field];
        }

        $wechat = Wechat::getInstance(Wechat::getAppidByBid(request()->bid));
        $message['appid'] = $wechat->appid;

        $message['url'] = tplmsg_url($default_template_setting['weixin']['url'],$msg_data);

        //--3-- 处理模板id
        if ($wechat->default) {
            $message['template_id'] = $default_template_setting['weixin']['template_id'];
        } else {
            $w = [];
            $w['appid'] = $message['appid'];
            $w['scene'] = $scene;
            $target_tpl = WxmpTemplate::get($w);
            if (empty($target_tpl)) {
                //该公众号还没有成功设置该模板.
                return $model->user_error('公众号还没有设置作业推送模板');
            }
            $message['template_id'] = $target_tpl['template_id'];
        }

        $user_template_setting = isset(Config::userConfig()['wechat_template'][$scene]) ? Config::userConfig()['wechat_template'][$scene] : null;
        if (empty($user_template_setting)) {
            //客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置
            $user_template_setting = $default_template_setting;
        }

        //--4-- 模板消息内容替换
        $search  = array_values($user_template_setting['tpl_fields']);
        $replace = array_values($temp);

        $data = $user_template_setting['weixin']['data'];
        foreach ($data as &$subject) {
            $subject = str_replace($search, $replace, $subject);
        }
        $sms_message = str_replace($search, $replace, $user_template_setting['sms']['tpl']);
        $message['data'] = $data;

        //--5-- 准备发送消息
        $inner_message = [];
        $inner_message['business_type'] = $scene;
        $inner_message['business_id'] = isset($msg_data['business_id']) ? $msg_data['business_id'] : 0;
        $inner_message['title']   = $default_template_setting['message']['title'];
        $inner_message['content'] = str_replace($search, $replace, $default_template_setting['message']['content']);
        $inner_message['url']     = $message['url'];
        foreach ($employees as $employee) {
            if(!($employee instanceof Employee)) {
                $employee = Employee::get($employee['eid']);
            }
            $user = $employee->user;
            if(empty($user)) continue;
            /** @var User $per_user */
            if(empty($user->getData())) continue;

            $inner_message['uid'] = $user['uid'];
            Message::create($inner_message);
            if ($user['mobile'] && $user_template_setting['sms_switch']) {
                queue_push('SendSmsMsg', [$user['mobile'], $sms_message]);
            }
            if ($user['openid'] && $user_template_setting['weixin_switch']) {
                $w = [];
                $w['openid'] = $user['openid'];
                $w['subscribe'] = WxmpFans::SUBSCRIBE;
                if (WxmpFans::get($w)) {
                    $message['openid'] = $user['openid'];
                    queue_push('SendWxTplMsg', $message);
                }
            }

        }

        return true;
    }

    //推送学生微信通知
    public static function wechat_tpl_notify_student($scene, $msg_data, $students)
    {
        $model = new self();

        //--1-- 模板信息链接字段检查
        $default_template_setting = config('tplmsg')[$scene];
        preg_match_all('/\{([^\}]+)\}/',$default_template_setting['weixin']['url'],$matches);
        if(isset($matches[1]) && !empty($matches[1])) {
            foreach($matches[1] as $field) {
                if($field == 'base_url') continue;

                if(!isset($msg_data[$field])) return $model->user_error($field.'模板信息链接字段不能为空');
            }
        }

        //--2-- 模板信息字段检查
        $temp = [];
        foreach($default_template_setting['tpl_fields'] as $field => $val) {
            if(!isset($msg_data[$field])) return $model->user_error($field.'模板信息字段不能为空');
            $temp[$field] = $msg_data[$field];
        }

        $wechat = Wechat::getInstance(Wechat::getAppidByBid(request()->bid));
        $message['appid'] = $wechat->appid;

        $message['url'] = tplmsg_url($default_template_setting['weixin']['url'],$msg_data);

        //--3-- 处理模板id
        if ($wechat->default) {
            $message['template_id'] = $default_template_setting['weixin']['template_id'];
        } else {
            $w = [];
            $w['appid'] = $message['appid'];
            $w['scene'] = $scene;
            $target_tpl = WxmpTemplate::get($w);
            if (empty($target_tpl)) {
                //该公众号还没有成功设置该模板.
                return $model->user_error('公众号还没有设置作业推送模板');
            }
            $message['template_id'] = $target_tpl['template_id'];
        }

        $user_template_setting = isset(Config::userConfig()['wechat_template'][$scene]) ? Config::userConfig()['wechat_template'][$scene] : null;
        if (empty($user_template_setting)) {
            //客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置
            $user_template_setting = $default_template_setting;
        }

        //--4-- 模板消息内容替换
        $search  = array_values($user_template_setting['tpl_fields']);
        $replace = array_values($temp);

        $data = $user_template_setting['weixin']['data'];
        foreach ($data as &$subject) {
            $subject = str_replace($search, $replace, $subject);
        }
        $sms_message = str_replace($search, $replace, $user_template_setting['sms']['tpl']);
        $message['data'] = $data;

        //--5-- 准备发送消息
        $inner_message = [];
        $inner_message['business_type'] = $scene;
        $inner_message['business_id'] = isset($msg_data['business_id']) ? $msg_data['business_id'] : 0;
        $inner_message['title']   = $default_template_setting['message']['title'];
        $inner_message['content'] = str_replace($search, $replace, $default_template_setting['message']['content']);
        $inner_message['url']     = $message['url'];
        foreach ($students as $student) {
            if(!($student instanceof Student)) {
                if(!empty($student['sid'])) {
                    $student = Student::get($student['sid']);
                } elseif(is_int($student)) {
                    $student = Student::get($student);
                }
            }
            if(empty($student)) continue;

            $users = $student->user;
            if(empty($users)) continue;
            /** @var User $per_user */
            foreach($users as $per_user) {
                if(empty($per_user->getData())) continue;

                $inner_message['uid'] = $per_user['uid'];
                $inner_message['sid'] = $student->sid;
                Message::create($inner_message);
                if ($per_user['mobile'] && $user_template_setting['sms_switch']) {
                    queue_push('SendSmsMsg', [$per_user['mobile'], $sms_message]);
                }
                if ($per_user['openid'] && $user_template_setting['weixin_switch']) {
                    $w = [];
                    $w['openid'] = $per_user['openid'];
                    $w['subscribe'] = WxmpFans::SUBSCRIBE;
                    if (WxmpFans::get($w)) {
                        $message['openid'] = $per_user['openid'];
                        queue_push('SendWxTplMsg', $message);
                    }
                }
            }

        }

        return true;
    }
    /**
     * 根据Appid和场景获得模板ID
     * @param  [type] $appid [description]
     * @param  [type] $scene [description]
     * @return [type]        [description]
     */
    public function getTemplateIdByAppidAndScene($appid,$scene){
        $w['appid'] = $appid;
        $w['scene'] = $scene;
        $m_wt = $this->where($w)->find();
        if($m_wt){
            return $m_wt->template_id;
        }
        return '';
    }

}