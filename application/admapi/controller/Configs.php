<?php
namespace app\admapi\controller;

use think\Db;
use think\Request;
use app\admapi\model\Config;

class Configs extends Base
{
    public function get_list(Request $request)
    {
        $cfg_name = input('cfg_name');
        if (empty($cfg_name)) {
            return parent::get_list($request);
        }

        $result = model('config')->where('cfg_name', $cfg_name)->find();
        if (empty($result)) {
            $config = config($cfg_name);

            if(!empty($config)) {
                $result['cfg_name']  = $cfg_name;
                $result['cfg_id']    = 0;
                $result['format']    = 'json';
                $result['cfg_value'] = $config;
            }
        }else{
            if(is_string($result['cfg_value'])){
                $result['cfg_value'] = json_decode($result['cfg_value'],true);
            }
            if($cfg_name == 'params'){
                $default_params = config('params');
                $result['cfg_value'] = deep_array_merge($default_params,$result['cfg_value']);
            }
        }

        return $this->sendSuccess($result);
    }

    /**
     * @desc  添加配置
     * @param array cfg_value
     * @method POST
     */
    public function post(Request $request)
    {
        $input = $request->post();
        $input['format'] = 'json';
        $rule = [
            'cfg_name|配置名称' => 'require',
            'cfg_value|配置内容' => 'require',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }

        $m_config = new Config();
        $result = $m_config->addConfig($input);

        if (!$result) {
            return $this->sendError(400, $m_config->getError());
        }
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $cfg_id = $request->put('cfg_id/d');
        if (empty($cfg_id)) {
            return $this->sendError(400, '缺少参数id');
        }
        $input = $request->put();
        $rule = [
            'cfg_name|配置名称' => 'require|unique:config,cfg_name,' . $cfg_id,
            'cfg_value|配置内容' => 'require',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            $this->sendError(400, $right);
        }

        $m_config = new Config();
        $where['cfg_id'] = $cfg_id;
        $where['cfg_name'] = $input['cfg_name'];
        $rs = $m_config->where($where)->find($cfg_id);
        if (!$rs) {
            return $this->sendError(400, '参数不合法');
        }
        $result = $m_config->uodateConfig($input);
        if (!$result) {
            return $this->sendError(400, $m_config->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 删除配置，删除配置必须用硬删除
     * @param Request $request
     * @param $id
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function delete(Request $request){
        $m_config = new Config();
        $id = input('id/d');

        $result = $m_config->deleteConfig($id);
        if ($result === false) {
            return $this->sendError(400, $m_config->getError());
        }

        return $this->sendSuccess();
    }


}