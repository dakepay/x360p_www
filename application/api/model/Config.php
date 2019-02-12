<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/20
 * Time: 10:52
 */
namespace app\api\model;

class Config extends Base
{
    protected $type = [
        'cfg_value' => 'json'
    ];

    protected $soft_delete = false;     //不使用软删除

    protected $hidden = ['format', 'create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    /**
     * @param $input
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addConfig($input)
    {
        if ($input['cfg_name'] === 'wxpay') {
            $prefix = request()->server('DOCUMENT_ROOT') . '/public/data/cert/';
            $input['cfg_value']['key_path'] = !empty($input['cfg_value']['key_path']) ? $prefix . $input['cfg_value']['key_path'] : '';
            $input['cfg_value']['cert_path'] = !empty($input['cfg_value']['cert_path']) ? $prefix . $input['cfg_value']['cert_path'] : '';
        }
        $w['cfg_name'] = $input['cfg_name'];
        $og_id = gvar('og_id');
        $bid   = 0 ;
        if(isset($input['bid'])){
            $bid = intval($input['bid']);
        }
        $w['og_id'] = $og_id;
        $w['bid']   = $bid;

        if($bid > 0 && $input['cfg_name'] == 'params'){
            $config = config('org_default_config.params');
            $branch_default_config = branch_default_config($config,'params');
            $input['cfg_value']    = deep_array_merge($branch_default_config,$input['cfg_value']);
        }
        $m_cfg = $this->where($w)->find();
        if($m_cfg){
            $m_cfg->cfg_value = $input['cfg_value'];
            $result = $m_cfg->save();
            if(false === $result){
                return $this->sql_save_error('config');
            }
        }else{
            $result = $this->allowField(true)->save($input);
            if(!$result){
                return $this->sql_add_error('config');
            }
        }
        if(substr($input['cfg_name'],-3) == '_ui'){
            set_cuc_info($input['cfg_name'],$input['cfg_value']);
        }
        return true;
    }

    /***
     * @param $input
     * @return bool
     */
    public function editConfig($input)
    {
        if ($input['cfg_name'] === 'wxpay') {
            $prefix = request()->server('DOCUMENT_ROOT') . '/public/data/cert/';
            $input['cfg_value']['key_path'] = !empty($input['cfg_value']['key_path']) ? $prefix . $input['cfg_value']['key_path'] : '';
            $input['cfg_value']['cert_path'] = !empty($input['cfg_value']['cert_path']) ? $prefix . $input['cfg_value']['cert_path'] : '';
        }

        if (isset($input['cfg_value']['service']['default_sm_pwd_type'])){
            if ($input['cfg_value']['service']['default_sm_pwd_type'] == 5){
                if(!preg_match('/^[_0-9a-z]{6,16}$/i',$input['cfg_value']['service']['default_sm_pwd'])){
                    return $this->user_error('密码为数字字母下划线且不能小于6位！');
                }
            }
        }

        $result = $this->allowField(true)->isUpdate(true)->save($input);
        if ($result === false) {
            return false;
        }
        if(substr($input['cfg_name'],-3) == '_ui'){
            set_cuc_info($input['cfg_name'],$input['cfg_value']);
        }
        return true;
    }

    /***
     * @return bool
     */
    public function deleteConfig(){
        $result = $this->delete(true);

        if(false == $result){
            return $this->sql_delete_error('config');
        }

        $cfg_name = $this->cfg_name;
        if(substr($cfg_name,-3) === '_ui'){
            set_cuc_info($cfg_name,'');
        }

        return true;
    }

    /**
     * 获取用户配置
     * @param int $bid
     * @return array
     * @throws \think\exception\DbException
     */
    public static function userConfig($bid = 0){
        //$cfg_list = self::all();
        $w['og_id'] = gvar('og_id');
        $w['bid']   = 0;
        $cfg_list = self::all($w);
        $config = [];
        foreach($cfg_list as $k=>$cfg){
            if($cfg['cfg_name'] == 'lesson'){
                $config['lesson'] = [];
                foreach($cfg['cfg_value'] as $_k=>$c){
                    $config['lesson'][$_k] = self::filter_enable_item($c);
                }
                continue;
            }
            $config[$cfg['cfg_name']] = $cfg_list[$k]['cfg_value'];
        }

        if($bid > 0){
            $w['bid'] = $bid;
            $branch_cfg_list = self::all($w);
            if(!empty($branch_cfg_list)){
                foreach($branch_cfg_list as $cfg){
                    $key = $cfg['cfg_name'];
                    if(isset($config[$key])) {
                        $config[$key] = deep_array_merge($config[$key], $cfg['cfg_value']);
                    }else{
                        $config[$key] = $cfg['cfg_value'];
                    }
                }
            }
        }
        return $config;
    }

    protected static function filter_enable_item($list){
        $ret = [];
        foreach($list as $k=>$item){
            if(!isset($item['enable'])){
                $item['enable'] = true;
            }
            if($item['enable']){
                array_push($ret,$item);
            }
        }
        return $ret;
    }

    //获取特定的配置
    public static function get_config($cfg_name)
    {
        $w['og_id'] = gvar('og_id');
        $w['cfg_name'] = $cfg_name;
        $config = (new self())->where($w)->find();
        return $config ? $config->toArray() : [];
    }

    /**
     * 设置校区配置项目
     * @param $input
     * @return bool
     */
    public function setBranchConfig($input){
        $need_fields = ['key','val','section'];

        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }
        $section = $input['section'];
        $allow_sections = ['report'];

        if(!in_array($section,$allow_sections)){
            return $this->input_param_error('sectionn');
        }


        $bid = isset($input['bid'])?$input['bid']:auto_bid();

        $key = $input['key'];
        $val = $input['val'];
        return user_branch_config($key,$bid,$section,$val);
    }

    /**
     * 校区配置
     * @return array
     * @throws \think\exception\DbException
     */
    public static function BranchConfigs(){
        $w['og_id'] = gvar('og_id');
        $w['bid']   = ['GT',0];
        $ret = [];
        foreach(config('org_default_config') as $k=>$_v){
            $ret[$k] = [];
        }

        $config_list = self::all($w);

        if(!empty($config_list)){
            foreach($config_list as $r){
                $ret[$r['cfg_name']][$r['bid']] = $r['cfg_value'];
            }
        }

        foreach($ret as $k=>$v){
            if(empty($v)){
                $ret[$k] = new \stdClass();
            }
        }

        return $ret;
    }


}