<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
        'id'   => '\d+'
    ],

    'student'            =>  'index/student/index', //家长端界面入口
    'm'                  =>  'index/m/index',       //机构移动端界面入口
    'school'             =>  'index/school/index',  //机构学校入口
    'ft'                 =>  'index/ft/index',      //外交端入口

    'api/tklogin'         => 'api/open/tklogin',    //TOKEN登录
    'api/signin'          => 'api/open/signin',     //登录
    'api/gentk'          => 'api/open/gentk',     //接口获取token
    'api/logout'          => 'api/index/logout',    //退出登录
    'api/captcha/[:name]' => 'api/open/captcha',    //验证码
    'api/upload'          => 'api/upload/restful',  //文件上传
    'api/unlock'          => 'api/index/unlock',    //解锁
    'api/global/:name'    => 'api/index/glob',      //全局变量
    'api/datarow'         => 'api/index/datarow',   //根据ID获取数据表一行记录
    'api/search/:name'    => 'api/:name/search',    //资源搜索
    'api/profile'         => 'api/index/profile',     //个人资料修改
    'api/authorize$'      => 'api/authorize/index',     //公众号授权
    'api/redirect'        => 'api/open/redirect',       //公开跳转
    'api/issetup'        => 'api/index/is_setup',       //是否初始化完成
    'api/organization'  => 'api/index/get_organization_info',       //机构信息
    'api/send_code'		=> 'api/index/sendCode',/*发送验证码*/

    'api/import' => 'api/index/import',      //导入
    'api/export' => 'api/index/export',      //导出

    'api/mobile_signin'     => 'api/open/mobile_signin',        //手机号获取登录token

    /*pc*/
    'api/:res/:id/:subres/:subid/:action'   => ['api/:res/dosub',['method'=>'POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\d+','action'=>'\w+']],
    'api/:res/:id/:subres/:subid'   => ['api/:res/restful_sub',['method'=>'GET/PUT/DELETE','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\w+']],
    'api/:res/:id/:subres'          => ['api/:res/restful_sub',['method'=>'GET|POST|DELETE','id'=>'\d+','res'=>'\w+','subres'=>'\w+s$']],
    'api/:res/:id'                  => ['api/:res/restful',['method'=>'GET|PUT|DELETE','id'=>'\d+','res'=>'\w+']],
    'api/:res/:ids'                 => ['api/:res/restful',['method'=>'DELETE','ids'=>'\d+(,\d+)+','res'=>'\w+']],


    /**
     * 后台基础API路由映射
     */
    'admapi/signin'          => 'admapi/open/signin',     //登录
    'admapi/logout'          => 'admapi/index/logout',    //退出登录
    'admapi/captcha/[:name]' => 'admapi/open/captcha',    //验证码
    'admapi/upload'          => 'admapi/upload/restful',  //文件上传
    'admapi/unlock'          => 'admapi/index/unlock',    //解锁
    'admapi/global/:name'    => 'admapi/index/glob',      //全局变量
    'admapi/profile'         => 'admapi/index/profile',     //个人资料修改
    

    'admapi/import' => 'admapi/index/import',      //导入
    'admapi/export' => 'admapi/index/export',      //导出

    /*pc*/
    'admapi/:res/:id/:subres/:subid/:action'   => ['admapi/:res/dosub',['method'=>'POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\d+','action'=>'\w+']],
    'admapi/:res/:id/:subres/:subid'   => ['admapi/:res/restful_sub',['method'=>'GET/PUT/DELETE','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\w+']],
    'admapi/:res/:id/:subres'          => ['admapi/:res/restful_sub',['method'=>'GET|POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+s$']],
    'admapi/:res/:id'                  => ['admapi/:res/restful',['method'=>'GET|PUT|DELETE','id'=>'\d+','res'=>'\w+']],
    'admapi/:res/:ids'                 => ['admapi/:res/restful',['method'=>'DELETE','ids'=>'\d+(,\d+)+','res'=>'\w+']],


    /**
     * 学生家长端
     */
    'sapi/signin'          => 'sapi/open/signin',     //登录
    'sapi/signup'          => 'sapi/open/signup',     //注册
    'sapi/logout'          => 'sapi/index/logout',    //退出登录
    'sapi/captcha/[:name]' => 'sapi/open/captcha',    //验证码
    'sapi/upload'          => 'sapi/upload/restful',  //文件上传
    'sapi/unlock'          => 'sapi/index/unlock',    //解锁
    'sapi/global/:name'    => 'sapi/index/glob',      //全局变量

    /*pc*/
    'sapi/:res/:id/:subres/:subid/:action'   => ['sapi/:res/dosub',['method'=>'POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\d+','action'=>'\w+']],
    'sapi/:res/:id/:subres/:subid'   => ['sapi/:res/restful_sub',['method'=>'GET/PUT/DELETE','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\w+']],
    'sapi/:res/:id/:subres'          => ['sapi/:res/restful_sub',['method'=>'GET|POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+s$']],
    'sapi/:res/:id'                  => ['sapi/:res/restful',['method'=>'GET|PUT|DELETE','id'=>'\d+','res'=>'\w+']],
    'sapi/:res/:ids'                 => ['sapi/:res/restful',['method'=>'DELETE','ids'=>'\d+(,\d+)+','res'=>'\w+']],

    /**
     * VIP会员自助服务中心
     */
    'vipapi/tklogin'         => 'vipapi/open/tklogin',    //TOKEN登录
    'vipapi/signin'          => 'vipapi/open/signin',     //登录
    'vipapi/logout'          => 'vipapi/index/logout',    //退出登录
    'vipapi/captcha/[:name]' => 'vipapi/open/captcha',    //验证码

    'vipapi/:res/:id/:subres/:subid/:action'   => ['vipapi/:res/dosub',['method'=>'POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\d+','action'=>'\w+']],
    'vipapi/:res/:id/:subres/:subid'   => ['vipapi/:res/restful_sub',['method'=>'GET/PUT/DELETE','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\w+']],
    'vipapi/:res/:id/:subres'          => ['vipapi/:res/restful_sub',['method'=>'GET|POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+s$']],
    'vipapi/:res/:id'                  => ['vipapi/:res/restful',['method'=>'GET|PUT|DELETE','id'=>'\d+','res'=>'\w+']],
    'vipapi/:res/:ids'                 => ['vipapi/:res/restful',['method'=>'DELETE','ids'=>'\d+(,\d+)+','res'=>'\w+']],


    /**
     * 小程序
     */
    'mccapi/signin'          => 'mccapi/open/signin',     //登录
    

    /*pc*/
    'mccapi/:res/:id/:subres/:subid/:action'   => ['mccapi/:res/dosub',['method'=>'POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\d+','action'=>'\w+']],
    'mccapi/:res/:id/:subres/:subid'   => ['mccapi/:res/restful_sub',['method'=>'GET/PUT/DELETE','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\w+']],
    'mccapi/:res/:id/:subres'          => ['mccapi/:res/restful_sub',['method'=>'GET|POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+s$']],
    'mccapi/:res/:id'                  => ['mccapi/:res/restful',['method'=>'GET|PUT|DELETE','id'=>'\d+','res'=>'\w+']],
    'mccapi/:res/:ids'                 => ['mccapi/:res/restful',['method'=>'DELETE','ids'=>'\d+(,\d+)+','res'=>'\w+']],



    /**
     * 外教长端
     */
    'ftapi/signin'          => 'ftapi/open/signin',     //登录
    'ftapi/logout'          => 'ftapi/index/logout',    //退出登录
    'ftapi/captcha/[:name]' => 'ftapi/open/captcha',    //验证码
    'ftapi/upload'          => 'ftapi/upload/restful',  //文件上传
    'ftapi/unlock'          => 'ftapi/index/unlock',    //解锁
    'ftapi/global/:name'    => 'ftapi/index/glob',      //全局变量

    'ftapi/:res/:id/:subres/:subid/:action'   => ['ftapi/:res/dosub',['method'=>'POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\d+','action'=>'\w+']],
    'ftapi/:res/:id/:subres/:subid'   => ['ftapi/:res/restful_sub',['method'=>'GET/PUT/DELETE','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\w+']],
    'ftapi/:res/:id/:subres'          => ['ftapi/:res/restful_sub',['method'=>'GET|POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+s$']],
    'ftapi/:res/:id'                  => ['ftapi/:res/restful',['method'=>'GET|PUT|DELETE','id'=>'\d+','res'=>'\w+']],
    'ftapi/:res/:ids'                 => ['ftapi/:res/restful',['method'=>'DELETE','ids'=>'\d+(,\d+)+','res'=>'\w+']],

    /**
     * 加盟商PC端
     */
    'fapi/unlock'          => 'fapi/index/unlock',    //解锁
    'fapi/global/:name'    => 'fapi/index/glob',      //全局变量

    'fapi/:res/:id/:subres/:subid/:action'   => ['fapi/:res/dosub',['method'=>'POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\d+','action'=>'\w+']],
    'fapi/:res/:id/:subres/:subid'   => ['fapi/:res/restful_sub',['method'=>'GET/PUT/DELETE','id'=>'\d+','res'=>'\w+','subres'=>'\w+','subid'=>'\w+']],
    'fapi/:res/:id/:subres'          => ['fapi/:res/restful_sub',['method'=>'GET|POST','id'=>'\d+','res'=>'\w+','subres'=>'\w+s$']],
    'fapi/:res/:id'                  => ['fapi/:res/restful',['method'=>'GET|PUT|DELETE','id'=>'\d+','res'=>'\w+']],
    'fapi/:res/:ids'                 => ['fapi/:res/restful',['method'=>'DELETE','ids'=>'\d+(,\d+)+','res'=>'\w+']],




];
