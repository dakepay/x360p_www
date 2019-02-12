<?php

namespace app\center\controller;

use think\Config;
use think\exception\ClassNotFoundException;
use think\exception\ValidateException;
use DawnApi\facade\Api;
use DawnApi\facade\Factory;
use DawnApi\contract\AuthContract;
use DawnApi\exception\UnauthorizedException;
use think\Loader;
use think\Request;
use think\Response;
use think\Exception;
use app\common\traits\ModelTrait;

class Base extends Api
{
    use ModelTrait;

    private static $_global_vars = [];
    protected $request;
    // 验证失败是否抛出异常
    protected $failException = false;
    // 是否批量验证
    protected $batchValidate = false;

    //是否开启授权认证
    public $apiAuth = true;


    //不需要认证的action
    protected $withoutAuthAction = [];

    //不需要验证权限的action
    protected $withoutPowerAction = [];

    /**
     * 前置操作方法列表
     * @var array $beforeActionList
     * @access protected
     */
    protected $beforeActionList = [];

    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct(Request $request = null)
    {
        if (is_null($request)) {
            $request = Request::instance();
        }
        $this->request = $request;

        // 控制器初始化
        $this->_init();

        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                    $this->beforeAction($options) :
                    $this->beforeAction($method, $options);
            }
        }

        $this->_auth();

        $this->_begin();

    }

    /**
     * 控制器初始化操作
     * @return [type] [description]
     */
    protected function _init(){

    }

    /**
     * 控制器初始化操作
     * @return [type] [description]
     */
    protected function _begin(){

    }

    /**
     * 认证用户
     * @return [type] [description]
     */
    protected function _auth(){
        $this->_register();
        $response = null;
        try {
            if (self::getConfig('api_debug')) {
                $auth = (self::getConfig('api_auth') && $this->apiAuth) ? self::auth() : true;
                if ($auth !== true) throw new UnauthorizedException();
            } else{
                /**
                 * 配置开启并且控制器开启后执行验证程序
                 * 认证授权通过  return true,
                 * 不通过可返回 return false or throw new AuthException
                 */
                //认证
                $auth = (self::getConfig('api_auth') && $this->apiAuth) ? self::auth() : true;
                if ($auth !== true) throw new UnauthorizedException();
            }
        } catch (UnauthorizedException $e) {
            //授权认证失败
            $response = $this->sendError(401, $e->getMessage(), 401, [], []);
        } catch (Exception $e) {

            //其他错误 返回500
            $response = $this->sendError(500, 'server error'.$e->getMessage(), 500);
        }

        if (!is_null($response)) {
            $response->send();
            exit;
        }
    }


    /**
     * 注册必要的
     */
    protected function _register()
    {
        //初始化配置
        $rs = self::getConfig();
        //授权器
        if (self::getConfig('api_auth') && $this->apiAuth) self::getAuth();
    }

    /**
     * 获取授权
     * @return mixed
     */
    private static function getAuth()
    {
        if (!isset(self::$app['auth']) || !self::$app['auth']) {
            $auth = self::getConfig('auth_class');
            //支持数组配置
            //判断是否实现验证接口
            if (((new \ReflectionClass($auth))->implementsInterface(AuthContract::class)))
                self::$app['auth'] = Factory::getInstance($auth);
        }
        return self::$app['auth'];
    }

    /**
     * 授权验证
     * @throws AuthException
     */
    private static function auth()
    {
        $baseAuth = Factory::getInstance(\DawnApi\auth\BaseAuth::class);
        try {
            $baseAuth->auth(self::$app['auth']);
        } catch (UnauthorizedException $e) {
            throw  new  UnauthorizedException($e->authenticate, $e->getMessage());
        } catch (Exception $e) {
            throw  new  Exception('server error'.$e->getMessage(), 500);
        }
        return true;
    }



    /**
     * 前置操作
     * @access protected
     * @param string $method  前置操作方法名
     * @param array  $options 调用参数 ['only'=>[...]] 或者['except'=>[...]]
     */
    protected function beforeAction($method, $options = [])
    {
        if (isset($options['only'])) {
            if (is_string($options['only'])) {
                $options['only'] = explode(',', $options['only']);
            }
            if (!in_array($this->request->action(), $options['only'])) {
                return;
            }
        } elseif (isset($options['except'])) {
            if (is_string($options['except'])) {
                $options['except'] = explode(',', $options['except']);
            }
            if (in_array($this->request->action(), $options['except'])) {
                return;
            }
        }

        call_user_func([$this, $method]);
    }







    /**
     * 成功响应
     * @param array $data
     * @param string $message
     * @param int $code
     * @param array $headers
     * @param array $options
     * @return Response|\think\response\Json|\think\response\Jsonp|Redirect|\think\response\Xml
     */
    public function sendSuccess($data = [], $message = 'success', $code = 200, $headers = [], $options = [])
    {
        $responseData['error'] = 0;
        $responseData['message'] = (string)$message;
        if (!empty($data)){
            $responseData['data'] = $data;
        }else{
            $responseData['data']  = [];
        }
        $responseData = array_merge($responseData, $options);
        $token = gvar('token');
        if($token){
            $responseData['authed'] = true;
        }
        return $this->response($responseData, $code, $headers);
    }


    /**
     * 发送确认消息
     * @param  string $text [description]
     * @return [type]       [description]
     */
    public function sendConfirm($text = '',$headers = []){
        $responseData['error'] = 1;
        $responseData['confirm'] = (string)$text;

        $token = gvar('token');
        if($token){
            $responseData['authed'] = true;
        }

        return $this->response($responseData,200,$headers);

    }




}
