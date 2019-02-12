<?php

namespace app\wxapi\controller;

use app\api\controller\Base;
use app\common\Wechat;
use think\Request;

class Jssdk extends Base
{
    public $apiAuth = false;
	/**
	 * 获得跳转
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function Index(Request $request)
    {
		$url = isset($_GET['url']) ? $_GET['url'] : isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

		$kv_k = '0:JSAPI:'.md5($url);

		$config = cache($kv_k);

		if(!$config){
			$js = Wechat::getApp()->js;
			if($url){
				$js->setUrl($url);
			}
			$api_list = array(
			    'checkJsApi',
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'hideAllNonBaseMenuItem',
                'showAllNonBaseMenuItem',
                'chooseImage',
                'previewImage',
                'uploadImage',
            );

			$config = $js->config($api_list, false, false, false);

			cache($kv_k,$config,7200);
		}
		
		$ret_type = isset($_GET[config('var_jsonp_handler')])?'JSONP':'JSON';

		if($ret_type == 'JSONP'){
			return jsonp($config);
		}
		return json($config);
	}
}