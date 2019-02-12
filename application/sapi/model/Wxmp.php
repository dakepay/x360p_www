<?php
/**
 * Author: luo
 * Time: 2017-12-23 12:19
**/

namespace app\sapi\model;

class Wxmp extends Base
{
    public $type = [
        'enable'        => 'boolean',
        'business_info' => 'json',
        'func_info'     => 'json',
        'is_default'    => 'boolean',
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

    protected $append = ['payment'];

    public function getPaymentAttr($value, $data)
    {
        $payment = [];
        $payment['merchant_id'] = isset($data['merchant_id']) ? $data['merchant_id'] : '';
        $payment['key']         = isset($data['key']) ? $data['key'] : '';
        $payment['cert_path']   = isset($data['cert_path']) ? $data['cert_path'] : '';//todo 证书路径
        $payment['key_path']    = isset($data['key_path']) ? $data['key_path'] : '';//todo 证书路径
    }

    public function setCertPathAttr($value)
    {
        $prefix = request()->server('DOCUMENT_ROOT') . '/public/data/cert/';
        return $prefix . $value;
    }

    public function setKeyPathAttr($value)
    {
        $prefix = request()->server('DOCUMENT_ROOT') . '/public/data/cert/';
        return $prefix . $value;
    }

    public function getTemplateMessageConfigAttr($value)
    {
        $default_config = config('tplmsg');
        $wxmp_config = json_decode($value, true);
        return array_merge($default_config, $wxmp_config);
    }

    public function menus()
    {
        return $this->hasMany('WxmpMenu', 'wxmp_id', 'wxmp_id');
    }

    public static function authorized(array $data)
    {
        $w = [];
        $w['authorizer_appid'] = $data['authorizer_appid'];
        $model = self::get($w);
        if (empty($model)) {
            $model = new self();
        }
        if (empty($data['bids'])) {
            $data['is_default'] = true;
            self::update(['is_default' => false], ['wxmp_id' => ['>', 0]]);
        } else {
            $data['is_default'] = false;
        }
        $model->allowField(true)->save($data);
        if (!empty($data['bids'])) {
            $bids = explode(',', $data['bids']);
            Branch::whereIn('bid', $bids)->update(['appid' => $data['authorizer_appid']]);
        }
    }

    public static function unauthorized($appid)
    {
        $w = [];
        $w['authorizer_appid'] = $appid;
        self::destroy($w, true);
        Branch::update(['appid' => ''], ['appid' => $appid]);
    }

    public function getPublicAccounts()
    {
        $list = $this->field(['authorizer_access_token', 'authorizer_refresh_token', 'func_info', 'business_info', 'key', 'cert_path', 'key_path'], true)
            ->where('status', 0)
            ->select();
        $system_default = Authorizer::field(['authorizer_access_token', 'authorizer_refresh_token', 'func_info', 'business_info'], true)
            ->where('system_default', 1)
            ->where('status', 0)
            ->select();
        return array_merge($list, $system_default);
    }
}