<?php
namespace app\api\validate;

use think\Validate;

class MarketChannel extends Validate
{
	// 验证规则
    protected $rule = [
        ['channel_name|渠道名', 'require|unique:market_channel,channel_name^bid'],
    ];

    protected $scene = [
        'post' => ['channel_name'],
        'edit' => ['channel_name|渠道名', 'require|unique:market_channel,channel_name^mc_id']
    ];




}