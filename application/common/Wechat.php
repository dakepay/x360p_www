<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/28
 * Time: 17:07
 */
namespace app\common;

use app\api\model\Authorizer;
use app\api\model\Branch;
use app\api\model\DatabaseConfig;
use app\api\model\Wxmp;
use EasyWeChat\Foundation\Application;
use think\Config;
use think\Exception;
use think\Log;
//use think\Model;

class Wechat
{
    public static $instance = null;

    protected $option = [];

    protected function __construct($options = [])
    {
        $this->option = $options;
    }

    /**
     * 根据校区ID获得公众号appid
     * @param  [type] $bid [description]
     * @return [type]      [description]
     */
    public static function getAppidByBid($bid)
    {
        $branch = Branch::get($bid);
        if(empty($branch)) return null;
        $appid = $branch['appid'];

        return empty($appid) ? null : $appid;
    }

    /**
     * 根据各种输入条件获取公众号的appid
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public static function getAppid($input = null)
    {
        $appid = '';
        if (!is_null($input)) {
            if (is_string($input)) {
                $appid = $input;
            } 
            if(is_array($input) || $input instanceof \think\Model){
                $bid = isset($input['bid'])? $input['bid']:0;
                if($bid){
                    $appid = self::getAppidByBid($bid);
                }
            }
        } else {
            if (input('appid')) {
                $appid = input('appid');
            }elseif($bid = input('bid')) {
                if ($bid) {
                   $appid = self::getAppidByBid($bid);
                }
            }
        }
        if (empty($appid)) {
            $where = ['is_default' => 1];
            if(isset($input['og_id'])) {
                $where['og_id'] = $input['og_id'];
            }else{
                $where['og_id'] = gvar('og_id');
            }

            $wxmp = Wxmp::get($where);
            if ($wxmp) {
                /*客户自己的默认公众号*/
                $appid = $wxmp['authorizer_appid'];
            } else {
                /*系统默认的公众号*/
                $authorization_info = Authorizer::get(['system_default' => 1]);
                $appid = $authorization_info['authorizer_appid'];
            }
        }
        return $appid;
    }

    public static function getInstance($appid = null)
    {
        $appid = self::getAppid($appid);

        if (is_null(self::$instance[$appid])) {
            $config  = config('wxopen');
            $openApp = new Application($config);
            $openPlatform = $openApp->open_platform;

            $options = [];
            $options['appid']   = $appid;
            $options['openApp'] = $openApp;
            $options['openPlatform'] = $openPlatform;
            $options['default'] = false;

            $w = [];
            $w['authorizer_appid'] = $options['appid'];
            $w['is_delete'] = 0;
            $authorization_info = db('wxopen_authorizer',config('db_center'))->where($w)->find();
            //$authorization_info = Authorizer::get($w);

            if (empty($authorization_info)) {
                throw new Exception('没有查询到appid对应的公众号的授权信息,请确认是否授权!'.$options['appid']);
            } elseif ($authorization_info['system_default']) {
                $options['default'] = true;
            }
//            else {
//                if (!empty(request()->client['cid']) && request()->client['cid'] !== $authorization_info['cid']) {
//                    throw new Exception('非法的请求！');
//                }
//            }

            $options['authorization_info'] = $authorization_info;

            if (!$options['default']) {
                /*不是系统默认公众号*/
                $client_info = db('client','db_center')->where('cid',$authorization_info['cid'])->find();
                if($client_info['parent_cid'] != 0){
                    $cid = $client_info['parent_cid'];
                }else{
                    $cid = $authorization_info['cid'];
                }
                $db_config = db('database_config',config('db_center'))->where('cid', $cid)->find();
                if (empty($db_config)) {
                    throw new Exception('没有查询到数据库配置信息!cid:' . $authorization_info['cid']);
                }
                Config::set('database', $db_config);
                $wxmp = Wxmp::get(['authorizer_appid' => $options['appid']]);
                $options['wxmp'] = $wxmp;
            }

            $options['app'] = $openPlatform->createAuthorizerApplication($appid, $authorization_info['authorizer_refresh_token']);
            self::$instance[$appid] = new static($options);
        }
        return self::$instance[$appid];
    }

    public static function getApp($appid = null)
    {
        return self::getInstance($appid)->app;
    }

    public static function getSystemDefaultTemplates()
    {
        $notice = self::getSystemDefaultApp()->notice;
        return $notice->getPrivateTemplates()['template_list'];
    }

    public static function getSystemDefaultApp()
    {
        $config  = config('wxopen');
        $openApp = new Application($config);
        $openPlatform = $openApp->open_platform;
        $w['system_default'] = 1;
        $w['status'] = 0;
        $authorization_info = Authorizer::get($w);
        if (empty($authorization_info)) {
            throw new Exception('系统默认的公众号不存在!');
        }
        $authorizerAppId = $authorization_info['authorizer_appid'];
        $authorizerRefreshToken = $authorization_info['authorizer_refresh_token'];
        return $openPlatform->createAuthorizerApplication($authorizerAppId, $authorizerRefreshToken);
    }

    public function __get($name)
    {
        if (isset($this->option[$name])) {
            return $this->option[$name];
        } else {
            return null;
        }
    }
}