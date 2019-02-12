<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\EmployeeReceipt;


class EmployeeReceipts extends Export
{
    protected $res_name = 'employee_receipt';

    protected $columns = [
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'eid','title'=>'员工','width'=>20],
        ['field'=>'amount','title'=>'业绩金额','width'=>20],
        ['field'=>'consume_type','title'=>'业绩类型','width'=>30],
        ['field'=>'sale_role_did','title'=>'销售角色','width'=>20],
        ['field'=>'sid','title'=>'学员姓名','width'=>20],
        ['field'=>'orb_id','title'=>'收据号','width'=>30],
        ['field'=>'oid','title'=>'订单号','width'=>30],
        ['field'=>'receipt_time','title'=>'业绩日期','width'=>40],
        ['field'=>'create_time','title'=>'录入日期','width'=>40]
    ];

    protected function get_title(){
        $title = '业绩记录';
        return $title;
    }

    public function get_data()
    {
        $model = new EmployeeReceipt();
        $result = $model->where('amount','neq',0)->where('sid','gt',0)->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {

            $list[$k]['bid']       = get_branch_name($v['bid']);
            $list[$k]['eid']       = get_employee_name($v['eid']);
            $list[$k]['sale_role_did'] = get_did_value($v['sale_role_did']);
            $list[$k]['orb_id'] = get_order_receipt_bill_orb_no($v['orb_id']);
            $list[$k]['oid'] = get_order_order_no($v['oid']);
            $list[$k]['sid']       = get_student_name($v['sid']);
            $list[$k]['consume_type'] = $this->convert_consume_type($v['consume_type']);
        }
        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }

    /**
     * @param $type
     * @return mixed
     */
    protected function convert_consume_type($type){
        $map = [
            '-',
            '新报',
            '续报',
            '扩科'
        ];

        return $map[$type];
    }
}