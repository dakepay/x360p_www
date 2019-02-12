<?php
/**
 * Author: luo
 * Time: 2017-11-21 17:27
**/

namespace app\api\model;

class TallyHelp extends Base
{

    const TYPE_CLIENT = 'client';       # 往来客户
    const TYPE_EMPLOYEE = 'employee';   # 核算员工
    const TYPE_ITEM = 'item';           # 核算项目

    //增加一个辅助核算
    public function addOneCate($data)
    {
        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->user_error('添加核算项目失败');

        return true;
    }

}