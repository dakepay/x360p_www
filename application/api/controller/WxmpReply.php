<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/20
 * Time: 16:24
 */
namespace app\api\controller;

use app\common\Wechat;
use think\Request;

class WxmpReply extends Base
{
    public function get_list(Request $request)
    {
        $reply = Wechat::getApp()->reply;
        $reply_list = $reply->current();
        return $this->sendSuccess($reply_list);
    }

    public function index(Request $request)
    {

    }
}