<?php

namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\Request;
use think\Response;

class Http extends Handle {
    
    public function render (Exception $e)
    {
        if ( $e instanceof HttpException ) {
            $statusCode = $e->getStatusCode ();
            
            if ( stristr ($e->getMessage (), "module not exists:") ) {
            	$path = request()->path();
            	$uc   = config('ui');
            	$http_host = request()->host();
            	if(($pos = strpos($http_host,$uc['domain'])) !== false){
		    		$pre_domain = substr($http_host,0,$pos-1);
		    		if(strpos($pre_domain,'.') !== false){
			    		$arr_domain = explode('.',$pre_domain);
			    		$user_main_domain = array_pop($arr_domain);
			    		$user_terminal_domain = implode('.',$arr_domain);

			    	}else{
			    		$user_main_domain = $pre_domain;
			    		$user_terminal_domain = $uc['default'];
			    	}

			    	if(!in_array($user_terminal_domain,$uc['sub_domains'])){
			    		$user_terminal_domain = 'pc';
			    	}

			    	$base_dir = $uc['base_dir'];

			    	$real_path = $this->replace_static_res_url($path,$user_terminal_domain,$base_dir);

			    	if(file_exists(PUBLIC_PATH.$real_path)){
			    		header('Location: /'.$real_path);
			    		exit();
			    	}
		    	}
            }
        }

        if(Request::instance()->isAjax() && !defined('APP_DEBUG')){
            $ret['error'] = 500;
            $ret['message'] = '服务器忙,请稍后再试';
            if(defined('APP_DEBUG') && constant('APP_DEBUG')){
                $ret['data']    = ['message'=>$e->getMessage(),'trace'=>$this->get_useful_trace($e)];
            }else{
                //todo:发送错误记录到邮件
            }
            return Response::create($ret, 'json',500);
            //return json($ret,500);
        }
        
        //可以在此交由系统处理
        return parent::render ($e);
    }


    protected function get_useful_trace(Exception $e){
        $trace = $e->getTrace();
        $message = [];
        foreach($trace as $t){
            $message[] = $t;
            /*
            if(isset($t['file'])){
                break;
            }
            */
        }
        return $message;
    } 


    protected function replace_static_res_url($path,$t,$base_dir){
    	if($t == 'pc'){
    		$re_search = '/[^\/]+\/dist\//';
    		$replace   = $base_dir.'/'.$t.'/dist/';
    	}else{
    		$re_search = '/[^\/]+\/static\//';
    		$replace   = $base_dir.'/'.$t.'/static/';
    	}

    	$path = preg_replace($re_search,$replace,$path,1);

    	return $path;
    }
    
}