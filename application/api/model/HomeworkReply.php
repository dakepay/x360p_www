<?php
/**
 * Author: luo
 * Time: 2018/3/27 11:48
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class HomeworkReply extends Base
{
    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function homeworkAttachment()
    {
        return $this->hasMany('HomeworkAttachment', 'hr_id', 'hr_id');
    }

    public function addReply($data, $attachment_data = [])
    {
        if(empty($data['hc_id']) || empty($data['eid'])) return $this->user_error('hc_id或者eid错误');

        $complete = HomeworkComplete::get($data['hc_id']);
        if(empty($complete)) return $this->user_error('完成的作业不存在');

        try {
            $this->startTrans();

            $rs = $this->allowField(true)->data($data)->isUpdate(false)->save($data);
            if ($rs === false) throw new FailResult($this->getErrorMsg());

            $hr_id = $this->hr_id;

            $reply_data = [
                'star' => isset($data['star']) ? $data['star'] : $complete['star'],
                'is_check' => 1,
                'check_time' => time(),
                'check_uid' => Employee::getUidByEid($data['eid']),
                'check_level' => isset($data['check_level']) ? $data['check_level'] : 0,
                'check_content' => isset($data['content']) ? $data['content'] : '',
                'result_level' => isset($data['result_level']) ? $data['result_level'] : 0,
            ];
            $rs = $complete->allowField(true)->isUpdate(true)->save($reply_data);
            if($rs === false) throw new FailResult('更新完成的作业失败');

            if (!empty($attachment_data)) {
                $m_ha = new HomeworkAttachment();
                foreach ($attachment_data as $row) {
                    if (!isset($row['file_id'])) throw new FailResult('file_id error');
                    $file = File::get($row['file_id']);
                    $file = $file->toArray();
                    $file['hr_id'] = $hr_id;
                    $file['att_type'] = $m_ha::ATT_TYPE_REPLY;
                    $rs = $m_ha->data([])->allowField(true)->isUpdate(false)->save($file);
                    if ($rs === false) throw new FailResult('附件保存失败');
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