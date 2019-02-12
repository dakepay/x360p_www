<?php
/**
 * Author: luo
 * Time: 2018-01-10 16:13
**/

namespace app\api\model;

class CourseAutoPlanLog extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    //添加一条课前提醒记录
    public function addOneLog($og_id,$bid)
    {
        if(!isset($og_id) || !isset($bid)) return $this->user_error('param error');
        $data = [
            'og_id' => $og_id,
            'bid' => $bid,
        ];

        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return $this->user_error('提醒记录失败');

        return true;
    }

}