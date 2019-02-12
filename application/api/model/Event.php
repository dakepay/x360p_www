<?php
/**
 * Author: luo
 * Time: 2018/7/6 11:17
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class Event extends Base
{

    const STATUS_DISABLE = 0; # 禁用
    const STATUS_NORMAL = 1; # 正常
    const STATUS_FINISHED = 2; # 结束
    const STATUS_CANCEL = 3; # 取消

    protected $skip_og_id_condition = true;

    protected $type = [
        'event_start_time' => 'timestamp',
        'event_end_time' => 'timestamp'
    ];

    protected function setBidsAttr($value)
    {
        return $value && is_array($value) ? implode(',', $value) : $value;
    }

    protected function getBidsAttr($value)
    {
        return $value && is_string($value) ? explode(',', $value) : $value;
    }

    public function eventAttachment()
    {
        return $this->hasMany('EventAttachment', 'event_id', 'event_id');
    }

    public function eventSignUp()
    {
        return $this->hasMany('EventSignUp', 'event_id', 'event_id');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function addEvent($data)
    {

        try {
            $this->startTrans();

            $rs = $this->allowField(true)->isUpdate(false)->save($data);
            if($rs === false) throw new FailResult($this->getErrorMsg());
            $event_id = $this->event_id;

            if(!empty($data['event_attachment']) && is_array($data['event_attachment'])) {
                $m_ea = new EventAttachment();
                $m_file = new File();
                foreach($data['event_attachment'] as $row_data) {
                    if(isset($row_data['file_id'])) {
                        $file = $m_file->find($row_data['file_id']);
                        $row_data = array_merge($row_data, $file->toArray());
                    }
                    $row_data['event_id'] = $event_id;
                    $rs = $m_ea->data([])->allowField(true)->isUpdate(false)->save($row_data);
                    if($rs === false) throw new FailResult('添加活动附件失败');
                }
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function updateEvent($data)
    {
        if(empty($this->getData())) return $this->user_error('活动数据为空');

        try {
            $this->startTrans();
            $rs = $this->allowField(true)->isUpdate(true)->save($data);
            if ($rs === false) throw new FailResult($this->getErrorMsg());

            $event_attachment = !empty($data['event_attachment']) ? $data['event_attachment'] : [];
            if (!empty($event_attachment) && is_array($event_attachment)) {
                $old_file_ids = $this->eventAttachment()->column('file_id');
                $new_file_ids = array_column($event_attachment, 'file_id');
                $del_file_ids = array_diff($old_file_ids, $new_file_ids);
                $add_file_ids = array_diff($new_file_ids, $old_file_ids);

                $rs = $this->eventAttachment()->where('file_id', 'in', $del_file_ids)->delete();
                if($rs === false) throw new FailResult($this->getErrorMsg());

                $m_file = new File();
                $m_ea = new EventAttachment();
                foreach ($add_file_ids as $per_file_id) {
                    $file = $m_file->find($per_file_id);
                    if(empty($file)) throw new FailResult('文件不存在');

                    $row_data['event_id'] = $this->event_id;
                    $row_data = array_merge($row_data, $file->toArray());
                    $rs = $m_ea->data([])->allowField(true)->isUpdate(false)->save($row_data);
                    if ($rs === false) throw new FailResult($m_ea->getErrorMsg());
                }
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function delEvent($is_force = 0)
    {
        if(empty($this->getData())) return $this->user_error('活动模型数据为空');

        if(!$is_force) {
            $m_esu = new EventSignUp();
            $sign_up_num = $m_esu->where('event_id', $this->event_id)->count();
            if($sign_up_num > 0) {
                return $this->user_error('已经有人报名，是否强制删除？', self::CODE_HAVE_RELATED_DATA);
            }
        }

        try {
            $this->startTrans();
            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $rs = $this->eventAttachment()->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $rs = $this->eventSignUp()->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        
        return true;
    }

    public static function AutoCheckStatus()
    {
        $now_time = time();
        $self = new self();
        $rs = $self->where('event_end_time', 'elt', $now_time)->where('status', self::STATUS_NORMAL)
            ->update(['status' => self::STATUS_FINISHED]);
        return $rs;
    }


}