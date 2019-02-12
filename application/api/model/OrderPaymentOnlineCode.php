<?php
/**
 * Author: luo
 * Time: 2018/3/14 12:02
 */

namespace app\api\model;

class OrderPaymentOnlineCode extends Base
{

    //生成付款码
    public function produceCode($oid, $aa_id, $paid_amount)
    {
        $two_hours_ago = strtotime('2 hours ago');
        $this->where('create_time', 'lt', $two_hours_ago)->delete(true);
        $this->where('oid', $oid)->delete(true);

        $new_code = substr($oid.$aa_id.strrev(time()), 0, 4);
        while(true) {
            $old_code_info = $this->where('code', $new_code)->find();
            if(empty($old_code_info)) break;
            $new_code = substr(str_shuffle('0123456789'), 0, 4);
        }

        $data = [
            'oid' => $oid,
            'aa_id' => $aa_id,
            'paid_amount' => $paid_amount,
            'code' => $new_code,
        ];

        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) return false;

        return $new_code;
    }

}