<?php
/**
 * Author: luo
 * Time: 2018/6/7 17:03
 */

namespace app\api\controller;


use app\api\model\Message;
use think\Request;
use util\sms\EasySms;

class Messages extends Base
{

    public function get_list(Request $request)
    {
        $m_message = new Message();
        $get = $request->get();
        $ret = $m_message->getSearchResult($get);

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  发送短信
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function send_sms(Request $request)
    {
        $post = $request->post();
        if(empty($post['mobile'])) return $this->sendError(400, '电话号码不对');

        $tpl_id = empty($post['tpl_id']) ? '' : $post['tpl_id'];
        $tpl_data = empty($post['tpl_data']) ? [] : $post['tpl_data'];
        $content = empty($post['content']) ? [] : $post['content'];

        $rs = EasySms::Send($post['mobile'], $content, $tpl_id, $tpl_data);
        if($rs !== true) return $this->sendError(400, $rs);
        
        return $this->sendSuccess();
    }

}