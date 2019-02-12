<?php
/**
 * Author: luo
 * Time: 2018/6/14 14:40
 */

namespace app\api\model;


class CreditRule extends Base
{
    protected $type = [
        'rule' => 'json',
    ];

    public function addCreditRule($post)
    {
        $rs = $this->validate()->allowField(true)->isUpdate(false)->save($post);
        if($rs === false) return false;

        return true;
    }

    public function updateCreditRule($data)
    {
        if(empty($this->getData())) return $this->user_error('积分规则模型数据错误');

        $rs = $this->validate()->allowField(true)->isUpdate(true)->save($data);
        if($rs === false) return false;

        return true;
    }

    public function delCreditRule()
    {
        if(empty($this->getData())) return $this->user_error('模型数据错误');
        if($this->getData('is_system')) return $this->user_error('系统规则，无法删除');

        $rs = $this->delete();
        if($rs === false) return false;

        return true;
    }

    //下单付款积分
    public function get_order_pay_credit($data, $rule)
    {
        if(empty($data['amount'])) return $this->user_error('订单金额不对');

        if(empty($rule['amount']) || empty($rule['credit'])) return $this->user_error('积分规则错误');

        $credit = round(($data['amount'] / $rule['amount']) * $rule['credit'], 2);

        return $credit;
    }


    //点评积分
    public function get_review_credit($data, $rule)
    {
        if(empty($data['star'])) return $this->user_error('点评星级不对');

        if(empty($rule)) return $this->user_error('积分规则错误');

        $credit = 0;
        if(is_array($rule)) {
            foreach($rule as $row) {
                if(!empty($row['star']) && $row['star'] == $data['star']) {
                    $credit = isset($row['credit']) ? $row['credit'] : 0;
                    break;
                }
            }
        }

        return $credit;
    }


}