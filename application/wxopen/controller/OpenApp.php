<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/24
 * Time: 11:08
 */

namespace app\wxopen\controller;

use app\api\controller\Base;
use EasyWeChat\Foundation\Application;

class OpenApp extends Base
{
    const WX_DISPLAY_CASE_APPID = 'wx570bc396a51b8ff8';/*微信全网发布测试公众号appid*/

    public $apiAuth = false;

    public $openApp = null;

    public $openPlatform = null;

    protected function _init()
    {
        $options = config('wxopen');
        $this->openApp = new Application($options);
        $this->openPlatform = $this->openApp->open_platform;
    }
}