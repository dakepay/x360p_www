<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\CustomerFollowUp;
use app\api\model\Customer;

class CustomerFollowUps extends Export
{
    protected $columns = [
        ['field'=>'name','title'=>'姓名','width'=>20],
        ['field'=>'tel','title'=>'手机号码','width'=>20],
        ['field'=>'status','title'=>'客户状态','width'=>20],
        ['field'=>'content','title'=>'跟进内容','width'=>20],
        ['field'=>'create_time','title'=>'跟进日期','width'=>20],
        ['field'=>'promise','title'=>'承若到访','width'=>20],
        ['field'=>'visit','title'=>'实际到访','width'=>20],
        ['field'=>'next_follow_time','title'=>'下次跟进','width'=>20],
        ['field'=>'intention_level','title'=>'意向级别','width'=>20],
    ];

    protected function get_title(){
        $title = '跟进记录';
        return $title;
    }

    protected function convert_promise($is_promise,$promise_int_day){
        if($is_promise==0){
            return '未诺到';
        }else if($is_promise==1){
            return int_day_to_date_str($promise_int_day);
        }
    }

    protected function convert_visit($is_visit,$visit_int_day){
        if($is_visit==0){
            return '未到访';
        }else if($is_visit==1){
            return int_day_to_date_str($visit_int_day);
        }
    }


    public function get_data()
    {
        $model = new CustomerFollowUp();
        $input = $this->params;

        if(!isset($input['is_system'])){
            $input['is_system'] = 0;
        }
        if(isset($input['name'])) {
            $name = $input['name'];
            unset($input['name']);
            $cu_ids = (new Customer())->where('name', 'like', "%$name%")->column('cu_id');
            if(!empty($cu_ids)) $model->where('cu_id', 'in', $cu_ids);
        }
        $result = $model->order('cfu_id', 'desc')->getSearchResult($input,[],false);

        $list = $result['list'];

        foreach ($list as $k => $v) {
            $cinfo = get_customer_info($v['cu_id']);
            $list[$k]['name'] = $cinfo['name'];
            $list[$k]['tel'] = $cinfo['first_tel'];
            $list[$k]['status'] = get_did_value($v['customer_status_did']);

            $list[$k]['promise'] = $this->convert_promise($v['is_promise'],$v['promise_int_day']);
            $list[$k]['visit'] = $this->convert_visit($v['is_visit'],$v['visit_int_day']);

        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];
    }
}