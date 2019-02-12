<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/20
 * Time: 16:14
 */
namespace app\api\behavior;

use think\Db;

class Sms
{
    private $expire_time = 300;/*过期时间5分钟*/

    public function smsAfterSend($param)
    {
//        $data['mobile'] = $param['mobile'];
//        $data['content'] = $param['code'];
//        $data['status'] = 0;
//        $data['create_time'] = time();
//        Db::name('sms_history')->insert($data);
        unset($data);
        $data['mobile'] = $param['mobile'];
        $data['type'] = $param['type'];
        $data['code'] = $param['code'];
        $data['create_time'] = time();
        $data['expire_time'] = time() + $this->expire_time;
        Db::name('sms_vcode')->insert($data, true);
    }

    public function smsBeforeSend($param)
    {
        $w['mobile'] = $param['mobile'];
        $w['type']   = $param['type'];
        $last_record = Db::name('sms_vcode')->where($w)->find();
        $current_time = time();
        if ($current_time < $last_record['expire_time']) {
            return false;
        }
        return true;
    }
}