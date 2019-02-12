<?php
/**
 * Author: luo
 * Time: 2018/5/22 16:46
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class ServiceTask extends Base
{
    const OBJECT_TYPE_CUSTOMER = 0; # 客户
    const OBJECT_TYPE_STUDENT = 1; # 学员
    const OBJECT_TYPE_CLASS = 2; # 班级

    const STATUS_TO_DO = 0; # 待办
    const STATUS_FINISH = 1; # 完成
    const STATUS_CANCEL = -1; # 取消

    public function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setIntHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

    public function customer()
    {
        return $this->hasOne('Customer', 'cu_id', 'cu_id')->field('cu_id,name');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function ownEmployee()
    {
        return $this->hasOne('Employee', 'eid', 'own_eid')->field('eid,ename');
    }

    public function addServiceTask($post)
    {

        $object_type = 0;
        if(!empty($post['cu_id'])) {
            $object_type = self::OBJECT_TYPE_CUSTOMER;
        } elseif(!empty($post['cid'])) {
            $object_type = self::OBJECT_TYPE_CLASS;
            // 添加一条 班级 服务安排日志
            ClassLog::addClassServiceTaskLog($post);
        } elseif(!empty($post['sid'])) {
            $object_type = self::OBJECT_TYPE_STUDENT;
            // 添加一条任务安排操作记录
            StudentLog::addServiceTaskLog($post);
        }

        $post['object_type'] = $object_type;

        $this->startTrans();
        try{
            $rs = $this->allowField(true)->isUpdate(false)->data([])->save($post);
            if($rs === false){
                $this->rollback();
                return $this->sql_add_error('service_task');
            }

            

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        
        return true;
    }

    public function updateServiceTask($data)
    {
        if(empty($this->getData())) return $this->user_error('模型数据为空');

        try {
            $this->startTrans();
            if(!empty($data['status']) && $data['status'] == self::STATUS_FINISH && $this->status != self::STATUS_FINISH) {
                $record_data = $this->getData();
                $record_data['eid'] = $record_data['own_eid'];
                $record_data['content'] = $record_data['remark'];
                $m_sr = new ServiceRecord();
                $rs = $m_sr->addServiceRecord($record_data);
                if($rs === false) throw new FailResult($m_sr->getErrorMsg());
            }

            $rs = $this->allowField(true)->isUpdate(true)->save($data);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(),$e);
        }

        return true;
    }


}