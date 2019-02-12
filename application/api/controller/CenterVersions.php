<?php
/**
 * Author: luo
 * Time: 2018/2/2 14:26
 */

namespace app\api\controller;


use think\Db;
use think\Request;

class CenterVersions extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();

        $where['delete_time'] = null;
        if(isset($get['ver'])) {
            if(empty($get['ver'])) {
                $client_version = gvar('client') ? gvar('client')['info']['current_version'] : '';
                if(!empty($client_version)) {
                    $where['ver'] = ['elt', $client_version];
                }
            }
        }

        $conn = Db::connect(config('center_database'));
        $list = $conn->name('version')->where($where)->order('ver desc')->order('vid desc')
            ->limit(0, 10)->select();
        return $this->sendSuccess($list);
    }

}