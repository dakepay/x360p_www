<?php
namespace app\ftapi\model;

use think\Log;

class Authorizer extends Base
{
    protected $name = 'wxopen_authorizer';

    protected $connection = 'db_center';

    public $type = [
        'business_info' => 'json',
        'func_info'     => 'json',
    ];

    protected $skip_og_id_condition = true;

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

    /*获取系统默认的公众号（学习管家服务号）*/
    public static function getSystemDefault()
    {
        return self::get(['system_default' => 1]);
    }

    public static function getByAppid($appid)
    {
        $w = [];
        $w['authorizer_appid'] = $appid;
        return self::get($w);
    }

    /**
     * 授权成功通知
     * 保存[公众号的授权信息]和[公众号的基本信息]
     */
    public static function authorized($data)
    {
        if(isset($data['service_type_info']) && $data['service_type_info']['id'] != 2) {
            Log::record($data, 'error');
            Log::record('不是服务号，绑定不了', 'error');
            return '不是服务号，绑定不了';
        }

        if(isset($data['verify_type_info']) && $data['verify_type_info'] < 0) {
            log_write('还没认证，目前类型：' . $data['verify_type_info'], 'error');
            return '还没认证';
        }

        $w = [];
        $w['authorizer_appid'] = $data['authorizer_appid'];
        $model = self::get($w);
        if (empty($model)) {
            $model = new self();
        } else {
            if ($model['cid'] !== $data['cid']) {
                Log::record('该公众号已经绑定到其他机构了,不能重复绑定!' . $data['cid'], 'error');
                return '该公众号已经绑定到其他机构了,不能重复绑定!';
            }
        }

        try {
            $data['status'] = 0;
            $model->allowField(true)->save($data);
            if (!empty($data['cid'])) {
                $client_info = db('client','db_center')->where('cid',$data['cid'])->find();
                $data['og_id'] = $client_info['og_id'];
                if($client_info['parent_cid'] != 0){
                    $cid = $client_info['parent_cid'];
                }else{
                    $cid = $data['cid'];
                }
                $db_config = DatabaseConfig::withTrashed()->where(['cid' => $cid])->find();
                config('database', $db_config->toArray());
                Wxmp::authorized($data);
            }

        } catch (\Exception $exception) {
            Log::record($exception->getMessage(), 'error');
            return $exception->getMessage();
        }
        return true;
    }

    public static function unauthorized($appid)
    {
        $model = self::getByAppid($appid);
        if (empty($model)) {
            return;
        }
        $cid = $model['cid'];
        $model->delete(true);
        $client_info = db('client','db_center')->where('cid',$cid)->find();
        if($client_info['parent_cid'] != 0){
            $cid = $client_info['parent_cid'];
        }else{
            $cid = $client_info['cid'];
        }
        $db_config = DatabaseConfig::get(['cid' => $cid]);
        config('database', $db_config->toArray());
        Wxmp::unauthorized($appid);
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