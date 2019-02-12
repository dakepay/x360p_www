<?php

namespace app\docs\controller;

use think\Request;
use think\Config;
use think\Url;
use DawnApi\helper\Tree;
use DawnApi\facade\Doc;

class Base extends Doc
{
     /**
     * 文档首页
     */
    public function main()
    {
        $mainHtmlPath = dirname(__FILE__) . DS . '..' . DS . 'view' . DS . 'main.tpl';
        $mainHtmlPath = (Config::get('mainHtmlPath')) ? Config::get('mainHtmlPath') : $mainHtmlPath;
        return view($mainHtmlPath, ['titleDoc' => $this->titleDoc]);
    }

    /**
     * 接口列表
     * @return \think\response\View
     */
    public function index()
    {
        $apiList = self::getApiDocList();
        $apiListHtmlPath = dirname(__FILE__) . DS . '..' . DS . 'view' . DS . 'apiList.tpl';
        $apiListHtmlPath = (Config::get('apiListHtmlPath')) ? Config::get('apiListHtmlPath') : $apiListHtmlPath;
        $menu = (empty($apiList)) ? '' : self::buildMenuHtml(Tree::makeTree($apiList));
        return view($apiListHtmlPath, ['menu' => $menu, 'titleDoc' => $this->titleDoc]);

    }

	/**
     * 获取文档
     * @return mixed
     */
    public static function getApiDocList()
    {

        //todo 可以写配置文件或数据
        $apiList = Config::get('api_doc');
        return $apiList;
    }

    /**
     * 接口详细文档
     * @param Request $request
     * @return \think\response\View
     */
    public function apiInfo(Request $request)
    {
        $id = $request->param('id');
        $apiOne = self::getApiDocOne($id);
        $className = $apiOne['class'];

        //获取接口类注释
        $classDoc = self::getBaseClassDoc($className);

        //没有接口类  判断是否有 Markdown文档
        if ($classDoc == false) {
            //输出 Markdown文档
            if (!isset($apiOne['readme']) || empty($apiOne['readme'])) return $this->sendError('', '没有接口');
            $apiMarkdownHtmlPath = dirname(__FILE__) . DS . '..' . DS . 'view' . DS . 'apiMarkdown.tpl';
            $apiMarkdownHtmlPath = (Config::get('apiMarkdownHtmlPath')) ? Config::get('apiMarkdownHtmlPath') : $apiMarkdownHtmlPath;
            return view($apiMarkdownHtmlPath, ['classDoc' => $apiOne, 'titleDoc' =>$this->titleDoc]);
        }

        //获取请求列表文档
        $methodDoc = self::getPublicMethodListDoc($className,$classDoc);
        //模板位置
        $apiInfoHtmlPath = dirname(__FILE__) . DS . '..' . DS . 'view' . DS . 'apiInfo.tpl';
        $apiInfoHtmlPath = (Config::get('apiInfoHtmlPath')) ? Config::get('apiInfoHtmlPath') : $apiInfoHtmlPath;

        //字段
        $fieldMaps['return'] = self::$returnFieldMaps;
        $fieldMaps['data'] = self::$dataFieldMaps;
        $fieldMaps['type'] = self::$typeMaps;
        return view($apiInfoHtmlPath, ['classDoc' => $classDoc, 'methodDoc' => $methodDoc, 'fieldMaps' => $fieldMaps, 'titleDoc' => $this->titleDoc]);
    }



    /**
     * 获取接口类文档
     * @param $className
     * @return array
     */
    private static function getBaseClassDoc($className)
    {
        try {
            $reflection = new \ReflectionClass($className);
        } catch (\ReflectionException  $e) {
            return false;
        }
        $docComment = $reflection->getDocComment();
        return static::getClassDoc($docComment);
    }



    /**
     * 获取各种方式响应文档
     * @param $className
     * @return mixed
     */
    private static function getPublicMethodListDoc($className,&$classDoc)
    {
        $base_uri = config('api.base_uri');
        if(substr($base_uri,-1) == '/'){
            $base_uri = substr($base_uri,0,-1);
        }
        //获取参数规则
        $rules = [];
        $clsRef = new \ReflectionClass($className);
        if($clsRef->hasMethod('getRules')){
            $rules = $className::getRules();
        }
      

        $methodList = self::getPublicMethodList($className);
        foreach ($methodList as $method) {
            $methodName = $method;
            $reflection = new \ReflectionMethod($className, $methodName);
            $docComment = $reflection->getDocComment();
            //获取title,desc,readme,return等说明
            $methodParams = static::getMethodDoc($docComment);

            $restMethods = ['get','post','put','delete','option'];
            if(!isset($methodParams['method'])){
                if(in_array($methodName,$restMethods)){
                    $methodParams['method'] = strtoupper($methodName);
                }else{
                    $methodParams['method'] = 'GET';
                }
            }

            if(empty($methodParams['url']) && !in_array($methodName,$restMethods)){
                if(substr($classDoc['url'],-1) == '/'){
                    $methodParams['url'] = $classDoc['url'].$methodName;
                }else{
                    $methodParams['url'] = $classDoc['url'].'/'.$methodName;
                }
            }else{
                if(!empty($methodParams['url'])){
                    $methodParams['url'] = $base_uri.'/'.$methodParams['url'];
                }else{
                    $methodParams['url'] = $classDoc['url'];
                }
                
            }

            $methodDoc[$methodName] = $methodParams;

            $methodDoc[$methodName]['rules'] = [];

            //接口注释的param
            $docMethodRules = [];
            if(isset($methodParams['param']) && !empty($methodParams['param'])) {
                foreach($methodParams['param'] as $param_arr) {
                    $docMethodRules[$param_arr['name']]['name'] = $param_arr['name'];
                    $docMethodRules[$param_arr['name']]['type'] = $param_arr['type'];
                    $docMethodRules[$param_arr['name']]['desc'] = $param_arr['desc'];
                }

                if(isset($rules[$methodName])) {
                    $rules[$methodName] = array_merge($docMethodRules, $rules[$methodName]);
                } else {
                    $rules[$methodName] = $docMethodRules;
                }
            }

            if(!empty($rules) ){
                $methodRules = isset($rules['all']) ? $rules['all'] : [];
                if(isset($rules[$methodName])){
                    $methodRules = array_merge($methodRules, $rules[$methodName]);
                }
                $methodDoc[$methodName]['rules'] = $methodRules;

            }

            
        }

        return $methodDoc;
    }


     /**
     * 获取接口所有请求方式
     * @param $className
     * @return array
     */
    private static function getPublicMethodList($className)
    {
        $reflection = new \ReflectionClass($className);
        $methods  = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methodNames = [];

        $method_list = ['getRules','response','sendSuccess','sendError','sendType','sendRedirect','setType','getConfig','init','restful','register'];

        if($reflection->hasProperty('noRest')){
            $method_list = array_merge($method_list,['get']);
        }
        foreach($methods as $method){
            if(substr($method->name,0,1) == '_' || in_array($method->name,$method_list)){
                continue;
            }
            array_push($methodNames,$method->name);
        }
        return $methodNames;
    }


    /**
     * 获取注释转换成数组
     * @param $docComment
     * @return mixed
     */
    protected static function getClassDoc($docComment)
    {
        $base_uri = config('api.base_uri');
        $docCommentArr = explode("\n", $docComment);
        foreach ($docCommentArr as $comment) {
            $comment = trim($comment);
            //接口名称
            $pos = stripos($comment, '@title');
            if ($pos !== false) {
                $data['title'] = trim(substr($comment, $pos + 6));
                continue;
            }
            //接口描述
            $pos = stripos($comment, '@desc');
            if ($pos !== false) {
                $data['desc'] = trim(substr($comment, $pos + 5));
                continue;
            }
            //接口说明文档
            $pos = stripos($comment, '@readme');
            if ($pos !== false) {
                $data['readme'] = trim(substr($comment, $pos + 7));
                continue;
            }
            //接口url
            $pos = stripos($comment, '@url');
            if ($pos !== false) {
                $data['url'] = trim(substr($comment, $pos + 4));
                continue;
            }
            //接口url versions
            $pos = stripos($comment, '@version');
            if ($pos !== false) {
                $data['version'] = trim(substr($comment, $pos + 8));
                continue;
            }

            //返回字段说明
            //@return注释
            $pos = stripos($comment, '@return');
            //以上都没有匹配到直接下一行
            if ($pos === false) {
                continue;
            }
            $returnCommentArr = explode(' ', substr($comment, $pos + 8));
            //将数组中的空值过滤掉，同时将需要展示的值返回
            $returnCommentArr = array_values(array_filter($returnCommentArr));
            //如果小于3个也过滤
            if (count($returnCommentArr) < 2) {
                continue;
            }
            if (!isset($returnCommentArr[2])) {
                $returnCommentArr[2] = '';    //可选的字段说明
            } else {
                //兼容处理有空格的注释
                $returnCommentArr[2] = implode(' ', array_slice($returnCommentArr, 2));
            }
            $returnCommentArr[0] = (in_array(strtolower($returnCommentArr[0]), array_keys(self::$typeMaps))) ? self::$typeMaps[strtolower($returnCommentArr[0])] : $returnCommentArr[0];
            $data['return'][] = [
                'name' => $returnCommentArr[1],
                'type' => $returnCommentArr[0],
                'desc' => $returnCommentArr[2],
            ];

        }
        if(substr($base_uri,-1) == '/'){
            $base_uri = substr($base_uri,0,-1);
        }
        
        $data['title'] = (isset($data['title'])) ? $data['title'] : '';
        $data['desc'] = (isset($data['desc'])) ? $data['desc'] : '';
        $data['readme'] = (isset($data['readme'])) ? $data['readme'] : '';
        $data['return'] = (isset($data['return'])) ? $data['return'] : [];
        $data['url'] = (isset($data['url'])) ? $base_uri.'/'.$data['url'] : '';
        $data['version'] = (isset($data['version'])) ? $data['version'] : '';
        return $data;
    }



     /**
     * 获取注释转换成数组
     * @param $docComment
     * @return mixed
     */
    protected static function getMethodDoc($docComment)
    {
        $docCommentArr = explode("\n", $docComment);
        foreach ($docCommentArr as $comment) {
            $comment = trim($comment);
            //接口名称
            $pos = stripos($comment, '@title');
            if ($pos !== false) {
                $data['title'] = trim(substr($comment, $pos + 6));
                continue;
            }
            //接口描述
            $pos = stripos($comment, '@desc');
            if ($pos !== false) {
                $data['desc'] = trim(substr($comment, $pos + 5));
                continue;
            }
            //接口说明文档
            $pos = stripos($comment, '@readme');
            if ($pos !== false) {
                $data['readme'] = trim(substr($comment, $pos + 7));
                continue;
            }
            //接口url
            $pos = stripos($comment, '@url');
            if ($pos !== false) {
                $data['url'] = trim(substr($comment, $pos + 4));
                continue;
            }
            //接口url versions
            $pos = stripos($comment, '@version');
            if ($pos !== false) {
                $data['version'] = trim(substr($comment, $pos + 8));
                continue;
            }

            // 接口调用 method
            $pos = stripos($comment,'@method');

            if($pos !== false){
                $data['method'] = trim(substr($comment,$pos +8));
                continue;
            }

            //返回字段说明
            //@return注释
            $pos = stripos($comment, '@return');
            //以上都没有匹配到直接下一行
            if ($pos !== false) {
                $returnCommentArr = explode(' ', substr($comment, $pos + 8));
                //将数组中的空值过滤掉，同时将需要展示的值返回
                $returnCommentArr = array_values(array_filter($returnCommentArr));
                //如果小于3个也过滤
                if (count($returnCommentArr) < 2) {
                    continue;
                }
                if (!isset($returnCommentArr[2])) {
                    $returnCommentArr[2] = '';    //可选的字段说明
                } else {
                    //兼容处理有空格的注释
                    $returnCommentArr[2] = implode(' ', array_slice($returnCommentArr, 2));
                }
                $returnCommentArr[0] = (in_array(strtolower($returnCommentArr[0]), array_keys(self::$typeMaps))) ? self::$typeMaps[strtolower($returnCommentArr[0])] : $returnCommentArr[0];
                $data['return'][] = [
                    'name' => $returnCommentArr[1],
                    'type' => $returnCommentArr[0],
                    'desc' => $returnCommentArr[2],
                ];

                continue;
            }

            //@param注释
            $pos = stripos($comment, '@param');
            //以上都没有匹配到直接下一行
            if ($pos === false) {
                continue;
            }
            $paramCommentArr = explode(' ', substr($comment, $pos + 7));
            //将数组中的空值过滤掉，同时将需要展示的值返回
            $paramCommentArr = array_values(array_filter($paramCommentArr));
            //如果小于3个也过滤
            if (count($paramCommentArr) < 2) {
                continue;
            }
            if (!isset($paramCommentArr[2])) {
                $paramCommentArr[2] = '';    //可选的字段说明
            } else {
                //兼容处理有空格的注释
                $paramCommentArr[2] = implode(' ', array_slice($paramCommentArr, 2));
            }
            $paramCommentArr[0] = (in_array(strtolower($paramCommentArr[0]), array_keys(self::$typeMaps))) ? self::$typeMaps[strtolower($paramCommentArr[0])] : $paramCommentArr[0];
            $data['param'][] = [
                'name' => $paramCommentArr[1],
                'type' => $paramCommentArr[0],
                'desc' => $paramCommentArr[2],
            ];

        }

        $data['method'] = (isset($data['method']))?$data['method'] :'GET';
        $data['title'] = (isset($data['title'])) ? $data['title'] : '';
        $data['desc'] = (isset($data['desc'])) ? $data['desc'] : '';
        $data['readme'] = (isset($data['readme'])) ? $data['readme'] : '';
        $data['return'] = (isset($data['return'])) ? $data['return'] : [];
        $data['param'] = (isset($data['param'])) ? $data['param'] : [];
        $data['url'] = (isset($data['url'])) ? $data['url'] : '';
        $data['version'] = (isset($data['version'])) ? $data['version'] : '';

        return $data;
    }

    /**
     * 生成 接口菜单
     * @param $data
     * @param string $html
     * @return string
     */
    private static function buildMenuHtml($data, $html = '')
    {
        foreach ($data as $k => $v) {
            $html .= '<li >';
            if (isset($v['children']) && is_array($v['children'])) {
                $html .= '<a href="javascript:;"><i class="fa fa-folder"></i> <span class="nav-label">' . $v['name'] . '</span><span class="fa arrow"></span></a>';//name
            } else {
                $html .= '<a href="' . Url::build('apiInfo', ['id' => $v['id']]) . '" class="J_menuItem"><i class="fa fa-file"></i> <span class="nav-label">' . $v['name'] . '</span></a>';//
            }
            //需要验证是否有子菜单
            if (isset($v['children']) && is_array($v['children'])) {

                $html .= '<ul class="nav nav-second-level">';
                $html .= self::buildMenuHtml($v['children']);
                //验证是否有子订单
                $html .= '</ul>';

            }
            $html .= '</li>';

        }
        return $html;

    }


}