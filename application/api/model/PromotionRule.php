<?php
namespace app\api\model;

class PromotionRule extends Base
{

    public function setSuitBidsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : 0;
    }

    public function getSuitBidsAttr($value)
    {
        return split_int_array($value);
    }

    public function setSuitLidsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : 0;
    }

    public function getSuitLidsAttr($value)
    {
        return split_int_array($value);
    }

    public function setSuitSjIdsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : 0;
    }

    public function getSuitSjIdsAttr($value)
    {
        return split_int_array($value);
    }

    public function setStartTimeAttr($value)
    {
        return strtotime($value,time());
    }

    public function setEndTimeAttr($value)
    {
        return strtotime($value,time());
    }

    public function getStartTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    public function getEndTimeAttr($value)
    {
        return date('Y-m-d H:i:s',$value);
    }

    /**
     * 添加促销规则
     * @param $post
     */
    public function addPromotionRule($post)
    {

        $result = $this->isUpdate(false)->allowField(true)->save($post);
        if (false === $result){
            return $this->sql_add_error('promotion_rule');
        }

        return true;
    }

    /**
     * 修改促销规则
     * @param $pr_id
     * @param $put
     */
    public function updatePromotionRule($pr_id,$put)
    {
        $promotion_rule = $this->where('pr_id',$pr_id)->find();
        if (empty($promotion_rule)){
            return $this->user_error('促销规则不存在!');
        }

        $w['pr_id'] = $pr_id;
        $result = $this->allowField(true)->save($put,$w);
        if (false == $result){
            return $this->sql_save_error('promotion_rule');
        }

        return true;
    }

    /**
     * 删除促销规则
     * @param $pr_id
     */
    public function delPromotionRule($pr_id)
    {
        $promotion_rule = $this->where('pr_id',$pr_id)->find();
        if (empty($promotion_rule)){
            return $this->user_error('促销规则不存在!');
        }

        $result = $promotion_rule->delete();
        if (false == $result){
            return $this->sql_delete_error('promotion_rule');
        }

        return true;
    }

    /**
     * 修改促销状态
     * @param $pr_id
     * @param $status
     */
    public function updatePromotionStatus($pr_id,$status)
    {
        $status = intval($status);
        if ($status != 0 && $status != 1){
            return $this->user_error('status');
        }

        $promotion_rule = $this->where('pr_id',$pr_id)->find();
        if (empty($promotion_rule)){
            return $this->user_error('促销规则不存在!');
        }

        $w['pr_id'] = $pr_id;
        $update['status'] = $status;
        $result = $this->save($update,$w);
        if (false == $result){
            return $this->sql_save_error('promotion_rule');
        }

        return true;
    }
}