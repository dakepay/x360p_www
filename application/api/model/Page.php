<?php
/**
 * Author: luo
 * Time: 2018/5/21 16:39
 */

namespace app\api\model;


class Page extends Base
{

    public function delPage()
    {
        if(empty($this->getData())) return $this->user_error('页面数据错误');

        $child = $this->where('parent_pid', $this->page_id)->find();
        if(!empty($child)) return $this->user_error('有子页面，删除不了');

        $rs = $this->delete();
        if($rs === false) return false;

        return true;
    }

}