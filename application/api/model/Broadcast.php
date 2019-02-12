<?php
/**
 * Author: luo
 * Time: 2017-11-24 17:22
**/

namespace app\api\model;

class Broadcast extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function setDptIdsAttr($value)
    {
        is_array($value) && $value = implode(',', $value);

        return $value;
    }

    public function  getDptIdsAttr($value)
    {
        return split_int_array($value);
    }

    public function setLidsAttr($value)
    {
        is_array($value) && $value = implode(',', $value);

        return $value;
    }

    public function  getLidsAttr($value)
    {
        return split_int_array($value);
    }

    public function setCidsAttr($value)
    {
        is_array($value) && $value = implode(',', $value);

        return $value;
    }

    public function  getCidsAttr($value)
    {
        return split_int_array($value);
    }

    public function setSidsAttr($value)
    {
        is_array($value) && $value = implode(',', $value);

        return $value;
    }

    public function  getSidsAttr($value)
    {
        return split_int_array($value);
    }


    public function user()
    {
        return $this->hasOne('User', 'uid', 'create_uid')->field('uid,name');
    }


    public function addBroadcast($input){
        $is_push = isset($input['is_push']) ? $input['is_push'] : 0;
        $this->startTrans();
        try {
            $result = $this->isUpdate(false)->allowField(true)->save($input);
            if (false === $result) {
                $this->rollback();
                return $this->sql_add_error('broadcast');
            }
            if ($is_push == 1){
                $broadcast = $this->getData();
                $rs = $this->pushBroadcast($broadcast);
//                if (false === $rs) {
//                    $this->rollback();
//                    return $this->user_error('push error');
//                }
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 推送公告
     * @param $backlog
     * @return bool
     */
    public function pushBroadcast($broadcast)
    {
        if (!$broadcast){
            return $this->user_error('公告不存在');
        }

        $mMessage = new Message();
        $scene = 'broadcast';
        $default_template_setting = config('tplmsg');
        $user_template_setting = isset(Config::userConfig()['wechat_template'][$scene]) ? Config::userConfig()['wechat_template'][$scene] : null;
        if (empty($user_template_setting)) {
            //客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置
            $user_template_setting = $default_template_setting[$scene];
        }

        $this->startTrans();
        try {
            $task_data['bc_id'] = $broadcast['bc_id'];
            $task_data['subject'] = '您已收到一条公告信息';
            $task_data['title'] = $broadcast['title'];
            $task_data['content'] = $broadcast['desc'];
            $task_data['date'] = $broadcast['create_time'];
            $task_data['url'] = isset($user_template_setting['weixin']['url']) ? $user_template_setting['weixin']['url'] : $default_template_setting['weixin']['url'];

            $user_list = $this->getPushUsers($broadcast);
            foreach ($user_list as $user){
                $task_data['uid'] = $user;
                $rs = $mMessage->sendTplMsg('broadcast',$task_data ,[],2);
                if($rs === false) return $this->user_error($mMessage->getError());
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 获取公告所要推送的用户
     * @param $broadcast
     * @return bool
     */
    protected function getPushUsers($broadcast)
    {
        $user_list = [];

        if (isset($broadcast['sids']) && $broadcast['sids'] != ''){
            $sids = split_int_array($broadcast['sids']);
            foreach ($sids as $sid){
                array_push($user_list,$sid);
            }
        }

        if (isset($broadcast['cids']) && $broadcast['cids'] != ''){
            $mClass = new Classes();
            $cids = split_int_array($broadcast['cids']);
            foreach ($cids as $cid){
                $student_list = [];
                $student_list = $mClass->getStudents($cid);
                foreach ($student_list as $student){
                    array_push($user_list,$student['sid']);
                }
            }
        }

        if (isset($broadcast['lids']) && $broadcast['lids'] != ''){
            $mLesson = new Lesson();
            $lids = split_int_array($broadcast['lids']);
            foreach ($lids as $lid){
                $classs_list = [];
                $classs_list = $mLesson->getClass($lid);
                $mClass = new Classes();
                foreach ($classs_list as $class){
                    $student_list = [];
                    $student_list = $mClass->getStudents($class['cid']);
                    foreach ($student_list as $student){
                        array_push($user_list,$student['sid']);
                    }
                }
            }

        }

        $user_list = array_values(array_unique($user_list));

        return $user_list;
    }



}