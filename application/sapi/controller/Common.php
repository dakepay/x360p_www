<?php
/**
 * Author: luo
 * Time: 2018/3/24 11:40
 */

namespace app\sapi\controller;

use app\common\job\TransferMedia;
use think\Log;
use think\Request;

/**
 * Class Common
 * @package app\sapi\controller
 * @desc 公用方法类
 */
class Common extends Base
{


    /**
     * 在微信h5页面调用jssdk上传图片到微信服务器后通过media_id保存用户的图片到七牛
     * @param Request $request
     */
    public function wx_download_file(Request $request)
    {
        $input = $request->post();
        if (empty($input['media_id']) || empty($input['msg_type'])) {
            return $this->sendError(400, '参数不合法!');
        }
        $data['MediaId'] = $input['media_id'];
        $data['MsgType'] = $input['msg_type'];
        $data['uid'] = $request->user['uid'];
        $wx_util = new TransferMedia();
        $res = $wx_util->sync_transfer($data);
        if ($res === false) {
            return $this->sendError(500, $wx_util->getError());
        }
        return $this->sendSuccess($res);
    }

}