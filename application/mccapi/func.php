<?php
use EasyWeChat\Foundation\Application;
/**
 * 获得小程序的实例
 * @param  string $developer 开发者
 * @return [type]            [description]
 */
function mapp_instance($developer = 'production'){
	static $mapp_instances = [];
	if(isset($mapp_instances[$developer])){
		return $mapp_instances[$developer];
	}

	$mapp_config = config('mcc.'.$developer);
	$options = [
		'mini_program'	=> $mapp_config
	];
	$app = new Application($options);
	$mapp = $app->mini_program;

	$mapp_instances[$developer] = $mapp;
	return $mapp;
}