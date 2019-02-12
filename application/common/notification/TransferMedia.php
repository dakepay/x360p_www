<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/10
 * Time: 14:37
 */
namespace app\common\notification;

use think\Db;
use think\Log;

/**
 * 用户创建了在公众号上传视频的会话后给用户一个模板消息提示
 * Class TransferMedia
 * @package app\common\notification
 */
class TransferMedia extends Notification
{
    protected $config_name = 'transfer_media';

    public function __construct($business)
    {
        /*命令行进程*/
        $this->business = $business;
        $this->set_wxmp($this->business);
        $this->set_template_config($this->wxmp);
    }

    protected function set_wxmp($data)
    {
        $where = [];
        $where['uid'] = $data['uid'];
        $where['bid'] = $data['bid'];
        $where['openid'] = $data['openid'];
        $bind_record  = Db::connect($data['client']['database'])->name('wechat_bind')->where($where)->find();
        if (empty($bind_record)) {
            $where['bid'] = 0;
            $bind_record  = Db::connect($data['client']['database'])->name('wechat_bind')->where($where)->find();
        }

        if ($bind_record) {
            $original_id = $bind_record['original_id'];
            $wxmp = Db::connect($data['client']['database'])->name('wxmp')->where(['original_id' => $original_id])->find();
            if ($wxmp) {
                $this->wxmp = $wxmp;
            } else {
                throw new \Exception('当前用户还没有在我们的系统中绑定微信公众号');
            }
        } else {
            throw new \Exception('当前用户还没有在我们的系统中绑定微信公众号');
        }
    }

    public function run()
    {
        $this->wechatPush();
    }

    public function wechatPush()
    {
        Log::record('notification============', 'debug');
        $tpl_data = $this->template_config['weixin'];
        $data = array_merge($tpl_data, $this->business);
        $data['wxmp'] = $this->wxmp->toArray();
        Log::record($data, 'debug');
        queue_push('SendWxTplMsg', $data);
    }

    public function smsPush()
    {
        // TODO: Implement smsPush() method.
    }

    public function addInternalMessage($field)
    {
        // TODO: Implement addInternalMessage() method.
    }
}