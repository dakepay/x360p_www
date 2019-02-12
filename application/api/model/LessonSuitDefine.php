<?php
/**
 * Author: luo
 * Time: 2018/5/25 16:40
 */

namespace app\api\model;


class LessonSuitDefine extends Base
{

    protected $append = ['create_employee_name'];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $type = [
        'define' => 'json'
    ];

    public function addLessonSuitDefine($data)
    {
        if(empty($data['name'])) return $this->user_error('套餐名字不能为空');

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return false;

        return true;
    }

    public function delLessonSuitDefine()
    {
        if(empty($this->getData())) return $this->user_error('学习套餐数据错误');

        $relate_data = LessonBuySuit::get(['lsd_id' => $this->lsd_id]);
        if(!empty($relate_data)) return $this->user_error('该学习套餐已被学习方案引用，无法删除');

        $rs = $this->delete();
        if($rs === false) return false;

        return true;
    }


}