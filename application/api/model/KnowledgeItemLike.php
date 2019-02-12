<?php
/**
 * Author: luo
 * Time: 2018/6/5 18:46
 */

namespace app\api\model;


class KnowledgeItemLike extends Base
{
    protected $skip_og_id_condition = true;

    //点赞
    public function like($post)
    {
        if(empty($post['ki_id']) || empty($post['eid'])) return $this->user_error('ki_id或eid错误');

        $is_exist = $this->where(['ki_id' => $post['ki_id'], 'eid' => $post['eid']])->find();
        if(!empty($is_exist)) return true;

        $rs = $this->allowField(true)->isUpdate(false)->save($post);
        if($rs === false) return false;

        KnowledgeItem::UpdateStars($post['ki_id']);

        return true;
    }

    //取消点选
    public function cancelLike($post)
    {
        if(empty($post['ki_id']) || empty($post['eid'])) return $this->user_error('ki_id或eid错误');
        $rs = $this->where('ki_id', $post['ki_id'])->where('eid', $post['eid'])->delete();
        if($rs === false) return false;

        KnowledgeItem::UpdateStars($post['ki_id']);

        return true;
    }

}