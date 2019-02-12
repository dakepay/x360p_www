<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/23
 * Time: 17:14
 */

namespace app\api\controller;

use app\api\model\Customer;
use app\api\model\CustomerStatusConversion;
use app\api\model\MarketChannel;
use app\api\model\MarketClue;
use app\api\model\CustomerFollowUp;
use app\api\model\Student;
use app\api\model\OrderItem;
use app\api\model\TrialListenArrange;
use think\Request;

class ReportCustomers extends Base
{
    /**
     * from_did:招生来源(招生来源字典ID),intention_level:意向程度1-5,customer_status_did:跟进状态(跟进状态字典ID),referer_sid:介绍人,学员ID
     * 客户分析报表&销售漏斗
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        if (!empty($input['group'])) {
            $group = explode(',', $input['group']);
        } else {
            $group = [];
        }
        $w = [];
        if (!empty($input['start_date'])) {
            $w['create_time'] = ['between', [str_to_time($input['start_date']), str_to_time($input['end_date'], true)]];
        }
        $model = new Customer();
        $field = $group;
        $field['count(cu_id)'] = 'subtotal';
        $field['sum(is_reg)']  = 'transfer_nums';
        $with = [];
        if (in_array('referer_sid', $group)) {
            $w['referer_sid'] = ['>', 0];
            $with[] = 'referer_student';
        }
        if (!empty($input['order_field'])) {
            $model->order($input['order_field'], $input['order_sort']);
        } else {
            $input['order_field'] = 'total_student_attendance';
            $input['order_sort']  = 'desc';
        }
        $data = $model->where($w)->field($field)->group(join(',', $group))->with($with)->getSearchResult($input,[]);
        return $this->sendSuccess($data);
    }

    /*转化率分析*/
    public function conversion_rate(Request $request)
    {
        $input = $request->only(['bid', 'follow_eid', 'old_value', 'new_value', 'date_period', 'order_field', 'order_sort']);
        $w = [];
        $prefix = 't.';/*联表查询的表别名*/
        foreach ($input as $key => $value) {
            if (in_array($key, ['follow_eid', 'old_value', 'new_value'])) {
                $w[$prefix . $key] = $value;
            } else {
                if ($key == 'date_period') {
                    $date_period = explode(',', $value);
                    $date_period = array_map(function($item) {
                        return strtotime($item);
                    }, $date_period);
                    $w[$prefix . 'create_time'] = ['between', $date_period];
                }
            }
        }
        $og_id = gvar('og_id');
        $w[$prefix . 'og_id'] = $og_id;
        if (isset($input['bid'])) {
            $bids = $input['bid'];
        } else {
            $bids = request()->header('x-bid');
        }
        if ($bids) {
            $w[$prefix . 'bid'] = ['in', explode(',', $bids)];
        }
        $group = ['old_value'];
        $group = array_map(function($item) use ($prefix) {
            return $prefix . $item;
        }, $group);
        $field = $group;
        //todo 不准确
        $field['count(distinct t.cu_id)'] = 'count_follow_up'; /*已跟进人数*/
        $field['count(case when t.old_value > t.new_value then 1 else 0 end)'] = 'count_advance'; /*进阶人数*/
        $field['count(case when t.old_value > t.new_value then 1 else 0 end)/count(distinct t.cu_id)'] = 'advance_rate'; /*进阶率*/
        $field['count(distinct c.sid)'] = 'count_transfer';/*转化人数*/
        $field['count(distinct c.sid)/count(distinct t.cu_id)'] = 'conversion_rate';/*转化率*/
        $model = new CustomerStatusConversion();
        $model->alias('t')
            ->where($w)
            ->field($field)
            ->join('__CUSTOMER__ c', 't.cu_id=c.cu_id')
            ->group(join(',', $group));
        if (!empty($input['order_field'])) {
            $model->order($input['order_field'], $input['order_sort']);
        } else {
            $input['order_field'] = 'conversion_rate';
            $input['order_sort']  = 'desc';
        }
        $data['list'] = $model->select();
        return $this->sendSuccess($data);
    }


    /**
     * 移动端 招生统计表 新
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function admissions(Request $request)
    {
        $input = $request->param();

        $bids = isset($input['bids']) ? explode(',',$input['bids']) : [];

        if(!isset($input['start_date'])){
            $input['start_date'] = '1970-01-02';
            $input['end_date']   = '9999-12-31';
        }

        $start_int_day = format_int_day($input['start_date']);
        $end_int_day   = format_int_day($input['end_date']);
        $start_ts      = strtotime($input['start_date'].' 00:00:00');
        $end_ts        = strtotime($input['end_date'].' 23:59:59');

        $params['bid'] = $bids;
        $params['between_int_day'] = [$start_int_day,$end_int_day];
        $params['between_ts'] = [$start_ts,$end_ts];

        $market_data = $this->get_market_data_value($params);
        $customer_data = $this->get_customer_data_value($params);
        $follow_data = $this->get_follow_data_value($params);
        $student_data = $this->get_student_data_value($params);
        $trial_data = $this->get_trial_data_value($params);

        $data = array(
            'market_data'   => $market_data,
            'customer_data' => $customer_data,
            'follow_data'   => $follow_data,
            'student_data'  => $student_data,
            'trial_data'    => $trial_data
        );
        
        return $this->sendSuccess($data);
    }

    // 市场
    protected function get_market_data_value($params)
    {
        $data = array(
            'market_channel_nums'    => 0,
            'market_clue_nums'       => 0,
            'new_market_clue_nums'   => 0,
            'valid_market_clue_nums' => 0
        );

        $mMarketChannel = new MarketChannel;
        $data['market_channel_nums'] = $mMarketChannel->where(['bid'=>['in',$params['bid']]])->count();

        $mMarketClue = new MarketClue;
        $data['market_clue_nums'] = $mMarketClue->where(['bid'=>['in',$params['bid']]])->count();
        $data['new_market_clue_nums'] = $mMarketClue->where(['bid'=>['in',$params['bid']],'get_time'=>['between',$params['between_ts']]])->count();
        $data['valid_market_clue_nums'] = $mMarketClue->where(['bid'=>['in',$params['bid']],'is_valid'=>1])->count();

        return $data;
    }

    // 客户
    protected function get_customer_data_value($params)
    {
        $data = array(
            'customer_nums'           => 0,
            'new_customer_nums'       => 0,
            'intention_customer_nums' => 0
        );

        $mCustomer = new Customer;

        $data['customer_nums'] = $mCustomer->where(['bid'=>['in',$params['bid']]])->count();
        $data['new_customer_nums'] = $mCustomer->where(['bid'=>['in',$params['bid']],'get_time'=>['between',$params['between_ts']]])->count();
        $data['intention_customer_nums'] = $mCustomer->where(['bid'=>['in',$params['bid']],'intention_level'=>['gt',2]])->count();

        return $data;
    }

    // 跟进
    protected function get_follow_data_value($params)
    {
        $data = array(
            'valid_communicate_nums' => 0,
            'promise_nums'           => 0,
            'visit_nums'             => 0
        );

        $mCustomerFollowUp = new CustomerFollowUp;
        $data['valid_communicate_nums'] = $mCustomerFollowUp->where(['bid'=>['in',$params['bid']],'is_connect'=>1,'create_time'=>['between',$params['between_ts']],'is_system'=>0])->count();
        $data['promise_nums'] = $mCustomerFollowUp->where(['bid'=>['in',$params['bid']],'is_promise'=>1,'create_time'=>['between',$params['between_ts']],'is_system'=>0])->count();
        $data['visit_nums'] = $mCustomerFollowUp->where(['bid'=>['in',$params['bid']],'is_visit'=>1,'create_time'=>['between',$params['between_ts']],'is_system'=>0])->count();

        return $data;
    }

    // 学员
    protected function get_student_data_value($params)
    {
        $data = array(
            'student_nums'         => 0,
            'new_student_nums'      => 0,
            'sign_student_nums'     => 0,
            'new_sign_student_nums' => 0
        );

        $mStudent = new Student;
        $status = ['1','30'];
        $data['student_nums'] = $mStudent->where(['bid'=>['in',$params['bid']],'status'=>['in',$status]])->count();
        $data['new_student_nums'] = $mStudent->where(['bid'=>['in',$params['bid']],'status'=>['in',$status],'create_time'=>['between',$params['between_ts']]])->count();
        $data['sign_student_nums'] = $mStudent->where(['bid'=>['in',$params['bid']],'status'=>['in',$status],'in_time'=>['gt',0]])->count();
        $data['new_sign_student_nums'] = $mStudent->where(['bid'=>['in',$params['bid']],'status'=>['in',$status],'in_time'=>['between',$params['between_ts']]])->count();

        return $data;
    }

    // 试听
    protected function get_trial_data_value($params)
    {
        $data = array(
            'trial_nums'      =>  0,
            'trial_times'     => 0,
            'trial_sign_nums' => 0
        );

        $mTrialListenArrange = new TrialListenArrange;
        
        $data['trial_nums'] = $this->get_trial_nums_value($params);
        $data['trial_sign_nums'] = $this->get_trial_sign_nums_value($params);
        $data['trial_times'] = $mTrialListenArrange->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']]])->count();

        return $data;
    }


    protected function get_trial_sign_nums_value($params)
    {
        $w['bid'] = ['in',$params['bid']];
        // $w['int_day'] = ['between',$params['between_int_day']];
        $w['is_attendance'] = 1;
        $w['attendance_status'] = 1;
        
        $w['cu_id'] = ['gt',0];
        $cu_ids = model('trial_listen_arrange')->where($w)->column('cu_id');
        $customer_sids = model('customer')->where('cu_id','in',$cu_ids)->column('sid');

        $w['sid'] = ['gt',0];
        unset($w['cu_id']);
        $student_sids = model('trial_listen_arrange')->where($w)->column('sid');
        $student_sids = array_unique($student_sids);

        $sids = array_merge($customer_sids,$student_sids);

        $order_items = model('order_item')->alias(['x360p_order_item'=>'oi','x360p_order'=>'o'])->join('x360p_order','oi.oid = o.oid')->where(['oi.gtype'=>OrderItem::GTYPE_LESSON,'oi.sid'=>['in',$sids],'o.paid_time'=>['between',$params['between_ts']],'o.pay_status'=>2])->select();
        $order_items = collection($order_items)->toArray();
        $order_sids = array_column($order_items,'sid');
        $order_sids = array_unique($order_sids);

        return count($order_sids);
    }


    protected function get_trial_nums_value($params)
    {
        $w['bid'] = ['in',$params['bid']];
        $w['is_attendance'] = 1;
        $w['attendance_status'] = 1;

        $cu_ids = $this->m_trial_listen_arrange->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']],'cu_id'=>['gt',0]])->column('cu_id');
        $trial_customer_nums = count(array_unique($cu_ids));

        $sids = $this->m_trial_listen_arrange->where(['bid'=>['in',$params['bid']],'int_day'=>['between',$params['between_int_day']],'sid'=>['gt',0]])->column('sid');
        $trial_student_nums = count(array_unique($sids));

        $trial_nums = $trial_customer_nums + $trial_student_nums;

        return $trial_nums;
    }

}