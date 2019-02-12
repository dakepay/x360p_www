<?php
/**
 * Author: luo
 * Time: 2018/6/15 12:18
 */

namespace app\api\model;


class EduGrowupPic extends Base
{

    public function addEduGrowupPic($data)
    {
        if(empty($data['egi_id']) || empty($data['eg_id'])) return $this->user_error('eg_id or egi_id error');

        $rs = $this->allowField(true)->isUpdate(false)->data([])->save($data);
        if($rs === false) return false;

        return true;
    }
}