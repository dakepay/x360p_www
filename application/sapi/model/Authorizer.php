<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/24
 * Time: 14:42
 */
namespace app\sapi\model;

class Authorizer extends Base
{
    protected $name = 'wxopen_authorizer';

    protected $connection = 'db_center';

    public $type = [
        'business_info' => 'json',
        'func_info'     => 'json',
    ];

    public function setServiceTypeInfoAttr($value)
    {
        if (is_array($value)) {
            return $value['id'];
        }
    }

    public function setVerifyTypeInfoAttr($value)
    {
        if (is_array($value)) {
            return $value['id'];
        }
    }


    public static function getByAppid($appid)
    {
        $w = [];
        $w['authorizer_appid'] = $appid;
        return self::get($w);
    }

    public function getPublicAccounts($cid)
    {
        $list = $this->where('cid', $cid)
            ->whereOr('system_default', 1)
            ->field(['authorizer_access_token', 'authorizer_refresh_token', 'func_info', 'business_info'], true)
            ->select();
        return $list;
    }
}