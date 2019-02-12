<?php
/**
 * Author: luo
 * Time: 2018/4/18 15:30
 */

namespace app\sapi\model;


class CommentClick extends Base
{

    public function click($data)
    {
        if(empty($data['cmt_id'])) return $this->user_error('cmt_id error');
        if(empty($data['sid']) && empty($data['eid'])) return $this->user_error('缺少点击人id');

        $comment = Comment::get($data['cmt_id']);
        if(empty($comment)) return $this->user_error('评论不存在');

        $w_cc = [
            'cmt_id' => $data['cmt_id'],
            'sid' => $data['sid'] ?? 0,
            'eid' => $data['eid'] ?? 0,
        ];

        $click = $this->where($w_cc)->find();
        if(!empty($click)) return true;

        $rs = $this->data([])->isUpdate(false)->save($data);
        if($rs === false) return false;

        $comment->up_num = $comment->up_num + 1;
        $comment->allowField('up_num')->isUpdate(true)->save();
        
        return $rs;
    }

}