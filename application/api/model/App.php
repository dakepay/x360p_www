<?php

namespace app\api\model;

class App extends Base
{

    public function updateAction($app_ename){
        $w = [
            'is_delete' => 0,
            'app_ename' => $app_ename
        ];
        $mApps = db('app', 'db_center')->where($w)->find();
        if (empty($mApps)){
            return $this->user_error($app_ename.'is null');
        }

        $org_pc_ui = set_disabled_per_items($app_ename);
        $update['cfg_value'] = $org_pc_ui;
        $update['cfg_name'] = 'org_pc_ui';

        $mConfig = new Config();

        $w_app = [
            'og_id' => gvar('og_id'),
            'cfg_name' => 'org_pc_ui',
            'bid'   => 0
        ];
        $m_cfg = $mConfig->skipBid()->where($w_app)->find();
        if($m_cfg){
            $w_cfg_update['cfg_id'] = $m_cfg->cfg_id;
            $result = $mConfig->allowField(true)->save($update,$w_cfg_update);
        }else{
            $update['bid'] = 0;     //不加这个会自动把bid设置为当前提交的校区
            $result = $mConfig->skipInsertBid()->allowField(true)->save($update);
        }

        if (!$result){
            return $this->user_error('config');
        }
        return true;
    }

}
