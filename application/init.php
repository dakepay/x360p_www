<?php
use think\Config;
use think\Hook;
use think\Lang;
use think\Request;
use think\Db;

//定义数据存放路径
define('DATA_PATH',ROOT_PATH.'data'.DS);
define('PUBLIC_DATA_PATH',PUBLIC_PATH.'data'.DS);

if (IS_CLI) {
    load_cli_config();
} else {
    $domains   = @include(CONF_PATH.'domains.php');
    $ui_config = include(CONF_PATH.'extra'.DS.'ui.php');
    $http_host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

    if(!empty($http_host) && array_key_exists($http_host,$domains)){
        $_SERVER['HTTP_HOST'] = $domains[$http_host].'.'.$ui_config['domain'];
    }
    load_app_config();
}

function load_cli_config($module = '')
{
    // 定位模块目录
    $module = $module ? $module . DS : '';

    $path = APP_PATH . $module;
    // 加载模块配置
    $config = Config::load(CONF_PATH . $module . 'config' . CONF_EXT);
    // 读取数据库配置文件
//    $filename = CONF_PATH . $module . 'database' . CONF_EXT;
//    Config::load($filename, 'database');
    // 读取扩展配置文件
    if (is_dir(CONF_PATH . $module . 'extra')) {
        $dir   = CONF_PATH . $module . 'extra';
        $files = scandir($dir);
        foreach ($files as $file) {
            if ('.' . pathinfo($file, PATHINFO_EXTENSION) === CONF_EXT) {
                $filename = $dir . DS . $file;
                Config::load($filename, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }

    // 加载应用状态配置
    if ($config['app_status']) {
        $config = Config::load(CONF_PATH . $module . $config['app_status'] . CONF_EXT);
    }

    // 加载行为扩展文件
    if (is_file(CONF_PATH . $module . 'tags' . EXT)) {
        Hook::import(include CONF_PATH . $module . 'tags' . EXT);
    }

    // 加载公共文件
    if (is_file($path . 'common' . EXT)) {
        include $path . 'common' . EXT;
    }

    // 加载当前模块语言包
    if ($module) {
        Lang::load($path . 'lang' . DS . Request::instance()->langset() . EXT);
    }

    return Config::get();
}

function load_app_config($module = ''){
	$path = APP_PATH . $module;
    // 加载模块配置
    $config = Config::load(CONF_PATH . $module . 'config' . CONF_EXT);
    // 读取数据库配置文件
    $center_db_cfg_file = CONF_PATH . $module . 'center_database' . CONF_EXT;
    $center_db_cfg = Config::load($center_db_cfg_file, 'center_database');

    bind_client_request();
    //获得客户配置
    load_client_config();
    // 读取扩展配置文件
    if (is_dir(CONF_PATH . $module . 'extra')) {
        $dir   = CONF_PATH . $module . 'extra';
        $files = scandir($dir);
        foreach ($files as $file) {
            if ('.' . pathinfo($file, PATHINFO_EXTENSION) === CONF_EXT) {
                $filename = $dir . DS . $file;
                Config::load($filename, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }

    // 加载应用状态配置
    if ($config['app_status']) {
        $config = Config::load(CONF_PATH . $module . $config['app_status'] . CONF_EXT);
    }

    // 加载行为扩展文件
    if (is_file(CONF_PATH . $module . 'tags' . EXT)) {
        Hook::import(include CONF_PATH . $module . 'tags' . EXT);
    }

    // 加载公共文件
    if (is_file($path . 'common' . EXT)) {
        include $path . 'common' . EXT;
    }

    // 加载当前模块语言包
    if ($module) {
        Lang::load($path . 'lang' . DS . Request::instance()->langset() . EXT);
    }

    defined('APP_DEBUG') || define('APP_DEBUG',$config['app_debug']);
}
/**
 * 加载模块配置
 * @param  string $module [description]
 * @return [type]         [description]
 */
function load_module_config($module = ''){
    if(empty($module))return;
    $module = $module . DS ;
    $path = APP_PATH . $module ;
    // 加载模块配置
    $config = Config::load(CONF_PATH . $module . 'config' . CONF_EXT);
    // 读取数据库配置文件
    $filename = CONF_PATH . $module . 'database' . CONF_EXT;
    Config::load($filename, 'database');
    // 读取扩展配置文件
    if (is_dir(CONF_PATH . $module . 'extra')) {
        $dir   = CONF_PATH . $module . 'extra';
        $files = scandir($dir);
        foreach ($files as $file) {
            if ('.' . pathinfo($file, PATHINFO_EXTENSION) === CONF_EXT) {
                $filename = $dir . DS . $file;
                Config::load($filename, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }

    // 加载应用状态配置
    if ($config['app_status']) {
        $config = Config::load(CONF_PATH . $module . $config['app_status'] . CONF_EXT);
    }

    // 加载行为扩展文件
    if (is_file(CONF_PATH . $module . 'tags' . EXT)) {
        Hook::import(include CONF_PATH . $module . 'tags' . EXT);
    }

    // 加载公共文件
    if (is_file($path . 'common' . EXT)) {
        include $path . 'common' . EXT;
    }
    // 加载function文件
    if (is_file($path . 'func' . EXT)){
        include $path .'func'.EXT;
    }

    // 加载当前模块语言包
    if ($module) {
        Lang::load($path . 'lang' . DS . Request::instance()->langset() . EXT);
    }
}

/**
 * 解析客户访问的域名
 * @return [type] [description]
 */
function get_client_domain(){
    $defined_client_domain = Config::get('g_client_domain');
    if($defined_client_domain){
        return $defined_client_domain;
    }
    $uc = include CONF_PATH . 'extra' . DS . 'ui' . CONF_EXT;
    $client_domain = [
        'root'          => false,
        'pre'           => '',
        'is_business'   => false,
        'main'          => '',
        'terminal'      => ''
    ];

    $pre_domain = '';
    $http_host  = Request::instance()->host();

    if($http_host == $uc['domain']){
        $client_domain['root'] = true;
        $client_domain['pre']  = '';
    }else{
        if(($pos = strpos($http_host,$uc['domain'])) !== false){
            $pre_domain = substr($http_host,0,$pos-1);
            $client_domain['pre'] = $pre_domain;
        }
    }
    

    if($pre_domain != ''){
        if(strpos($pre_domain,'.') !== false){
            $arr_domain = explode('.',$pre_domain);
            $user_main_domain = array_pop($arr_domain);
            $user_terminal_domain = implode('.',$arr_domain);
        }else{
            $user_main_domain = $pre_domain;
            $user_terminal_domain = $uc['default'];
        }
        $client_domain['main']      = $user_main_domain;
        $client_domain['terminal']  = $user_terminal_domain;
        if(in_array($user_main_domain,$uc['business_domain'])){
            $client_domain['is_business'] = true;
        }
    }
    Config::set('g_client_domain',$client_domain);
    return $client_domain;
}

/**
 * 读取客户全局配置
 * @param  [type] $database_config [description]
 * @return [type]                  [description]
 */
function load_client_config() {
    //根据域名，动态配置数据库配置
    $header_sub_host = Request::instance()->header('x-sub-host');
    $client_domain = get_client_domain();

    if($header_sub_host){
        $sub_host    = $header_sub_host;
        $defined_way = 'header';
    }else{
       
        $sub_host      = $client_domain['main'];
        $defined_way   = 'domain';
    }

    if(!$sub_host && Config::get('app_debug')){
        $sub_host    = 'base';
        $defined_way = 'debug';
    }

    if($sub_host != '' && !$client_domain['is_business'] && ($client_domain['pre'] != '' || $client_domain['root']) ){
         $client = load_client_by_host($sub_host,$defined_way,$client_domain['terminal']);
        //设置全局变量
        Config::set('g_client',$client);
        Config::set('g_og_id',$client['og_id']);
        Request::instance()->bind('client',$client);
    }
}

/**
 * 根据host载入客户信息
 * @param  [type] $sub_host [description]
 * @return [type]           [description]
 * @return [string] $user_terminal_domain [<description>]
 */
function load_client_by_host($sub_host,$defined_way = 'login',$user_terminal_domain = ''){
    $client = [
        'defined'       => false,       //是否明确
        'defined_way'   => 'domain',    //明确方式:domain 是通过域名,header 是通过http头 , debug 是调试 , login 登录方式
        'domain'        => '',          //客户的四级域名
        'subdomain'     => '',          //客户的五级域名
        'database'      => [],          //数据库连接配置
        'info'          => [],          //基本信息 
        'cid'           => 0,           //客户ID
        'parent_cid'    => 0,           //父CID
        'og_id'         => 0,           //机构ID
        'client_type'   => 0,           //客户类型（0为一般客户，1为代理商)
        'is_org_open'   => 0,           //是否开通加盟商

    ];
    if($sub_host){
        $center_db_cfg = Config::get('center_database');
        $db            = Db::connect($center_db_cfg);
        $client_info   = $db->name('client')->where('host',$sub_host)->where('delete_time',NULL)->find();

        if($client_info){
            //params deal
            $default_client_params = Config::get('org_default_config.center_params');
            if(isset($client_info['params']) && !empty($client_info['params'])){
                $client_info_params = json_decode($client_info['params'],true);
                foreach($default_client_params as $k=>$c){
                    if(isset($client_info_params[$k])){
                        $client_info_params[$k] = array_merge($c,$client_info_params[$k]);
                    } else {
                        $client_info_params[$k] = $c;
                    }
                }
                $client_info['params'] = $client_info_params;
            }else{
                $client_info['params'] = $default_client_params;
            }
            $cid = $client_info['cid'];
            $w_cd['cid'] = $cid;
            if($client_info['parent_cid'] != 0 ){
                $w_cd['cid'] = $client_info['parent_cid'];

                $parent_client = $db->name('client')->where($w_cd)->find();


            }else{
                $parent_client = false;
            }
            $client_db_config = $db->name('database_config')->where($w_cd)->find();
            if($client_db_config){
               
                Config::set('database',$client_db_config);          //设置客户的数据库连接
                /*
                $client_info['params'] = isset($client_info['params']) && !empty($client_info['params']) ?
                        json_decode($client_info['params'], true) : [];
                */
                $int_expire_day = intval($client_info['expire_day']);

                $is_expire = false;
                $is_warn = false;
                $expire_message = '';
                $now_time = time();


                if($int_expire_day > 0 && (strtotime($client_info['expire_day']) - $now_time) < 0){
                    $is_expire = true;
                }

                if(!$is_expire && $int_expire_day > 0){
                    $client_expire_warn_days = Config::get('center.client_expire_warn_days');
    		        $warn_int_day = intval(date('Ymd',strtotime("+{$client_expire_warn_days} days",$now_time)));
                    if($int_expire_day <= $warn_int_day){
                        $is_warn = true;
                        $expire_message = '您的系统将于'. date('Y年m月d日',strtotime($client_info['expire_day'])) .'到期,请及时续费!';
                    }
                }

                $client_info['is_expire'] = $is_expire;
                $client_info['is_warn'] = $is_warn;
                $client_info['expire_message'] = $expire_message;

                if($client_info['is_init_pay'] == 0 && $parent_client && $parent_client['client_type'] == 0){
                    $client_info['is_init_pay'] = $parent_client['is_init_pay'];
                }

                if($client_info){
                    $client['defined'] = true;
                    $client['defined_way'] = $defined_way;
                    $client['domain']       = $sub_host;
                    $client['subdomain']    = $user_terminal_domain;
                    $client['database']     = $client_db_config;
                    $client['info']         = $client_info; 
                    $client['cid']          = $cid;
                    $client['parent_cid']   = $client_info['parent_cid'];
                    $client['og_id']        = $client_info['og_id'];
                    $client['client_type']  = $client_info['client_type'];
                    $client['is_org_open']  = $client_info['is_org_open'];
                }
            }
        }
        
    }

    return $client;
}

/**
 * 绑定header相关信息到request实例
 * @return [type] [description]
 */
function bind_client_request(){
    $client_time     = intval(Request::instance()->header('x-client-time'));
    if(!$client_time){
        $client_time = time();
    }
    $client_date = date('Ymd',$client_time);
    $server_date = date('Ymd',time());

    $client_time_correct = true;
    if($client_date != $server_date){
        $client_time_correct = false;
    }
    Request::instance()->bind('client_time',$client_time);
    Request::instance()->bind('client_time_correct',$client_time_correct);
}