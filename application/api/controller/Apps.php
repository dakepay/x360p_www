<?php

namespace app\api\controller;

use think\Request;
use app\api\model\App;
use app\api\model\Config;


class Apps extends Base
{

    public function get_list(Request $request)
    {
        $client = gvar('client');
        $need_fields = ['app_id','app_ename','app_name','app_uri','pics','app_icon_uri','app_desc','price_type','year_price','volume_price','is_system'];
        $app_list = db('app', 'db_center')->where('is_delete',0)->field($need_fields)->select();
        $client_app_list = db('vip_client_app', 'db_center')->where('cid', $client['cid'])->select();
        $buy_apps = [];
        if (!empty($client_app_list)){
            foreach ($client_app_list as $key => $value){
                $buy_apps[$key] = $value['app_ename'];
            }
        }
        $disabled_per_items = get_disabled_per_items();
        $list = [];
        foreach ($app_list as $k => $v){
            //加盟商管理应用比较特殊，只有在客户开启了加盟商条件下才显示该应用。
            if($v['app_ename'] == 'franchisees' && $client['info']['is_org_open'] == 0){
                continue;
            }
            if (in_array($v['app_ename'],$buy_apps)){
                $v['is_buy'] = 1;
            }else{
                $v['is_buy'] = 0;
            }
            if (in_array($v['app_ename'],$disabled_per_items)){
                $v['action'] = 'disable';
            }else{
                $v['action'] = 'enable';
            }
            $v['pics'] = explode(',',$v['pics']);
            array_push($list,$v);
        }
        $ret['list'] = $list;
        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $app_id = input('id/d');
        $need_fields = ['app_id','app_ename','app_name','app_uri','pics','app_icon_uri','app_desc','price_type','year_price','volume_price','is_system'];
        $w_app = [
            'app_id' => $app_id,
            'is_delete' => 0
        ];
        $mApps = db('app', 'db_center')->where($w_app)->field($need_fields)->find();
        if (empty($mApps)){
            return $this->sendSuccess();
        }
        $items = get_disabled_per_items();
        if (in_array($mApps['app_ename'],$items)){
            $mApps['action'] = 'disable';
        }else{
            $mApps['action'] = 'enable';
        }

        $client = gvar('client');
        $w_vca = [
            'cid' => $client['cid'],
            'is_delete' => 0,
            'app_id' => $app_id
        ];
        $mApps['pics'] = explode(',',$mApps['pics']);
        $mVca = db('vip_client_app', 'db_center')->where($w_vca)->find();
        if (empty($mVca)){
            $mApps['is_buy'] = 0;
        }else{
            $mApps['is_buy'] = 1;
            array_copy($mApps,$mVca,['expire_int_day','volume_limit','volume_used','buy_time','og_uid','status']);
            $mApps['buy_time'] = date('Y-m-d H:i:s',$mApps['buy_time']);
            $mApps['volume_limit'] = floor($mApps['volume_limit'] / 60);
            $mApps['volume_used'] = floor($mApps['volume_used'] / 60);
        }
        return $this->sendSuccess($mApps);
    }

    public function post(Request $request){
        $input = input();
        if (empty($input['app_ename'])){
            return $this->sendError(400,'params is null');
        }

        $apps = new App();
        $rs = $apps->updateAction($input['app_ename']);

        if ($rs === false){
            return $this->sendError(400, $apps->getError());
        }
        return $this->sendSuccess();
    }

}