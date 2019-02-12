<?php
/**
 * Author: luo
 * Time: 2018/5/22 17:04
 */

namespace app\api\model;


use app\common\exception\FailResult;

class ServicePushTask extends Base
{
    const OBJECT_TYPE_CUSTOMER = 0; # 客户
    const OBJECT_TYPE_STUDENT = 1; # 学员
    const OBJECT_TYPE_CLASS = 2; # 班级

    const CONTENT_TYPE_FILE_PACKAGE = 'file_package';
    const CONTENT_TYPE_LINK= 'link';
    const CONTENT_TYPE_PAGE= 'page';

    public function addTask($post, $push_now = false)
    {
        if(empty($post['sid']) && empty($post['cu_id']) && empty($post['cid'])) {
            return $this->user_error('推送对象错误');
        }

        $object_type = 0;
        if(!empty($post['cu_id'])) {
            $object_type = self::OBJECT_TYPE_CUSTOMER;
        } elseif(!empty($post['cid'])) {
            $object_type = self::OBJECT_TYPE_CLASS;
        } elseif(!empty($post['sid'])) {
            $object_type = self::OBJECT_TYPE_STUDENT;
        }

        if(isset($post['content_type']) && $post['content_type'] == self::CONTENT_TYPE_FILE_PACKAGE) {
            $file_package = FilePackage::get($post['rel_id']);
            if(!empty($file_package) && !empty($file_package['short_id'])) {
                $post['url'] = request()->domain() . '/student#/fp/' . $file_package['short_id'];
            }
        }

        $post['object_type'] = $object_type;
        $post['is_push'] = 0;

        $where_frequently = [
            'sid' => $post['sid'] ?? 0,
            'cu_id' => $post['cu_id'] ?? 0,
            'cid' => $post['cid'] ?? 0,
            'rel_id' => $post['rel_id'] ?? 0,
            'create_time' => ['gt', time() - 300]
        ];

        $service_push_task = $this->where($where_frequently)->find();
        if(!empty($service_push_task)) return $this->user_error('5分钟内已经发过');

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($post);
        if($rs === false) return false;

        if($push_now) {
            $this->pushTask($this->spt_id);
        }

        return true;
    }

    public function pushTask($spt_id)
    {
        $task_data = self::get($spt_id, [], true);
        if(empty($task_data)) return true;
        $task_data = $task_data->toArray();

        $success_num = 0;
        $fail_num = 0;

        $subject = '老师给你推送了新的资料查阅';
        $m_message = new Message();
        if(!empty($task_data['sid'])) {
            try {
                $task_data['subject'] = $subject;
                $task_data['remark'] = isset($task_data['remark']) ? $task_data['remark'] : '查看详情';
                $rs = $m_message->sendTplMsg('to_do', $task_data);
                if($rs === false) throw new FailResult($m_message->getErrorMsg());
            } catch(\Exception $e) {
                log_write($e->getFile() . ' ' . $e->getLine() . ' '. $e->getMessage(), 'error');
                $fail_num += 1;
            }

            $success_num += 1;

        } elseif(!empty($task_data['cid'])) {
            $sids = ClassStudent::GetSidsOfClass($task_data['cid']);
            foreach($sids as $sid) {
                $task_data['sid'] = $sid;
                try {
                    $rs = $m_message->sendTplMsg('to_do', $task_data);
                    if($rs === false) throw new FailResult($m_message->getErrorMsg());
                } catch(\Exception $e) {
                    log_write($e->getMessage(), 'error');
                    $fail_num += 1;
                    continue;
                }

                $success_num += 1;
            }
        }

        $update_data = [
            'is_push' => 1,
            'push_time' => time(),
            'push_success_nums' => $success_num,
            'push_failure_nums' => $fail_num,
            'spt_id' => $spt_id,
        ];
        $rs = $this->allowField(true)->isUpdate(true)->save($update_data);
        if($rs === false) return false;

        return true;
    }

    public function delTask()
    {
        if(empty($this->getData())) return $this->user_error('推送任务数据错误');

        $rs = $this->delete();
        if($rs === false) return false;

        return true;
    }

}