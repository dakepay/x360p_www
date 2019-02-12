<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/25
 * Time: 15:29
 */
namespace app\api\model;

class ClientUser extends Base
{
    protected $connection = 'db_center';

    /**
     * 根据student手机号码创建一个学习管家账号后在center数据库client_user表中添加一条记录
     * 应用场景：当创建一条student记录的时候根据student记录的手机号码创建家长账号（user表），然后根据模型事件调用下面的方法.
     * @param User $student_user
     * @return $this
     */
    public static function createRecordAfterCreateStudentAccount(User $student_user)
    {
        //如果不创建中心账号引用泽不处理
        $not_create_center_referer = gvar('not_create_center_referer');

        if($not_create_center_referer){
            return false;
        }
        $client = gvar('client');
        $data['cid']   = $client['cid'];
        $data['og_id'] = $client['og_id'];
        //$data['uid']   = $student_user['uid'];
        $data['account'] = $student_user['account'];
        $exist_record = self::get($data);
        if ($exist_record) {
            $exist_record->uid = $student_user['uid'];
            $exist_record->allowField('uid')->save();
            return $exist_record;
        } else {
            return self::create($data);
        }
    }

    /**
     * 更新student_user的手机号码（等同于account）的时候同步更新center数据库client_user表
     * 应用场景：变更student预留的手机号码的时候同步更新user表和client_user表中的手机号码
     * @param User $student_user
     */
    public static function updateRecordAfterUpdateStudentAccount(User $student_user)
    {
        $client = gvar('client');
        $data['account'] = $student_user['account'];
        $w = [];
        $w['cid']   = $client['cid'];
        $w['og_id'] = $client['og_id'];
        $w['uid']   = $student_user['uid'];
        return self::update($data, $w);
    }
}