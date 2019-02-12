<?php
/**
 * Author: luo
 * Time: 2018/4/12 9:28
 */

namespace app\api\model;


class StudentArtworkReview extends Base
{

    public function addReview($data)
    {
        if(empty($data['content'])) return $this->user_error('点评内容不能为空');

        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return $this->user_error($this->getErrorMsg());

        return true;
    }
}