<?php
/**
 * Author: luo
 * Time: 2018/8/2 16:43
 */

namespace app\api\model;


class VipClientApp extends Base
{
    protected $connection = 'db_center';

    /**
     * 获得客户APP信息
     * @param $cid
     * @param $app_ename
     * @return array|false|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function GetClientAppInfo($cid,$app_ename)
    {
        $self = new self();
        $w = [
            'cid' => $cid,
            'app_ename' => $app_ename
        ];
        $app = $self->where($w)->find();

        if(!$app){
            return null;
        }

        return $app;
    }

    /**
     * @param $cid
     * @param $used_nums
     * @param $token
     * @param int $cacu_unit
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function UpdateWebCallSeconds($cid, $used_nums ,$token,$cacu_unit = 1)
    {
        $center_call_log_conn = db('webcall_call_log', 'db_center');
        $center_wcl = $center_call_log_conn->where('token', $token)->find();
        if($cacu_unit == 1){
            $used_billsecs = $used_nums * 60;
        }else{
            $used_billsecs = $used_nums;
        }
        if (!empty($center_wcl)) {
            $self = new self();
            $w = [
                'cid' => $cid,
                'app_ename' => 'webcall'
            ];
            $m_client_app = $self->where($w)->find();
            if(!$m_client_app){
                exception('client_app records does not exists!');
            }

            $m_client_app->volume_used = $m_client_app->volume_used + $used_billsecs;
            $rs = $m_client_app->save();

            if(false === $rs){
                exception('update database failured!');
            }
        }
        return true;
    }


}