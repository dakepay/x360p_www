<?php
namespace app\index\controller;

use think\Controller;


class Base extends Controller
{
	protected function send_ui($terminal = ''){
		$client_domain = gvar('client_domain');

		if($terminal != ''){
			$client_domain['terminal'] = $terminal;
		}


    	$uc = config('ui');
    	if($client_domain['pre'] == '' && $terminal == ''){
            if($client_domain['root']){
        		return $this->redirect(request()->domain().'/'.$uc['base_dir'].'/'.$uc['default']);
            }else{
                return $this->error('PAGE NOT FOUND');
            }
    	}
    	
        if($client_domain['is_business']){     //如果是业务域名
            $base_dir = PUBLIC_PATH.$uc['business_base_dir'].DS.$client_domain['main'];
        }else{
        	$base_dir = PUBLIC_PATH.$uc['base_dir'];

        	if(file_exists($base_dir.DS.$client_domain['main'].DS.'config.php')){
        	    $base_dir .= DS.$client_domain['main'];
            }
        }

    	$ui_index_html_file = $base_dir.DS.$client_domain['terminal'].DS.'index.html';

        if(file_exists($ui_index_html_file)){
            $ui_index_html_file = file_get_contents($ui_index_html_file);

            return $this->output_html($ui_index_html_file,$client_domain['terminal'],$client_domain['main'],$uc,$client_domain['is_business']);
        }

        return $this->error('PAGE NOT FOUND');
	}

    /**
     * 获得UI配置输出
     * @param $t
     */
	protected function get_ui_config_output($t){
        $ui_config = get_ui_config($t);
        $json_ui_config = json_encode($ui_config,JSON_UNESCAPED_UNICODE);
        $output = 'g.UI_CONFIG = ' . $json_ui_config . ';';

        return $output;
    }

	protected function output_html($html,$t,$m,$uc,$is_business = false){
        if(!$is_business){
            $base_dir = $uc['base_dir'];
            $cfg_key = 'pc';
        	if($t == 'pc'){
        		$re_search = '/\/ui\/[^\/]+\/dist\//';
        		$replace   = '/'.$base_dir.'/'.$t.'/dist/';
        	}else{
        		$re_search = '/\/ui\/[^\/]+\/static\//';
        		$replace   = '/'.$base_dir.'/'.$t.'/static/';
                if($t == 'student'){
                    $cfg_key   = 'mobile';
                }

        	}

        	$html = preg_replace($re_search,$replace,$html);

            $js_domain_output = 'g.CLIENT_DOMAIN = \''.$m.'\';';
            $js_ui_config_output = $this->get_ui_config_output($t);

            $default_sys_name = '校360专业版';
            $sys_name         = $default_sys_name;
            $client = gvar('client');
            if($client && !empty($client['info']['params'])){
                if(is_array($client['info']['params'])){
                    $sys_name = isset($client['info']['params'][$cfg_key])?$client['info']['params'][$cfg_key]['system_name']:$default_sys_name;
                    $json_object = json_encode($client['info']['params'],JSON_UNESCAPED_UNICODE);
                }else{
                    $json_object = $client['info']['params'];
                    $params = json_decode($json_object,true);
                    if(isset($params[$cfg_key])){
                        $sys_name = $params[$cfg_key]['system_name'];
                    }
                }
                $js_params_output = "g.CLIENT_PARAMS = ".$json_object.";";
            }else{
                $js_params_output = "g.CLIENT_PARAMS = {};";
            }
            //替换全局变量
            $search_str_arr =   [
                                    '<title>'.$default_sys_name.'</title>',
                                    'g.CLIENT_DOMAIN = \'\';',
                                    'g.CLIENT_PARAMS = {};',
                                    'g.UI_CONFIG = {};'
                                ];

            $replace_str_arr =  [
                                    '<title>'.$sys_name.'</title>',
                                    $js_domain_output,
                                    $js_params_output,
                                    $js_ui_config_output
                                ];

            $html = str_replace($search_str_arr,$replace_str_arr,$html);
        }else{
            $base_dir = $uc['business_base_dir'];
            $re_search = './static/';
            $replace   = $base_dir.'/'.$m.'/'.$t.'/static/';

            $html  = str_replace($re_search,$replace,$html);
        }

    	return $html;
    }
}