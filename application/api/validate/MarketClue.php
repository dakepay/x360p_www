<?php

namespace app\api\validate;

use think\Validate;

class MarketClue extends Validate
{
	protected $rule = [
        'name'  =>  'require|max:25',
        'tel'   =>  'require|number',
	];

	protected $message = [
        'name.require' => '姓名不得为空',
        'name.max'     => '姓名长度不超过25个字符',
        'tel.require'  => '电话不得为空',
        'tel.number'   => '电话必须为数字',
	];
}