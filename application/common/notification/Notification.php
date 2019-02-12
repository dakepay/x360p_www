<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/2
 * Time: 11:51
 */
namespace app\common\notification;

use app\api\model\Base;
use app\api\model\Wxmp;
use EasyWeChat\Foundation\Application;
use think\Cache;

abstract class Notification
{
    protected $template_name;

    protected $template_config;

    protected $business;

    protected $wxmp;

    public $error;

    public function __construct(Base $business)
    {
        $this->business = $business;
        $this->set_wxmp($business['bid']);
        $this->set_template_config($this->wxmp);
    }

    public function run()
    {
        if ($this->template_config['sms_switch']) {
            $this->smsPush();
        }

        if ($this->template_config['weixin_switch']) {
            $this->wechatPush();
        }
    }

    abstract protected function wechatPush();

    abstract protected function smsPush();

    abstract protected function addInternalMessage($field);

    /*同步测试*/
//    protected function sendWechatTplMsg($data)
//    {
//        $wxapp = new Application(config('wechat'));
//        $notice = $wxapp->notice;
//        $messageId = $notice->send([
//            'touser' => $data['openid'],
//            'template_id' => $data['template_id'],
//            'url' => $data['url'],
//            'data' => $data['data'],
//        ]);
//        if ($messageId) {
//            return $messageId;
//        }
//        return false;
//    }

    /*插入站内信(message)的时候判断send_mode字段值*/
    protected function getSendMode()
    {
        if ($this->template_config['sms_switch'] && $this->template_config['weixin_switch']) {
            return 3;
        } elseif ($this->template_config['sms_switch']){
            return 2;
        } elseif ($this->template_config['weixin_switch']) {
            return 1;
        }
        return 0;
    }

    protected function set_wxmp($bid)
    {
        $cid = config('g_client.cid');
        $cache_key = "client:{$cid}:branch:{$bid}:wxmp";
        $wxmp = Cache::get($cache_key);
        if ($wxmp) {
            $this->wxmp = $wxmp;
            return $wxmp;
        }
        $wxmp = Wxmp::get(['bid' => $bid]);
        if (!$wxmp) {
            $wxmp = Wxmp::get(['bid' => 0]);
        }
        //todo 如何保证用户微信相关的配置正确
        if (!$wxmp) {
            throw new \Exception('还没有进行微信公众号的配置，请联系管理员!');
        }
        if (empty($wxmp['template_message_config'])) {
            throw new \Exception('还没有配置微信模板消息，请联系管理员!');
        }
        Cache::set($cache_key, $wxmp);
        $this->wxmp = $wxmp;
        return $wxmp;
    }

    protected function set_template_config($wxmp)
    {
        $default_template_config = config('tplmsg');
        $user_template_config    = array_merge($default_template_config, $wxmp['template_message_config']);
        $this->template_config   = $user_template_config[$this->template_name];
        return $this->template_config;
    }

}