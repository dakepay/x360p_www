<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/2
 * Time: 11:52
 */
namespace app\common\notification;

use app\api\model\Message;
use think\Log;

class PaySuccess extends Notification
{
    protected $config_name = 'pay_success';

    private $notify;

    /*微信支付成功的回调返回的信息*/
    public function setNotify($notify)
    {
        $this->notify = $notify;
    }

    public function wechatPush()
    {
        $tpl_data = $this->config['weixin'];
        $tpl_data['openid'] = $this->notify['openid'];

        $content = [];
        $content['pay_amount'] = $this->notify['total_fee'] / 100;
        $content['course_info'] = join('\n', $this->business->getGoodsTitles());
        $content['out_trade_no'] = $this->notify['out_trade_no'];

        $search  = array_values($this->config['tpl_fields']);
        /*动态生成模板消息的url*/
        array_push($search, '{base_url}');
        array_push($search, '{oid}');

        $replace = array_values(array_merge($this->config['tpl_fields'], $content));
        array_push($replace, request()->domain());
        array_push($replace, $this->business->oid);
        $data = str_replace_json($search, $replace, $tpl_data);
        Log::record($data);
        Log::record('------------------发送订单支付成功的模板消息--------------------------');
        queue_push('SendWxTplMsg', $data);
//        $this->sendWechatTplMsg($data);

//        $message_field['content'] = $content;
        $message_field['content'] = str_replace_json($search, $replace, $this->config['sms']['tpl']);
        $this->addInternalMessage($message_field);
        return true;
    }

    protected function addInternalMessage($field)
    {
        $data['bid'] = $this->business['bid'];
        $data['uid'] = $this->business['uid'];
//        $data['cid'] = $this->business['cid'];
        $data['business_type'] = $this->config['tpl_id'];
        $data['business_id'] = $this->business['oid'];
        $data['send_mode'] = $this->getSendMode();
        $data['title'] = $this->config['name'];
        $data = array_merge($data, $field);
        Message::create($data);
    }


    public function smsPush()
    {

    }
}