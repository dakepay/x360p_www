<?php


namespace app\api\behavior;

use app\api\model\ActionLog;
use think\Db;
use think\Response;

class RecordAction extends Response
{
    public function run($response)
    {
        if($response->code >= 500 ) {
            return true;
        }

        $method = strtolower(request()->method());
      
        //只记录增、删、改
        if($method == 'get' ) {
            return true;
        }

        //uri信息
        $dispatch = request()->dispatch();
        $module   = isset($dispatch['module']) && !empty($dispatch) ? $dispatch['module'] : [];

        $uri = [];
        foreach($module as $v){
            if(!empty($v)){
                array_push($uri,$v);
            }
        }
        
        $uri = implode('/',$uri);

        $data['create_time'] = request()->time();
        $data['uid']         = gvar('uid') ? gvar('uid') : 0;
        $data['ip']          = request()->ip();
        
        //url相关参数
        $param = request()->param();
        $param = !empty($param) ? request()->param() : [];

        $data['log_params'] = '';
        if(!empty($param)) {
            $log_params['request']  = $param;
            $log_params['response']['code'] = $response->code;
            $log_params['response']['message'] = isset($response->data['message']) ? substr($response->data['message'], 0, 220):'';
            $data['log_params'] = json_encode($log_params);
        }

        $data['log_desc'] = $this->get_log_desc($method,$uri,$data,$param);
        $data['uri']    = $method.':'.$uri;
        ActionLog::create($data, true);
        //Db::name('action_log')->insert($data);

        return true;
    }

    /**
     * 获得日志描述
     * @param  [type] $method [description]
     * @param  [type] $uri   [description]
     * @param  [type] $data  [description]
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    protected function get_log_desc($method,$uri,$data,$param){
        $log = action_log();
        if(!empty($log)){
            return $log;
        }

        $log_desc_config = config('action_name_desc');
        $action_map = [
            'post'      => '添加数据',
            'put'       => '修改数据',
            'delete'    => '删除数据'
        ];

        $action_uri = $method.':'.$uri;

        if(isset($log_desc_config[$action_uri])){
            $tpl_data = array_merge($param,$data);

            $desc = tpl_replace($log_desc_config[$action_uri],$tpl_data);
        }else{
            $desc = isset($action_map[$method]) ? $action_map[$method] : $method;
        }

        return $desc;
    }
}