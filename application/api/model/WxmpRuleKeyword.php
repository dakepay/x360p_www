<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/21
 * Time: 14:25
 */
namespace app\api\model;

class WxmpRuleKeyword extends Base
{
    public function rule()
    {
        return $this->belongsTo('WxmpRule', 'rule_id', 'rule_id');
    }

    protected $skip_og_id_condition = true;
}