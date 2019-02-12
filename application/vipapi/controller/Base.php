<?php

namespace app\vipapi\controller;

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
use app\api\model\Base as BaseModel;
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
    /**
     * 允许访问的请求类型
     * @var string
     */
    public $restMethodList = 'get|post|put|delete|patch|head|options';

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

        $is_confirm = $request->get('force') == 1;
        $request->bind('confirm',$is_confirm);

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

        $action = $request->action();

        $rest_action = explode('|',$this->restMethodList);

        $skip_auth_action = array_merge($rest_action,['restful']);

        if(!in_array($action,$skip_auth_action) && !in_array($action,$this->withoutAuthAction)){
            $this->_auth();
            $this->_power();
        }
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
        if (self::getConfig('api_debug')) {
            $auth = (self::getConfig('api_auth') && $this->apiAuth) ? self::auth() : true;
            if ($auth !== true) throw new UnauthorizedException();
        } else {
            $response = null;
            try {
                /**
                 * 配置开启并且控制器开启后执行验证程序
                 * 认证授权通过  return true,
                 * 不通过可返回 return false or throw new AuthException
                 */
                //认证
                $auth = (self::getConfig('api_auth') && $this->apiAuth) ? self::auth() : true;
                if ($auth !== true) throw new UnauthorizedException();
            } catch (UnauthorizedException $e) {
                //授权认证失败
                $response = $this->sendError(401, $e->getMessage(), 401, [], []);
            } catch (Exception $e) {
                //其他错误 返回500
                $response = $this->sendError(500, 'server error', 500);
            }

            if(!is_null($response)){
                $response->send();
                exit;
            }
        }
    }

    /**
     * 验证权限
     * @return [type] [description]
     */
    protected function _power(){

    }

    /**
     * 执行具体方法
     * @param $request
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    private function run(Request $request)
    {
        $action = $this->methodToAction[$this->method];
        $param  = $request->param();
        if($action == 'get'){
            $list_action   = $action.'_list';
            $detail_action = $action.'_detail';
            if(isset($param['id']) && method_exists($this,$detail_action)){
                $response = $this->$detail_action($request,$param['id']);
            }elseif(method_exists($this,$list_action)){
                $response = $this->$list_action($request);
            }else{
                $response = $this->$action($request);
            }
        }elseif (method_exists($this, $action)) {
            $response = $this->$action($request);
        } else {
            //执行空操作
            $response = $this->_empty($action);
        }
        return $response;
    }
    /**
     * 执行具体的子restful方法
     * @param  [type] $request [description]
     * @return [type]          [description]
     */
    private function run_sub(Request $request,$subres)
    {
        $action = $this->methodToAction[$this->method];
        $sub_default_action = $action.'_sub';
        $sub_res_action     = $action.'_'.$subres;
        $param  = $request->param();

        if($action == 'get'){
            $list_subres_action    = 'get_list_'.$subres;
            $detail_subres_aciton  = 'get_detail_'.$subres;

            $list_default_action   = 'get_list_sub';
            $detail_default_action = 'get_detail_sub';


            if(isset($param['subid'])){
                if(method_exists($this,$detail_subres_aciton)){
                    $response = $this->$detail_subres_aciton($request,$param['subid']);
                }else{
                    $response = $this->$detail_default_action($request,$param['subid'],$subres);
                }
            }else{
                if(method_exists($this,$list_subres_action)){
                    $response = $this->$list_subres_action($request);
                }else{
                    $response = $this->$list_default_action($request,$subres);
                }
            }
        }else{
            if(method_exists($this, $sub_res_action)) {
                $response = $this->$sub_res_action($request);
            }else{
                $response = $this->$sub_default_action($request,$subres);
            }
        }

        return $response;
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
            throw  new  Exception('server error', 500);
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



    protected function _init_restful()
    {
        // 资源类型检测
        $request = Request::instance();
        $ext = $request->ext();
        if ('' == $ext) {
            // 自动检测资源类型
            $this->type = $request->type();
        } elseif (!preg_match('/\(' . $this->restTypeList . '\)$/i', $ext)) {
            // 资源类型非法 则用默认资源类型访问
            $this->type = $this->restDefaultType;
        } else {
            $this->type = $ext;
        }

        //必要性的注册
        $this->_register();
        //设置响应类型
        $this->setType();

        // 请求方式检测
        $method = strtolower($request->method());
        $this->method = $method;
        if (false === stripos($this->restMethodList, $method)) {
            return false;
        }

        return true;

    }

    /**
     * 执行代码
     * @param Request $request
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws UnauthorizedException
     */
    public function restful(Request $request)
    {
        //检查方法是否允许等初始操作
        $init = $this->_init_restful();
        if ($init !== true) return $this->sendError(405, 'Method Not Allowed', 405, [], ["Access-Control-Allow-Origin" => $this->restMethodList]);
        try {
            if (self::getConfig('api_debug')) {
                $auth = (self::getConfig('api_auth') && $this->apiAuth) ? self::auth() : true;
                if ($auth !== true) throw new UnauthorizedException();
                //执行操作
                $response = $this->run($request);
            } else {
                    /**
                     * 配置开启并且控制器开启后执行验证程序
                     * 认证授权通过  return true,
                     * 不通过可返回 return false or throw new AuthException
                     */
                    //认证
                    $auth = (self::getConfig('api_auth') && $this->apiAuth) ? self::auth() : true;
                    if ($auth !== true) throw new UnauthorizedException();
                    //执行操作
                    $response = $this->run($request);
            }
        } catch (UnauthorizedException $e) {
            //授权认证失败
            $response = $this->sendError(401, $e->getMessage(), 401, [], $e->getHeaders());
        } catch (Exception $e) {
            //其他错误 返回500
            if(config('app_debug')){
                $response = $this->sendError(500, format_error($e), 500);
            }else{
                $response = $this->sendError(500,'server error',500);
            }
        }
        //清空之前输出 保证输出格式
        ob_end_clean();
        return $response;
    }

    /**
     * 子资源restful
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function restful_sub(Request $request,$subres = '',$id = 0)
    {
        $param  = $request->param();
        $method = strtolower($request->method());

        if(substr($subres,0,2) == 'do' && 'post' == $method){
            return $this->do($request,$subres,$id);
        }

        //检查方法是否允许等初始操作
        $init = $this->_init_restful();
        if ($init !== true) return $this->sendError(405, 'Method Not Allowed', 405, [], ["Access-Control-Allow-Origin" => $this->restMethodList]);;
        if (self::getConfig('api_debug')) {
            $auth = (self::getConfig('api_auth') && $this->apiAuth) ? self::auth() : true;
            if ($auth !== true) throw new UnauthorizedException();
            //执行操作
            $response = $this->run_sub($request,$subres);
        } else {
            try {
                /**
                 * 配置开启并且控制器开启后执行验证程序
                 * 认证授权通过  return true,
                 * 不通过可返回 return false or throw new AuthException
                 */
                //认证
                $auth = (self::getConfig('api_auth') && $this->apiAuth) ? self::auth() : true;
                if ($auth !== true) throw new UnauthorizedException();
                //执行操作
                $response = $this->run_sub($request,$subres);

            } catch (UnauthorizedException $e) {
                //授权认证失败
                $response = $this->sendError(401, $e->getMessage(), 401, [], $e->getHeaders());
            } catch (Exception $e) {
                //其他错误 返回500
                if(config('app_debug')){
                    $response = $this->sendError(500, format_error($e), 500);
                }else{
                    $response = $this->sendError(500,'server error',500);
                }

            }
            //清空之前输出 保证输出格式
            ob_end_clean();
        }
        return $response;
    }


    protected function get_res($pf = 'res'){
        $param = request()->param();
        $res = isset($param[$pf])?$param[$pf]:Loader::parseName(request()->controller());
        return $this->get_res_table($res);
    }

    protected function get_res_table($res){
        $special_res = [
            'classes'   => 'classes',
            'branches'  => 'branch',
            'goods'     => 'goods',
            'order_goods'   => 'order_goods',
        ];

        if(substr($res,-1) == 's'){
            if(isset($special_res[$res])){
                $table = $special_res[$res];
            }else{
                $table = substr($res,0,-1);
            }
        }else{
            $table = $res;
        }
        return $table;
    }

    protected function get_model($name = null){
        if(is_null($name)){
            $res    = $this->get_res();
        }else{
            $res    = $this->get_res_table($name);
        }
        try{
            $model = Loader::model($res);
        }catch(ClassNotFoundException $e){
            $tables = get_tables();
            if(!in_array($res,$tables)){
                return $this->sendError('404','resource_not_found:tables:'.print_r($tables,true).',res:'.$res);
            }
            $model = new BaseModel();
            $model->name($res);
        }
        return $model;
    }

    protected function get_list(Request $request){
        $model = $this->get_model();
        if($model instanceof Response){
            return $model;
        }
        $input = $request->request();
        $result = $model->getSearchResult($input);
        return $this->sendSuccess($result);
    }

    protected function get_detail(Request $request, $id = 0){
        $model = $this->get_model();
        if($model instanceof Response){
            return $model;
        }
        $obj = $model->find($id);
        return $this->sendSuccess($obj);
    }


    protected function get_list_sub(Request $request,$subres){
        $res_model = $this->get_model();
        if($res_model instanceof Response){
            return $res_model;
        }
        $sub_res_model = $this->get_model($subres);

        if($sub_res_model instanceof Response){
            return $sub_res_model;
        }

        $input = $request->request();
        $input[$res_model->getPk()] = $request->param('id');
        $result = $sub_res_model->getSearchResult($input);
        return $this->sendSuccess($result);

    }

    protected function get_detail_sub(Request $request,$subid,$subres){
        $sub_res_model = $this->get_model($subres);
        if($sub_res_model instanceof Response){
            return $sub_res_model;
        }
        $obj = $sub_res_model->find($request->param('subid'));
        return $this->sendSuccess($obj);

    }


    /**
     * 默认添加操作
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function post(Request $request){
        $model  = $this->get_model();
        if($model instanceof Response){
            return $model;
        }
        $module = request()->module();
        $validator_name = \Think\Loader::parseName($model->getResName(),1);

        if ($validator_name === 'Class') {
            $validator_name = 'Classes';
        }
        $validator_class = \Think\Loader::parseClass($module,'validate',$validator_name);
        $input  = input('post.');
        if(class_exists($validator_class)){
            $result = $this->validate($input,$validator_name);
            if ($result !== true) {
                return $this->sendError('400', $result);
            }
        }
        try{
            $result = $model->allowField(true)->save($input);
        }catch(\Exception $e){
            return $this->sendError('500',$e->getMessage());
        }
        return $this->sendSuccess($model->getPkValue());
    }


    /**
     * 默认添加操作
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function post_sub(Request $request,$subres){
        $model  = $this->get_model();
        if($model instanceof Response){
            return $model;
        }
        $sub_model = $this->get_model($subres);
        if($sub_model instanceof Response){
            return $sub_model;
        }

        $module = request()->module();
        $validator_name = \Think\Loader::parseName($sub_model->getResName(),1);
        $validator_class = \Think\Loader::parseClass($module,'validate',$validator_name);
        $input  = input('post.');
        $input[$model->getPk()] = $request->param('id');

        if(class_exists($validator_class)){
            $result = $this->validate($input,$validator_name);
            if ($result !== true) {
                return $this->sendError('400', $result);
            }
        }
        try{
            $sub_model->allowField(true)->save($input);
        }catch(\Exception $e){
            return $this->sendError('500',$e->getMessage());
        }
        return $this->sendSuccess();
    }

    /**
     * 默认修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function put(Request $request){
        $id = input('id/d');
        if (empty($id)) {
            return $this->sendError(400, 'invalid parameter id');
        }
        $model = $this->get_model();

        if($model instanceof Response){
            return $model;
        }

        $rs = $model->find($id);

        if (!$rs) {
            return $this->sendError(400, 'resource not exists');
        }


        $module = request()->module();
        $validator_name = \Think\Loader::parseName($model->getResName(),1);
        $validator_class = \Think\Loader::parseClass($module,'validate',$validator_name);
        $input  = input('put.');
        if(class_exists($validator_class)){
            $result = $this->validate($input,$validator_name);
            if ($result !== true) {
                return $this->sendError('400', $result);
            }
        }
        $result = $rs->allowField(true)->data($input, true)->isUpdate(true)->save();
        if ($result === false) {
            return $this->sendError(400, $rs->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 默认修改
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function put_sub(Request $request,$subres){
        $id = input('subid/d');
        if (empty($id)) {
            return $this->sendError(400, 'invalid parameter subid');
        }

        $model = $this->get_model($subres);

        if($model instanceof Response){
            return $model;
        }

        $rs = $model->find($id);

        if (!$rs) {
            return $this->sendError(400, 'resource not exists');
        }


        $module = request()->module();
        $validator_name = \Think\Loader::parseName($model->getResName(),1);
        $validator_class = \Think\Loader::parseClass($module,'validate',$validator_name);
        $input  = input('put.');
        if(class_exists($validator_class)){
            $reflector = new \ReflectionClass($validator_class);
            $instance = $reflector->newInstance();
            $scene = $reflector->getProperty('scene');
            $scene->setAccessible(true);
            $sceneList = $scene->getValue($instance);
            if (array_key_exists('edit', $sceneList)) {
                $validator_name .= '.edit';
            }
            $result = $this->validate($input,$validator_name);
            if ($result !== true) {
                return $this->sendError('400', $result);
            }
        }

        $result = $rs->allowField(true)->data($input, true)->isUpdate(true)->save();
        if ($result === false) {
            return $this->sendError(400, $rs->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 默认删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete(Request $request)
    {
        $id = input('id/d');
        $ids = input('ids/s');

        if (empty($id) && empty($ids)) {
            return $this->sendError(400, 'invalid parameter id');
        }

        $model = $this->get_model();
        if($model instanceof Response){
            return $model;
        }

        if($id){
            $rs = $model->find($id);
            if (!$rs) {
                return $this->sendError(400, 'resource not exists');
            }
            try{
                if(method_exists($rs,'singleDelete')){
                    $result = $rs->singleDelete();
                }else{
                    $result = $rs->delete();
                }
            }catch(Exception $e){
                return $this->sendError(500,$e->getMessage());
            }
            if ($result === false) {
                return $this->sendError(400, $rs->getError());
            }
        }else{

            if(method_exists($model,'batDelete')){
                $result = $model->batDelete($ids);
                if(!$result){
                    return $this->sendError(400,$model->getError());
                }
            }
        }

        return $this->sendSuccess('ok');
    }



    /**
     * 默认删除
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete_sub(Request $request,$subres)
    {
        $id = input('subid/d');
        if (empty($id)) {
            return $this->sendError(400, 'invalid parameter subid');
        }
        $model = $this->get_model($subres);
        if($model instanceof Response){
            return $model;
        }
        $rs = $model->find($id);
        if (!$rs) {
            return $this->sendError(400, 'resource not exists');
        }
        try{
            $result = $rs->delete();
        }catch(Exception $e){
            return $this->sendError(500,$e->getMessage());
        }
        if ($result === false) {
            return $this->sendError(400, $rs->getError());
        }
        return $this->sendSuccess('ok');
    }


    protected function do(Request $request,$action = '',$id = 0){
        $do_action = 'do_'.substr($action,2);

        if(method_exists($this, $do_action)){
            return $this->$do_action($request,$id);
        }else{
            return $this->sendError(400,sprintf('do action not exists! action:%s,id:%s',$do_action,$id));
        }
    }


    public function dosub(Request $request, $action = '', $subid = '', $subres = ''){
        $do_action = 'do_' . $subres . '_' . substr($action,2);
        if(method_exists($this, $do_action)) {
            return $this->$do_action($request , $subid);
        } else {
            return $this->sendError(400, sprintf('do action not exists! action:%s,subid:%s', $do_action, $subid));
        }
    }

    /**
     * 获得当前用户信息
     * @return [type] [description]
     */
    protected function getUserInfo(){
        return gvar('user');
    }

    /**
     * 验证数据
     * @access protected
     * @param array        $data     数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array        $message  提示信息
     * @param bool         $batch    是否批量验证
     * @param mixed        $callback 回调方法（闭包）
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate($data, $validate, $message = [], $batch = false, $callback = null)
    {
        if (is_array($validate)) {
            $v = Loader::validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            try{
                $v = Loader::validate($validate);
            }catch(ClassNotFoundException $e){
                $class = 'app\\api\\validate\\'.Loader::parseName($validate,1);
                if (class_exists($class)) {
                    $v = new $class;
                } else {
                    throw new ClassNotFoundException('class not exists:' . $class, $class);
                }
            }

            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        if (is_array($message)) {
            $v->message($message);
        }

        if ($callback && is_callable($callback)) {
            call_user_func_array($callback, [$v, &$data]);
        }

        if (!$v->check($data)) {
            if ($this->failException) {
                throw new ValidateException($v->getError());
            } else {
                return $v->getError();
            }
        } else {
            return true;
        }
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

    /**
     * 获得全局变量
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    protected function getGlobalVars($name = ''){
        $ret = [];

        if(!empty($name) && $name != 'all'){
            if(isset(self::$_global_vars[$name])){
                return self::$_global_vars[$name];
            }
            switch($name){
                case 'dbservers':
                    $ret = model('dbserver')->order('db_nums ASC')->select();
                    break;
                default:
                    break;
            }
            self::$_global_vars[$name] = $ret;
        }else{
            $global_vars = ['dbservers'];
            foreach($global_vars as $var){
                $ret[$var] = $this->getGlobalVars($var);
            }
        }
        return $ret;
    }

    /**
     * 获得所有字典列表
     * @return [type] [description]
     */
    protected function getDictLists(){
        $dict_fields = ['did','pid','name','title','desc','is_system','sort'];
        $top_dicts   = model('dictionary')->where(['pid'=>0])->field($dict_fields)->order('sort DESC')->select();
        $dict_id_map = [];
        $dicts = [];
        foreach($top_dicts as $d){

            $dict_id_map[$d['did']] = $d['name'];
            $dicts[$d['name']] = [];
        }


        $dict_items = model('dictionary')->where('pid','NEQ',0)->where('display',1)->order('sort DESC')->field($dict_fields)->select();


        foreach($dict_items as $item){
            if(isset($dict_id_map[$item['pid']])){
                $name = $dict_id_map[$item['pid']];
                if($name && isset($dicts[$name])){
                    array_push($dicts[$name],$item);
                }
            }
        }
        return $dicts;
    }


}
