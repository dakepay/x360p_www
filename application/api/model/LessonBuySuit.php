<?php
/**
 * Author: luo
 * Time: 2018/5/25 17:05
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class LessonBuySuit extends Base
{
    protected $type = ['define' => 'json'];

    protected $append = ['create_employee_name'];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url,first_tel');
    }

    public function customer()
    {
        return $this->hasOne('Customer', 'cu_id', 'cu_id')->field('cu_id,name,first_tel');
    }

    public function lessonSuitDefine()
    {
        return $this->hasOne('LessonSuitDefine', 'lsd_id', 'lsd_id');
    }

    public function addLessonBuySuit($data)
    {
        if(empty($data['sid']) && empty($data['cu_id'])) return $this->user_error('sid或者cu_id');

        try {
            $rs = $this->allowField(true)->isUpdate(false)->data([])->save($data);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $lbs_id = $this->lbs_id;

            if(!empty($data['ss_id'])) {
                $m_ss = new StudySituation();
                $rs = $m_ss->where('ss_id', $data['ss_id'])->update(['lsb_id' => $this->lsb_id]);
                if($rs === false) throw new FailResult($m_ss->getErrorMsg());
            }
        } catch(Exception $e) {
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $lbs_id;
    }


}