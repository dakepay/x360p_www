<?php
/**
 * Author: luo
 * Time: 2018-01-10 16:13
**/

namespace app\api\model;

class CourseRemindLog extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    //添加一条课前提醒记录
    public function addOneLog($data)
    {
        if(!isset($data['sid']) || !isset($data['ca_id'])) return $this->user_error('param error');

        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return $this->user_error('提醒记录失败');

        return true;
    }

}