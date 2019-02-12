<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/21
 * Time: 10:33
 */
namespace app\api\behavior;

use think\Db;
use think\Log;

class Email
{
    public function emailAfterSend($param)
    {
        Log::record('邮件发送成功，正在插入数据库!');
        if (!empty($param['is_vcode'])) {
            unset($param['is_vcode']);
            $param['expire_time'] = time() + 180;
            Db::name('email_vcode')->field(['email', 'type', 'code', 'expire_time'])->insert($param, true);
            $param['content'] = $param['code'];
            unset($param['code'], $param['expire_time'], $param['is_vcode']);
        }
        $param['status'] = 1;
        Db::name('email_history')->strict(false)->field('email,content,status')->insert($param);
        return true;
    }
}