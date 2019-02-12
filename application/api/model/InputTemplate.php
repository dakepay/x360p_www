<?php
/** 
 * Author: luo
 * Time: 2017-10-30 14:48
**/


namespace app\api\model;


class InputTemplate extends Base
{

    const TYPE_ORDER = 1; # 报名模板
    const TYPE_TALLY = 2; # 记账模板

    protected $type = [
        'template' => 'json'
    ];

    public function createOneTemplate($data)
    {
        $data['create_uid'] = gvar('uid');
        $rs = $this->data([])->validate(true)->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->user_error($this->getError());

        return true;
    }


}