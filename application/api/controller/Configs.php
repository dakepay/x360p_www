<?php
/**
 * luo
 */
namespace app\api\controller;

use think\Db;
use think\Request;

class Configs extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $cfg_name =$input['cfg_name'];
        $bid = 0;
        if(isset($input['bid'])){
            $bid = intval($input['bid']);
        }
        if (empty($cfg_name)) {
            return parent::get_list($request);
        }
        $org_default_config = config('org_default_config');

        $og_id = gvar('og_id');
        $w['og_id'] = $og_id;
        $w['bid']   = $bid;
        $w['cfg_name'] = $cfg_name;

        $mConfig = new \app\api\model\Config();
        $result  = $mConfig->where($w)->find();

        if(!$result && $bid > 0){
            $w['bid'] = 0;
            $result = $mConfig->where($w)->find();

            if($result){
                $result['cfg_id'] = 0;
            }
        }

        if (empty($result)) {
            if($cfg_name == 'wechat_template'){
                $cfg_name = 'tplmsg';
            }
            $config = config($cfg_name) ? config($cfg_name) : $org_default_config[$cfg_name];
            if(!empty($config)) {
                $result['bid']       = $bid;
                $result['cfg_name']  = $cfg_name;
                $result['cfg_id']    = 0;
                $result['format']    = 'json';
                $result['cfg_value'] = $config;

                if($bid > 0){
                    $result['cfg_value'] = branch_default_config($config,$cfg_name);
                }
            }
        }else{
            if(is_string($result['cfg_value'])){
                $result['cfg_value'] = json_decode($result['cfg_value'],true);
            }

            if($cfg_name == 'params'){
                $config = config('org_default_config.params');
                if($bid > 0){
                    $branch_default_config = branch_default_config($config,'params');
                    $result['cfg_value']   = is_array($result['cfg_value'])?
                        deep_array_merge($branch_default_config,$result['cfg_value']):$branch_default_config;

                    $result['bid'] = $bid;
                }else{
                    $result['cfg_value'] = is_array($result['cfg_value']) ?
                        deep_array_merge($config,$result['cfg_value']) : $config;
                }

            }elseif($cfg_name == 'wechat_template'){
                $result['cfg_value'] = is_array($result['cfg_value']) ?
                    deep_array_merge(config('tplmsg'),$result['cfg_value']) : config('tplmsg');
            }else{
                if(isset($org_default_config[$cfg_name]) && is_array($result['cfg_value'])){
                    $result['cfg_value'] = deep_array_merge($org_default_config[$cfg_name],$result['cfg_value']);
                }else {
                    if (config($cfg_name)) {

                        $result['cfg_value'] = is_array($result['cfg_value']) ?

                            deep_array_merge(config($cfg_name), $result['cfg_value']) : config($cfg_name);
                    }
                }
            }
        }
        return $this->sendSuccess($result);
    }

    /**
     * @desc  添加配置
     * @author luo
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

        $mConfig = new \app\api\model\Config();

        $result = $mConfig->addConfig($input);
        if (!$result) {
            return $this->sendError(400, $mConfig->getError());
        }
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {

        $input = $request->put();
        $rule = [
            'cfg_name|配置名称' => 'require',
            'cfg_value|配置内容' => 'require',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            $this->sendError(400, $right);
        }
        $cfg_id = $request->param('id');
        $bid = 0;
        if(isset($input['bid'])){
            $bid = intval($input['bid']);
        }
        if (empty($cfg_id) && !$bid) {
            return $this->sendError(400, '缺少参数id');
        }
        $mConfig = new \app\api\model\Config();

        if($bid > 0){
            $result = $mConfig->addConfig($input);
        }else{
            $where['cfg_id'] = $cfg_id;
            $where['cfg_name'] = $input['cfg_name'];
            $config  = $mConfig->where($where)->find($cfg_id);
            if (!$config) {
                return $this->sendError(400, '参数不合法');
            }
            $result = $config->editConfig($input);
        }


        if (!$result) {
            return $this->sendError(400, $config->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 删除配置，删除配置必须用硬删除
     * @param Request $request
     * @param $id
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete(Request $request){
        $model = model('config');
        $id = input('id/d');

        if($id){
            $rs = $model->find($id);
            if (!$rs) {
                return $this->sendError(400, 'resource not exists');
            }
            try{
                $result = $rs->deleteConfig();
            }catch(Exception $e){
                return $this->sendError(500,$e->getMessage());
            }
            if ($result === false) {
                return $this->sendError(400, $rs->getError());
            }
        }

        return $this->sendSuccess('ok');
    }

    /**
     * @desc  界面设置
     * @author luo
     * @method GET
     */
    public function get_interface_config()
    {
        $client = gvar('client.info');
        if(empty($client)) return $this->sendError(400, '客户登录信息不存在');

        return $this->sendSuccess($client);
    }

    /**
     * @desc  修改中心数据库的配置
     * @author luo
     * @method POST
     */
    public function edit_center_params(Request $request)
    {
        $post = $request->post();
        $cid = $post['cid'];
        if(empty($cid)) return $this->sendError(400, 'cid错误');
        $params = $post['params'];
        if(empty($params)) return $this->sendError(400, '修改配置错误');

        $conn = Db::connect('center_database');
        $client = $conn->name('client')->where('cid', $cid)->find();
        if(empty($client)) return $this->sendError(400, '客户不存在');

        $client['params'] = isset($client['params']) && !empty($client['params']) ?
            json_decode($client['params'], true) : [];

        $default_org_center_config = config('org_default_config.center_params');

        $client_params = [];

        foreach($default_org_center_config as $k=>$c){
            if(isset($client['params'][$k])){
                $c = array_merge($c,$client['params'][$k]);
            }
            if(isset($params[$k])){
                $c = array_merge($c,$params[$k]);
            }

            $client_params[$k] = $c;
        }

        $client['params'] = json_encode($client_params);
        $rs = $conn->name('client')->where('cid', $cid)->update(['params' => $client['params']]);
        if($rs === false) return $this->sendError(400, '编辑配置失败');

        return $this->sendSuccess();
    }

    /**
     * @desc 获得或写入UI配置
     * @param Request $request
     * @method GET|POST
     */
    public function uiconfig(Request $request){
        $client = gvar('client');
        $cid = $client['cid'];
        if($request->isPost()){
            $post = $request->post();
            if(!isset($post['terminal'])){
                return $this->sendError(400,'缺少参数:terminal');
            }
            $tkey   = $post['terminal'].'_config';
            $config = '';
            if(!empty($post['config'])){
                $config = json_encode($post['config'],JSON_UNESCAPED_UNICODE);
            }
            $w_cuc['cid'] = $cid;
            $cuc_info = get_cuc_info($w_cuc);
            $db = Db::connect('center_database');

            if($cuc_info){
                //update
                $update[$tkey] = $config;
                $result = $db->name('client_ui_config')->where('cuc_id',$cuc_info['cuc_id'])->update($update);
                if(false === $result){
                    return $this->sendError(400,'写入UI配置失败!');
                }
            }else{
                //create
                $data['cid'] = $cid;
                $data[$tkey] = $config;
                $data['create_time'] = time();
                $data['update_time'] = time();

                $result = $db->name('client_ui_config')->insert($data);

                if(!$result){
                    return $this->sendError(400,'写入UI配置失败!');
                }
            }
            return $this->sendSuccess();
        }

        $t = input('get.t');

        $ui_config = get_ui_config($t);

        return $this->sendSuccess($ui_config);
    }

    /**
     * 获取校区配置
     * 使用方法  get /api/configs/0/branch?key=ki&section=report
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_list_branch(Request $request){
        $get = input('get.');

        $ret = [];
        if(!isset($get['key'])){
            return $this->sendSuccess($ret);
        }

        $key = $get['key'];
        $section = isset($get['section'])?$get['section']:'';

        if($section == ''){
            $section = 'report';
        }

        $bid = isset($get['bid'])?$get['bid']:$request->bid;
        $ret = user_branch_config($key,$bid,$section);
        return $this->sendSuccess($ret);
    }

    /**
     * 设置校区配置项
     * 使用方法  post /api/configs/0/branch
     * {
     *      'key':'ki',
     *      'section':'report',
     *      'val':{},   //配置值,json结构
     *      'bid':1     //可选
     * }
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function post_branch(Request $request){
        $input = input('post.');
        $mConfig = new \app\api\model\Config();

        $result = $mConfig->setBranchConfig($input);

        if(!$result){
            return $this->sendError(400,$mConfig->getError());
        }

        return $this->sendSuccess();

    }



}