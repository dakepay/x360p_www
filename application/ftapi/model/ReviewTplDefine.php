<?php

namespace app\ftapi\model;

class ReviewTplDefine extends Base
{

    public function reviewTplSetting()
    {
        return $this->hasOne('ReviewTplSetting', 'rts_id', 'rts_id');
    }

    public function getAllTpl()
    {
        $list = $this->with('reviewTplSetting')->select();
        return $list;
    }

    public function addOneDefine($data)
    {
        $where = [
            'lid' => isset($data['lid']) ? $data['lid'] : 0,
            'sj_id' => isset($data['sj_id']) ? $data['sj_id'] : 0,
            'cid' => isset($data['cid']) ? $data['cid'] : 0,
        ];
        $is_exit = $this->where($where)->find();
        if(!empty($is_exit)) return $this->user_error('已经绑定过了');

        $rs = $this->isUpdate(false)->allowField(true)->save($data);
        if($rs === false) return $this->user_error('添加失败');

        return true;
    }

}