<?php
/**
 * Author: luo
 * Time: 2018/3/20 15:52
 */

namespace app\api\model;


use app\common\exception\FailResult;
use app\common\Wechat;
use think\Exception;

class HomeworkTask extends Base
{

    const LESSON_TYPE_CLASS = 0; # 班课作业
    const LESSON_TYPE_ONE = 1;   # 一对一作业
    const LESSON_TYPE_MANY = 2;   # 一对多作业

    //protected $append = ['students_count'];
    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];


    public function setSidsAttr($value)
    {
        return !empty($value) && is_array($value) ? implode(',', $value) : $value;
    }

    public function setDeadlineAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function getSidsAttr($value)
    {
        return is_string($value) ? explode(',', $value) : $value;
    }

    //作业的多学员信息
    public function getStudentsAttr($value, $data)
    {
        $students = [];
        if(isset($data['sids']) && !empty($data['sids'])) {
            $sids = is_array($data['sids']) ? $data['sids'] : explode(',', $data['sids']);
            $students = (new Student())->where('sid', 'in', $sids)->field('sid,bid,student_name,sex,photo_url')->select();
        }
        return $students;
    }

    public function getStudentsCountAttr($value, $data)
    {
        $sids = [];
        if($data['lesson_type'] == self::LESSON_TYPE_CLASS) {
            $m_cs = new ClassStudent();
            $sids = $m_cs->where('status', ClassStudent::STATUS_NORMAL)->where('cid', $data['cid'])
                ->column('sid');
        } elseif ($data['lesson_type'] == self::LESSON_TYPE_ONE) {
            $sids = $data['sid'] > 0 ? [$data['sid']] : [];
        } elseif ($data['lesson_type'] == self::LESSON_TYPE_MANY) {
            $sids = is_array($data['sids']) ? $data['sids'] : explode(',', $data['sids']);
        }

        return count($sids);
    }

    public function getStudentsCount($ht_id)
    {
        $data = $this->getData();
        if(empty($data)) {
            $data = $this->where('ht_id',$ht_id)->find();
        }

        $sids = [];
        if($data['lesson_type'] == self::LESSON_TYPE_CLASS) {
            $m_cs = new ClassStudent();
            $sids = $m_cs->where('status', ClassStudent::STATUS_NORMAL)->where('cid', $data['cid'])
                ->column('sid');
        } elseif ($data['lesson_type'] == self::LESSON_TYPE_ONE) {
            $sids = $data['sid'] > 0 ? [$data['sid']] : [];
        } elseif ($data['lesson_type'] == self::LESSON_TYPE_MANY) {
            $sids = is_array($data['sids']) ? $data['sids'] : explode(',', $data['sids']);
        }

        return count($sids);
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,bid,student_name,sex,photo_url');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,bid,class_name,lid');
    }

    public function homeworkAttachment()
    {
        return $this->hasMany('HomeworkAttachment', 'ht_id', 'ht_id');
    }

    public function homeworkComplete()
    {
        return $this->hasMany('HomeworkComplete', 'ht_id', 'ht_id');
    }

    //添加作业
    public function addOneHomework($data, $attachment_data = [])
    {
        try {
            $this->startTrans();

            if(empty($data['htts_id']) && empty($data['content'])) throw new FailResult('参数错误');
            if(empty($data['sid']) && empty($data['sids']) && empty($data['cid'])) throw new FailResult('作业对象错误');

            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false) throw new FailResult($this->getErrorMsg());

            $ht_id = $this->ht_id;

            if (!empty($attachment_data) && is_array($attachment_data)) {
                $m_ha = new HomeworkAttachment();
                $m_file = new File();
                foreach ($attachment_data as $row_data) {
                    if (isset($row_data['file_id'])) {
                        $file = $m_file->find($row_data['file_id']);
                        $row_data = array_merge($row_data, $file->toArray());
                    }
                    $row_data['ht_id'] = $ht_id;
                    $rs = $m_ha->data([])->allowField(true)->isUpdate(false)->save($row_data);
                    if ($rs === false) throw new FailResult('添加作业附件失败');
                }
            }

            add_service_record('homework', ['sid' => $data['sid'] ?? 0, 'cid' => $data['cid'] ?? 0, 'st_did' => 233]);

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //删除作业
    public function delHomework()
    {
        if(!isset($this->ht_id)) return $this->user_error('作业数据错误');
        $complete = HomeworkComplete::get(['ht_id' => $this->ht_id]);
        if(!empty($complete)) return $this->user_error('学生已经完成作业，无法删除');

        try {
            $this->startTrans();
            $m_ha = new HomeworkAttachment();
            $rs = $m_ha->where('ht_id', $this->ht_id)->delete();
            if ($rs === false) throw new FailResult('删除作业附件失败');

            $rs = $this->delete();
            if ($rs === false) throw new FailResult('删除作业失败');

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function getStudentsOfHomework($ht_id)
    {
        $homework = $this->find($ht_id);
        if(empty($homework)) return $this->user_error('作业数据为空');

        $sids = [];
        if($homework['lesson_type'] == self::LESSON_TYPE_CLASS) {
            $m_cs = new ClassStudent();
            $sids = $m_cs->where('status', ClassStudent::STATUS_NORMAL)->where('cid', $homework['cid'])->column('sid');
        } elseif ($homework['lesson_type'] == self::LESSON_TYPE_ONE) {
            $sids = $homework['sid'] > 0 ? [$homework['sid']] : [];
        } elseif ($homework['lesson_type'] == self::LESSON_TYPE_MANY) {
            $sids = $homework['sids'];
        }

        $students = [];
        if(!empty($sids)) {
            $students = (new Student())->field('sid,bid,student_name,sex,photo_url')->where('sid', 'in', $sids)->select();
        }

        return $students;
    }

    //微信通知内容
    public function make_wechat_data()
    {
        if(empty($this->getData())) return $this->user_error('作业数据为空');

        if(!empty($this->lid)) {
            $msg_data['lesson_name'] = get_lesson_name($this->lid);
        } else {
            $msg_data['lesson_name'] = get_class_name($this->cid);
        }
        $msg_data['homework_name'] = get_employee_name($this->eid) . '布置了作业';
        $msg_data['detail'] = date('Y-m-d', $this->getData('create_time'));

        $msg_data['ht_id'] = $this->ht_id;

        return $msg_data;
    }

    //推送微信通知
    public function wechat_tpl_notify($scene, $msg_data, $students)
    {
        //--1-- 模板信息链接字段检查
        $default_template_setting = config('tplmsg')[$scene];
        preg_match_all('/\{([^\}]+)\}/',$default_template_setting['weixin']['url'],$matches);
        if(isset($matches[1]) && !empty($matches[1])) {
            foreach($matches[1] as $field) {
                if($field == 'base_url') continue;

                if(!isset($msg_data[$field])) return $this->user_error($field.'模板信息链接字段不能为空');
            }
        }

        //--2-- 模板信息字段检查
        $temp = [];
        foreach($default_template_setting['tpl_fields'] as $field => $val) {
            if(!isset($msg_data[$field])) return $this->user_error($field.'模板信息字段不能为空');
            $temp[$field] = $msg_data[$field];
        }

        $wechat = Wechat::getInstance(Wechat::getAppidByBid(request()->bid));
        $message['appid'] = $wechat->appid;

        $message['url'] = tplmsg_url($default_template_setting['weixin']['url'],$msg_data);

        //--3-- 处理模板id
        if ($wechat->default) {
            $message['template_id'] = $default_template_setting['weixin']['template_id'];
        } else {
            $w = [];
            $w['appid'] = $message['appid'];
            $w['scene'] = $scene;
            $target_tpl = WxmpTemplate::get($w);
            if (empty($target_tpl)) {
                //该公众号还没有成功设置该模板.
                return $this->user_error('公众号还没有设置作业推送模板');
            }
            $message['template_id'] = $target_tpl['template_id'];
        }

        $user_template_setting = isset(Config::userConfig()['wechat_template'][$scene]) ? Config::userConfig()['wechat_template'][$scene] : null;
        if (empty($user_template_setting)) {
            //客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置
            $user_template_setting = $default_template_setting;
        }

        //--4-- 模板消息内容替换
        $search  = array_values($user_template_setting['tpl_fields']);
        $replace = array_values($temp);

        $data = $user_template_setting['weixin']['data'];
        foreach ($data as &$subject) {
            $subject = str_replace($search, $replace, $subject);
        }
        $sms_message = str_replace($search, $replace, $user_template_setting['sms']['tpl']);
        $message['data'] = $data;

        //--5-- 准备发送消息
        $inner_message = [];
        $inner_message['business_type'] = $scene;
        $inner_message['business_id'] = isset($msg_data['ht_id']) ? $msg_data['ht_id'] : 0;
        $inner_message['title']   = $default_template_setting['message']['title'];
        $inner_message['content'] = str_replace($search, $replace, $default_template_setting['message']['content']);
        $inner_message['url']     = $message['url'];
        foreach ($students as $student) {
            if(!($student instanceof Student)) {
                $student = Student::get($student['sid']);
            }
            $users = $student->user;
            if(empty($users)) continue;
            /** @var User $per_user */
            foreach($users as $per_user) {
                if(empty($per_user->getData())) continue;

                $inner_message['uid'] = $per_user['uid'];
                $inner_message['sid'] = $student->sid;
                Message::create($inner_message);
                if ($per_user['mobile'] && $user_template_setting['sms_switch']) {
                    queue_push('SendSmsMsg', [$per_user['mobile'], $sms_message]);
                }
                if ($per_user['openid'] && $user_template_setting['weixin_switch']) {
                    $w = [];
                    $w['openid'] = $per_user['openid'];
                    $w['subscribe'] = WxmpFans::SUBSCRIBE;
                    if (WxmpFans::get($w)) {
                        $message['openid'] = $per_user['openid'];
                        queue_push('SendWxTplMsg', $message);
                    }
                }
            }

        }

        return true;
    }

    //编辑
    public function edit($data, $attachment_data = [])
    {
        if(empty($this->getData())) return $this->user_error('作业数据为空');

        try {
            $this->startTrans();
            $rs = $this->allowField(true)->isUpdate(true)->save($data);
            if ($rs === false) throw new FailResult('更新失败');

            if (!empty($attachment_data) && is_array($attachment_data)) {
                $old_file_ids = $this->homeworkAttachment()->column('file_id');
                $new_file_ids = array_column($attachment_data, 'file_id');
                $del_file_ids = array_diff($old_file_ids, $new_file_ids);
                $add_file_ids = array_diff($new_file_ids, $old_file_ids);

                $rs = $this->homeworkAttachment()->where('file_id', 'in', $del_file_ids)->delete();
                if($rs === false) throw new FailResult('删除原附件失败');
                
                $m_file = new File();
                $m_ha = new HomeworkAttachment();
                foreach ($add_file_ids as $per_file_id) {
                    $file = $m_file->find($per_file_id);
                    if(empty($file)) throw new FailResult('文件不存在');

                    $row_data['ht_id'] = $this->ht_id;
                    $row_data = array_merge($row_data, $file->toArray());
                    $rs = $m_ha->data([])->allowField(true)->isUpdate(false)->save($row_data);
                    if ($rs === false) throw new FailResult('添加作业附件失败');

                }
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}