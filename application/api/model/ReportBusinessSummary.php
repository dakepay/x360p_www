<?php
namespace app\api\model;

use app\common\Report;

/**
 * 运营汇总表
 * Class ReportBusinessSummary
 * @package app\api\model
 */
class ReportBusinessSummary extends Report
{
    protected $report_name  = '运营总表';
    protected $report_table_name     = 'report_business_summary';
    protected $extra_title = [
        [
            [
                'title' => '市场','start_field'=>'market_channel_nums','end_field'=>'customer_deal_amount'
            ],
            [
                'title' => '销售','start_field'=>'sale_customer_total','end_field'=>'order_discount_amount'
            ],
            [
                'title' => '运营','start_field'=>'student_new','end_field'=>'consume_type3_amount'
            ],
            [
                'title' => '财务','start_field'=>'iae_oi_ct1_amount','end_field'=>'student_money_total'
            ]
        ],
        [
            [
                'title' => '渠道名单','start_field'=>'market_channel_nums','end_field'=>'market_clue_deal_amount'
            ],
            [
                'title' => '家长推荐','start_field'=>'market_clue_recommend_new','end_field'=>'market_clue_recommend_deal_amount'
            ],
            [
                'title' => '客户名单导入','start_field'=>'customer_new','end_field'=>'customer_deal_amount'
            ],
            [
                'title' => '销售总计','start_field'=>'sale_customer_total','end_field'=>'sale_customer_deal_amount'
            ],
            [
                'title' => '订单总计','start_field'=>'order_nums','end_field'=>'order_discount_amount'
            ],
            [
                'title' => '学员数','start_field'=>'student_new','end_field'=>'student_total'
            ],
            [
                'title' => '班级汇总','start_field'=>'class_num','end_field'=>'class_consume_amount'
            ],
            [
                'title' => '1对1汇总','start_field'=>'onebyone_course_arrange_nums','end_field'=>'onebyone_consume_amount'
            ],
            [
                'title' => '1对多汇总','start_field'=>'onebytwo_course_arrange_nums','end_field'=>'onebytwo_consume_amount'
            ],
            [
                'title' => '考勤','start_field'=>'student_attendance_nums','end_field'=>'student_attendance_unarrange_nums'
            ],
            [
                'title' => '课消','start_field'=>'consume_nums','end_field'=>'consume_type3_amount'
            ],
            [
                'title' => '收支','start_field'=>'iae_oi_ct1_amount','end_field'=>'iae_order_transfer_amount'
            ],
            [
                'title' => '预存','start_field'=>'student_money_add','end_field'=>'student_money_total'
            ]
        ]
    ];
    protected $report_fields  = [
        //渠道名单
        'market_channel_nums'                   =>  ['title'=>'来源渠道数','type'=>Report::FTYPE_INT],
        'market_clue_new'                       =>  ['title'=>'新名单数','type'=>Report::FTYPE_INT],
        'market_clue_valid'                     =>  ['title'=>'有效数','type'=>Report::FTYPE_INT],
        'market_clue_trans_customer'            =>  ['title'=>'转化客户数','type'=>Report::FTYPE_INT],
        'market_clue_promise_visit'             =>  ['title'=>'诺到','type'=>Report::FTYPE_INT],
        'market_clue_trial_listen'              =>  ['title'=>'试听','type'=>Report::FTYPE_INT],
        'market_clue_deal_nums'                 =>  ['title'=>'成交数量','type'=>Report::FTYPE_INT],
        'market_clue_deal_amount'               =>  ['title'=>'成交金额','type'=>Report::FTYPE_DECIMAL132],
        //家长推荐
        'market_clue_recommend_new'             =>  ['title'=>'推荐数','type'=>Report::FTYPE_INT],
        'market_clue_recommend_valid'           =>  ['title'=>'有效数','type'=>Report::FTYPE_INT],
        'market_clue_recommend_trans_customer'  =>  ['title'=>'转化客户','type'=>Report::FTYPE_INT],
        'market_clue_recommend_promise_visit'   =>  ['title'=>'诺到','type'=>Report::FTYPE_INT],
        'market_clue_recommend_trial_listen'    =>  ['title'=>'试听','type'=>Report::FTYPE_INT],
        'market_clue_recommend_deal_nums'       =>  ['title'=>'成交数量','type'=>Report::FTYPE_INT],
        'market_clue_recommend_deal_amount'     =>  ['title'=>'成交金额','type'=>Report::FTYPE_DECIMAL132],
        //客户名单录入
        'customer_new'                          =>  ['title'=>'新客户名单数','type'=>Report::FTYPE_INT],
        'customer_valid'                        =>  ['title'=>'有效数','type'=>Report::FTYPE_INT],
        'customer_trans_student'                =>  ['title'=>'转化学员数','type'=>Report::FTYPE_INT],
        'customer_promise_visit'                =>  ['title'=>'诺到数','type'=>Report::FTYPE_INT],
        'customer_trial_listen'                 =>  ['title'=>'试听','type'=>Report::FTYPE_INT],
        'customer_deal_nums'                    =>  ['title'=>'成交数量','type'=>Report::FTYPE_INT],
        'customer_deal_amount'                  =>  ['title'=>'成交金额','type'=>Report::FTYPE_DECIMAL132],
        //销售总计
        'sale_customer_total'                   =>  ['title'=>'客户名单总数','type'=>Report::FTYPE_INT],
        'sale_customer_unassigned'              =>  ['title'=>'未分配数','type'=>Report::FTYPE_INT],
        'sale_customer_new'                     =>  ['title'=>'新增客户数','type'=>Report::FTYPE_INT],
        'sale_customer_valid'                   =>  ['title'=>'有效沟通数','type'=>Report::FTYPE_INT],
        'sale_customer_trans_student'           =>  ['title'=>'转化学员数','type'=>Report::FTYPE_INT],
        'sale_customer_promise_visit'           =>  ['title'=>'诺到数','type'=>Report::FTYPE_INT],
        'sale_customer_trial_listen'            =>  ['title'=>'试听数','type'=>Report::FTYPE_INT],
        'sale_customer_deal_nums'               =>  ['title'=>'成交数','type'=>Report::FTYPE_INT],
        'sale_customer_deal_amount'             =>  ['title'=>'成交金额','type'=>Report::FTYPE_DECIMAL132],
        //订单总计
        'order_nums'              =>  ['title'=>'订单数','type'=>Report::FTYPE_INT],
        'order_student_nums'      =>  ['title'=>'签约人数','type'=>Report::FTYPE_INT],
        'order_lesson_hours'      =>  ['title'=>'签约课时数','type'=>Report::FTYPE_DECIMAL132],
        'order_present_hours'     =>  ['title'=>'赠送课时数','type'=>Report::FTYPE_DECIMAL132],
        'order_amount'            =>  ['title'=>'签约金额','type'=>Report::FTYPE_DECIMAL132],
        'order_payment_amount'    =>  ['title'=>'实收金额','type'=>Report::FTYPE_DECIMAL132],
        'order_unpaid_amount'     =>  ['title'=>'欠款金额','type'=>Report::FTYPE_DECIMAL132],
        'order_deduct_amount'     =>  ['title'=>'直减金额','type'=>Report::FTYPE_DECIMAL132],
        'order_discount_amount'   =>  ['title'=>'折扣金额','type'=>Report::FTYPE_DECIMAL132],
        //学员数
        'student_new'                           =>  ['title'=>'新增学员','type'=>Report::FTYPE_INT],
        'student_normal'                        =>  ['title'=>'在读学员','type'=>Report::FTYPE_INT],
        'student_stop'                          =>  ['title'=>'停课学员','type'=>Report::FTYPE_INT],
        'student_suspend'                       =>  ['title'=>'休学学员','type'=>Report::FTYPE_INT],
        'student_end'                           =>  ['title'=>'结课学员','type'=>Report::FTYPE_INT],
        'student_demo'                          =>  ['title'=>'体验课学员','type'=>Report::FTYPE_INT],
        'student_total'                         =>  ['title'=>'总学员数','type'=>Report::FTYPE_INT],
        //班级报表
        'class_num'                             =>  ['title'=>'班级数','type'=>Report::FTYPE_INT],
        'class_new'                             =>  ['title'=>'新开班数','type'=>Report::FTYPE_INT],
        'class_plan_student_nums'               =>  ['title'=>'计划招人数','type'=>Report::FTYPE_INT],
        'class_real_student_nums'               =>  ['title'=>'实际人数','type'=>Report::FTYPE_INT],
        'class_course_arrange_nums'             =>  ['title'=>'排课次数','type'=>Report::FTYPE_INT],
        'class_course_arrange_hours'            =>  ['title'=>'排课课时数','type'=>Report::FTYPE_DECIMAL132],
        'class_consume_nums'                    =>  ['title'=>'消耗课次','type'=>Report::FTYPE_INT],
        'class_consume_hours'                   =>  ['title'=>'消耗课时','type'=>Report::FTYPE_DECIMAL132],
        'class_consume_amount'                  =>  ['title'=>'消耗金额','type'=>Report::FTYPE_DECIMAL156],
        //1对1
        'onebyone_course_arrange_nums'          =>  ['title'=>'排课次数','type'=>Report::FTYPE_INT],
        'onebyone_course_arrange_hours'         =>  ['title'=>'排课课时','type'=>Report::FTYPE_DECIMAL132],
        'onebyone_consume_nums'                 =>  ['title'=>'消耗课次','type'=>Report::FTYPE_INT],
        'onebyone_consume_hours'                =>  ['title'=>'消耗课时','type'=>Report::FTYPE_DECIMAL132],
        'onebyone_consume_amount'               =>  ['title'=>'消耗金额','type'=>Report::FTYPE_DECIMAL156],
        //1对多
        'onebytwo_course_arrange_nums'          =>  ['title'=>'排课次数','type'=>Report::FTYPE_INT],
        'onebytwo_course_arrange_hours'         =>  ['title'=>'排课课时','type'=>Report::FTYPE_DECIMAL132],
        'onebytwo_consume_nums'                 =>  ['title'=>'消耗课次','type'=>Report::FTYPE_INT],
        'onebytwo_consume_hours'                =>  ['title'=>'消耗课时','type'=>Report::FTYPE_DECIMAL132],
        'onebytwo_consume_amount'              =>  ['title'=>'消耗金额','type'=>Report::FTYPE_DECIMAL156],
        //考勤
        'student_attendance_nums'               =>  ['title'=>'出勤学员数','type'=>Report::FTYPE_INT],
        'student_attendance_need_nums'          =>  ['title'=>'应出勤学员数','type'=>Report::FTYPE_INT],
        'student_attendance_rate'               =>  ['title'=>'学员出勤率','type'=>Report::FTYPE_DECIMAL52],
        'student_attendance_unarrange_nums'     =>  ['title'=>'未排课学员数','type'=>Report::FTYPE_INT],
        //总课消
        'consume_nums'                          =>  ['title'=>'总课消笔数','type'=>Report::FTYPE_INT],
        'consume_hours'                         =>  ['title'=>'总课消课时','type'=>Report::FTYPE_DECIMAL132],
        'consume_amount'                        =>  ['title'=>'总课消金额','type'=>Report::FTYPE_DECIMAL156],
        'consume_type0_amount'                  =>  ['title'=>'正课时课耗','type'=>Report::FTYPE_DECIMAL156],
        'consume_type1_amount'                  =>  ['title'=>'副课时消耗','type'=>Report::FTYPE_DECIMAL156],
        'consume_type3_amount'                  =>  ['title'=>'违约课耗','type'=>Report::FTYPE_DECIMAL156],
        //财务
        'iae_oi_ct1_amount'                     =>  ['title'=>'新签金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_oi_ct2_amount'                     =>  ['title'=>'续报金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_oi_ct3_amount'                     =>  ['title'=>'扩科金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_amount'                      =>  ['title'=>'订单金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_pay_amount'                  =>  ['title'=>'付款金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_unpaid_amount'               =>  ['title'=>'欠款金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_refund_amount'               =>  ['title'=>'退费金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_transfer_amount'             =>  ['title'=>'结转金额','type'=>Report::FTYPE_DECIMAL132],
        //预存
        'student_money_add'                     =>  ['title'=>'预存增加','type'=>Report::FTYPE_DECIMAL132],
        'student_money_reduce'                  =>  ['title'=>'预存消耗','type'=>Report::FTYPE_DECIMAL132],
        'student_money_total'                   =>  ['title'=>'剩余预存','type'=>Report::FTYPE_DECIMAL132]

    ];

    public function getExtraTitle(){
        return $this->extra_title;
    }

    public function getReportFields(){
        return $this->report_fields;
    }


    /**
     * 生成报表前段
     * @param $params
     */
    protected function build_day_section_report_before(&$params){
        $this->count_market_clue($params);
        $this->count_customer($params);
        $this->count_order($params);
        $this->count_class($params);
        $this->count_arrange($params);
        $this->count_attendance($params);
        $this->count_consume($params);
        $this->count_money($params);

    }

    /**
     * 获得渠道总数
     * @param $params
     * @return int|string
     */
    public function get_market_channel_nums_value($params){
        $w_mc['bid']    = $params['bid'];
        $w_mc['og_id']  = $params['og_id'];
        $model = new MarketChannel();
        $total = $model->where($w_mc)->count();
        $this->bid_row_field_value['market_channel_nums'] = $total;
        return $total;
    }

    /**
     * 获得客户总数量
     * @param $params
     * @return int|string
     */
    public function get_sale_customer_total_value($params){
        $w_cu['og_id'] = $params['og_id'];
        $w_cu['bid'] = $params['bid'];
        $model = new Customer();
        $total = $model->where($w_cu)->count();
        $this->bid_row_field_value['sale_customer_total'] = $total;
        return $total;
    }

    /**
     * @param $params
     * @return int|string
     */
    public function get_sale_customer_unassigned_value($params){
        $w_cu['og_id'] = $params['og_id'];
        $w_cu['bid'] = $params['bid'];
        $w_cu['follow_eid'] = 0;
        $model = new Customer();
        $total = $model->where($w_cu)->count();
        $this->bid_row_field_value['sale_customer_unassigned'] = $total;
        return $total;
    }

    /**
     * 统计有效沟通记录数
     * @param $params
     */
    public function get_sale_customer_valid_value($params){
        $w_cfu['og_id'] = $params['og_id'];
        $w_cfu['bid'] = $params['bid'];
        $w_cfu['create_time'] = ['between',$params['between_ts']];
        $w_cfu['is_connect'] = 1;

        $count = model('customer_follow_up')->where($w_cfu)->count('distinct cu_id');
        if(!$count){
            $count = 0;
        }
        return $count;
    }

    /**
     * 获取诺到记录数
     * @param $params
     * @return int|string
     */
    public function get_sale_customer_promise_visit_value($params){
        $w_cfu['og_id'] = $params['og_id'];
        $w_cfu['bid'] = $params['bid'];
        $w_cfu['visit_int_day'] = ['between',$params['between_int_day']];

        $count = model('customer_follow_up')->where($w_cfu)->count('distinct cu_id');
        if(!$count){
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得试听数
     * @param $params
     * @return int|string
     */
    public function get_sale_customer_trial_listen_value($params){
        $w_tla['og_id'] = $params['og_id'];
        $w_tla['bid'] = $params['bid'];
        $w_tla['is_attendance'] =1;
        $w_tla['attendance_status'] = 1;
        $w_tla['int_day'] = ['between',$params['between_int_day']];
        $w_tla['cu_id'] = ['GT',0];
        $count = model('trial_listen_arrange')->where($w_tla)->count('distinct cu_id');
        if(!$count){
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得销售成交数
     * @param $params
     * @return int|string
     */
    public function get_sale_customer_deal_nums_value($params){
        $w_cu['og_id'] = $params['og_id'];
        $w_cu['bid'] = $params['bid'];
        $w_cu['signup_int_day'] = ['between',$params['between_int_day']];

        $count = model('customer')->skipOgId(true)->where($w_cu)->count();
        if(!$count){
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得销售成交金额
     * @param $params
     * @return float|int
     */
    public function get_sale_customer_deal_amount_value($params){
        $w_cu['og_id'] = $params['og_id'];
        $w_cu['bid'] = $params['bid'];
        $w_cu['signup_int_day'] = ['between',$params['between_int_day']];

        //$cu_list = model('customer')->skipOgId(true)->where($w_cu)->select();

        $amount = 0;
        foreach(get_all_rows('customer',$w_cu,100) as $cu){
            if($cu['signup_amount'] == 0){
                $w = [];
                if($cu['sid'] > 0){
                    $w['sid'] = $cu['sid'];
                    $w['order_status'] = ['GT',0];
                    $order_amount = model('order')->where($w)->sum('money_pay_amount');
                    if($order_amount > 0){
                        $update_cu = [];
                        $update_cu['signup_amount'] = $order_amount;
                        $w_cu_update = [];
                        $w_cu_update['cu_id'] = $cu['cu_id'];
                        db('customer')->where($w_cu_update)->update($update_cu);

                    }
                }
            }
            $amount += $cu['signup_amount'];
        }
        return $amount;
    }



    /**
     * 统计市场名单
     * @param $params
     */
    protected function count_market_clue($params){
        $this->get_market_channel_nums_value($params);

        $market_clue_new = 0;
        $market_clue_valid = 0;
        $market_clue_trans_customer = 0;
        $market_clue_promise_visit = 0;
        $market_clue_trial_listen = 0;
        $market_clue_deal_nums = 0;
        $market_clue_deal_amount = 0.00;

        $market_clue_recommend_new = 0;
        $market_clue_recommend_valid = 0;
        $market_clue_recommend_trans_customer = 0;
        $market_clue_recommend_promise_visit = 0;
        $market_clue_recommend_trial_listen = 0;
        $market_clue_recommend_deal_nums = 0;
        $market_clue_recommend_deal_amount = 0.00;

        $w_mcl['bid'] = $params['bid'];
        $w_mcl['get_time'] = ['between',$params['between_ts']];

        foreach(get_all_rows('market_clue',$w_mcl) as $mcl){
            if($mcl['recommend_sid'] > 0 ){
                $market_clue_recommend_new++;
                if($mcl['is_valid'] > 0){
                    $market_clue_recommend_valid++;
                }
                if($mcl['cu_id'] > 0){
                    $market_clue_recommend_trans_customer++;
                }
                if($mcl['is_visit'] > 0){
                    $market_clue_recommend_promise_visit++;
                }
                if(isset($mcl['is_trial']) && $mcl['is_trial'] > 0){
                    $market_clue_recommend_trial_listen++;
                }else{
                    //主动去找
                    if($mcl['cu_id'] > 0){
                        $w_tla['cu_id'] = $mcl['cu_id'];
                        $w_tla['attendance_status'] = 1;

                        $tla = get_tla_info($w_tla);

                        if($tla){
                            $market_clue_recommend_trial_listen++;
                        }

                    }
                }
                if($mcl['is_deal'] > 0){
                    $market_clue_recommend_deal_nums++;
                    if($mcl['deal_amount'] > 0){
                        $market_clue_recommend_deal_amount += $mcl['deal_amount'];
                    }else{

                        $cu_info = get_customer_info($mcl['cu_id']);
                        if($cu_info && $cu_info['signup_amount'] > 0){
                            $market_clue_recommend_deal_amount += $cu_info['signup_amount'];
                        }

                    }
                }

            }else{
                $market_clue_new++;
                if($mcl['is_valid'] > 0){
                    $market_clue_valid++;
                }
                if($mcl['cu_id'] > 0){
                    $market_clue_trans_customer++;
                }
                if($mcl['is_visit'] > 0){
                    $market_clue_promise_visit++;
                }
                if(isset($mcl['is_trial']) && $mcl['is_trial'] > 0){
                    $market_clue_trial_listen++;
                }else{
                    //主动去找
                    if($mcl['cu_id'] > 0){
                        $w_tla['cu_id'] = $mcl['cu_id'];
                        $w_tla['attendance_status'] = 1;

                        $tla = get_tla_info($w_tla);

                        if($tla){
                            $market_clue_trial_listen++;
                        }

                    }
                }
                if($mcl['is_deal'] > 0){
                    $market_clue_deal_nums++;
                    if($mcl['deal_amount'] > 0){
                        $market_clue_recommend_deal_amount += $mcl['deal_amount'];
                    }else{

                        $cu_info = get_customer_info($mcl['cu_id']);
                        if($cu_info && $cu_info['signup_amount'] > 0){
                            $market_clue_recommend_deal_amount += $cu_info['signup_amount'];
                        }

                    }
                }
            }
        }

        $this->bid_row_field_value['market_clue_new'] = $market_clue_new;
        $this->bid_row_field_value['market_clue_valid'] = $market_clue_valid;
        $this->bid_row_field_value['market_clue_trans_customer'] = $market_clue_trans_customer;
        $this->bid_row_field_value['market_clue_promise_visit'] = $market_clue_promise_visit;
        $this->bid_row_field_value['market_clue_trial_listen']  = $market_clue_trial_listen;
        $this->bid_row_field_value['market_clue_deal_nums'] = $market_clue_deal_nums;
        $this->bid_row_field_value['market_clue_deal_amount'] = $market_clue_deal_amount;

        $this->bid_row_field_value['market_clue_recommend_new'] = $market_clue_recommend_new;
        $this->bid_row_field_value['market_clue_recommend_valid'] = $market_clue_recommend_valid;
        $this->bid_row_field_value['market_clue_recommend_trans_customer'] = $market_clue_recommend_trans_customer;
        $this->bid_row_field_value['market_clue_recommend_promise_visit'] = $market_clue_recommend_promise_visit;
        $this->bid_row_field_value['market_clue_recommend_trial_listen'] = $market_clue_recommend_trial_listen;
        $this->bid_row_field_value['market_clue_recommend_deal_nums'] = $market_clue_recommend_deal_nums;
        $this->bid_row_field_value['market_clue_recommend_deal_amount'] = $market_clue_recommend_deal_amount;


    }

    /**
     * 统计客户名单
     * @param $params
     */
    protected function count_customer($params){
        /**
         * 'customer_new'                          =>  ['title'=>'新客户名单数','type'=>Report::FTYPE_INT],
        'customer_valid'                        =>  ['title'=>'有效数','type'=>Report::FTYPE_INT],
        'customer_trans_student'                =>  ['title'=>'转化学员数','type'=>Report::FTYPE_INT],
        'customer_promise_visit'                =>  ['title'=>'诺到数','type'=>Report::FTYPE_INT],
        'customer_trial_listen'                 =>  ['title'=>'试听','type'=>Report::FTYPE_INT],
        'customer_deal_nums'                    =>  ['title'=>'成交数量','type'=>Report::FTYPE_INT],
        'customer_deal_amount'                  =>  ['title'=>'成交金额','type'=>Report::FTYPE_DECIMAL132],
         */

        $new            = 0;
        $valid          = 0;
        $trans_student  = 0;
        $promise_visit  = 0;
        $trial_listen   = 0;
        $deal_nums      = 0;
        $deal_amount    = 0.00;

        $sale_new       = 0;
        $sale_valid     = 0;
        $sale_trans_student  = 0;
        $sale_promise_visit  = 0;
        $sale_trial_listen   = 0;
        $sale_deal_nums      = 0;
        $sale_deal_amount    = 0.00;


        $w_cu['bid'] = $params['bid'];
        $w_cu['get_time'] = ['between',$params['between_ts']];

        foreach(get_all_rows('customer',$w_cu) as $cu){
            $sale_new++;
            $sale_valid++;
            if ($cu['sid'] > 0) {
                $sale_trans_student++;
            }
            if($cu['visit_times'] > 0){
                $sale_promise_visit++;
            }
            if($cu['trial_listen_times'] > 0){
                $sale_trial_listen++;
            }
            if($cu['signup_amount'] > 0){
                $sale_deal_nums++;
                $sale_deal_amount += $cu['signup_amount'];
            }
            if($cu['mcl_id'] == 0) {
                $new++;
                $valid++;
                if ($cu['sid'] > 0) {
                    $trans_student++;
                }
                if ($cu['visit_times'] > 0) {
                    $promise_visit++;
                }

                if ($cu['trial_listen_times'] > 0) {
                    $trial_listen++;
                }
                if ($cu['signup_amount'] > 0) {
                    $deal_nums++;
                    $deal_amount += $cu['signup_amount'];
                }
            }
        }

        $this->bid_row_field_value['sale_customer_new'] = $sale_new;

        $this->bid_row_field_value['sale_customer_trans_student'] = $sale_trans_student;

        $this->bid_row_field_value['customer_new'] = $new;
        $this->bid_row_field_value['customer_valid'] = $valid;
        $this->bid_row_field_value['customer_trans_student'] = $trans_student;
        $this->bid_row_field_value['customer_promise_visit'] = $promise_visit;
        $this->bid_row_field_value['customer_trial_listen']  = $trial_listen;
        $this->bid_row_field_value['customer_deal_nums'] = $deal_nums;
        $this->bid_row_field_value['customer_deal_amount']   = $deal_amount;

    }

    protected function count_order($params){
        $w_o['bid']     = $params['bid'];
        $w_o['og_id']   = $params['og_id'];
        $w_o['paid_time']   = ['between',$params['between_ts']];

        /*
        'order_student_nums'      =>  ['title'=>'签约人数','type'=>Report::FTYPE_INT],
        'order_lesson_hours'      =>  ['title'=>'签约金额数','type'=>Report::FTYPE_DECIMAL132],
        'order_present_hours'     =>  ['title'=>'赠送课时数','type'=>Report::FTYPE_DECIMAL132],
        'order_amount'            =>  ['title'=>'签约金额','type'=>Report::FTYPE_DECIMAL132],
        'order_payment_amount'    =>  ['title'=>'实收金额','type'=>Report::FTYPE_DECIMAL132],
        'order_unpaid_amount'     =>  ['title'=>'欠款金额','type'=>Report::FTYPE_DECIMAL132],
        'order_deduct_amount'     =>  ['title'=>'直减金额','type'=>Report::FTYPE_DECIMAL132],

        'iae_oi_ct1_amount'                     =>  ['title'=>'新签金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_oi_ct2_amount'                     =>  ['title'=>'续报金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_oi_ct3_amount'                     =>  ['title'=>'扩科金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_amount'                      =>  ['title'=>'订单金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_pay_amount'                  =>  ['title'=>'付款金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_unpaid_amount'               =>  ['title'=>'欠款金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_refund_amount'               =>  ['title'=>'退费金额','type'=>Report::FTYPE_DECIMAL132],
        'iae_order_transfer_amount'             =>  ['title'=>'结转金额','type'=>Report::FTYPE_DECIMAL132],
        */
        $nums = 0;
        $student_nums = 0;
        $lesson_hours = 0;
        $present_hours = 0;
        $amount = 0;
        $payment_amount = 0.00;
        $unpaid_amount  = 0.00;
        $deduct_amount  = 0.00;
        $discount_amount = 0.00;

        $ct1_amount = 0.00;
        $ct2_amount = 0.00;
        $ct3_amount = 0.00;




        $sid_order = [];
        foreach(get_all_rows('order',$w_o) as $o){
            $w_orb = [];
            $w_orb['oid'] = $o['oid'];
            $orb_list = get_table_list('order_receipt_bill',$w_orb);
            $money_paid_amount = 0;
            foreach($orb_list as $orb){
                $money_paid_amount += $orb['money_paid_amount'];
            }
            if($money_paid_amount > 0){
                $nums++;
            }
            $sid = $o['sid'];
            if($money_paid_amount > 0 && !isset($sid_order[$sid])){
                $student_nums++;
            }
            $sid_order[$sid] = 1;
            $w_oi = [];
            $w_oi['oid'] = $o['oid'];
            $oi_list = get_table_list('order_item',$w_oi);
            foreach($oi_list as $oi){
                if($oi['gtype'] == 0){
                    $lesson_hours += $oi['origin_lesson_hours'];
                    $present_hours += $oi['present_lesson_hours'];

                    if($oi['consume_type'] == 1){
                        $ct1_amount += $oi['subtotal'];
                    }elseif($oi['consume_type'] == 2){
                        $ct2_amount += $oi['subtotal'];
                    }elseif($oi['consume_type'] == 3){
                        $ct3_amount += $oi['subtotal'];
                    }
                }
                $deduct_amount += $oi['reduced_amount'];
                $discount_amount += $oi['discount_amount'];
            }
            $amount += $money_paid_amount;
            $payment_amount += $o['paid_amount'];
            $unpaid_amount  += $o['unpaid_amount'];
        }

        $this->bid_row_field_value['order_nums'] = $nums;
        $this->bid_row_field_value['order_student_nums'] = $student_nums;
        $this->bid_row_field_value['order_lesson_hours'] = $lesson_hours;
        $this->bid_row_field_value['order_present_hours'] = $present_hours;
        $this->bid_row_field_value['order_amount']  = $amount;
        $this->bid_row_field_value['order_payment_amount'] = $payment_amount;
        $this->bid_row_field_value['order_unpaid_amount'] = $unpaid_amount;
        $this->bid_row_field_value['order_deduct_amount'] = $deduct_amount;
        $this->bid_row_field_value['order_discount_amount'] = $discount_amount;
        $this->bid_row_field_value['iae_oi_ct1_amount'] = $ct1_amount;
        $this->bid_row_field_value['iae_oi_ct2_amount'] = $ct2_amount;
        $this->bid_row_field_value['iae_oi_ct3_amount'] = $ct3_amount;
        $this->bid_row_field_value['iae_order_amount'] = $amount;
        $this->bid_row_field_value['iae_order_pay_amount'] = $payment_amount;
        $this->bid_row_field_value['iae_order_unpaid_amount'] =$unpaid_amount;
    }

    public function get_student_new_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['create_time'] = ['between',$params['between_ts']];

        $count = model('student')->where($w)->count();

        $this->bid_row_field_value['student_new'] = $count;
        return $count;
    }

    public function get_student_normal_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['status'] = 1;

        $count = model('student')->where($w)->count();

        $this->bid_row_field_value['student_normal'] = $count;
        return $count;
    }

    public function get_student_suspend_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['status'] = 30;

        $count = model('student')->where($w)->count();

        $this->bid_row_field_value['student_suspend'] = $count;
        return $count;
    }

    public function get_student_stop_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['status'] = 20;

        $count = model('student')->where($w)->count();

        $this->bid_row_field_value['student_stop'] = $count;
        return $count;
    }

    public function get_student_end_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['status'] = 90;

        $count = model('student')->where($w)->count();

        $this->bid_row_field_value['student_end'] = $count;
        return $count;
    }

    public function get_student_demo_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['vip_level'] = 0;
        $w['status'] = 1;

        $count = model('student')->where($w)->count();

        $this->bid_row_field_value['student_demo'] = $count;
        return $count;
    }

    public function get_student_total_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['status'] = ['LT',90];

        $count = model('student')->where($w)->count();

        $this->bid_row_field_value['student_total'] = $count;
        return $count;
    }

    public function get_iae_order_refund_amount_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['refund_int_day'] = ['between',$params['between_int_day']];
        $amount = model('order_refund')->where($w)->sum('refund_amount');
        if(!$amount){
            $amount = 0.00;
        }
        $this->bid_row_field_value['iae_order_refund_amount'] = $amount;
        return $amount;
    }

    public function get_iae_order_transfer_amount_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['create_time'] = ['between',$params['between_ts']];
        $amount = model('order_transfer')->where($w)->sum('transfer_amount');
        if(!$amount){
            $amount = 0.00;
        }
        $this->bid_row_field_value['iae_order_transfer_amount'] = $amount;
        return $amount;
    }

    public function get_class_num_value($params){
        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['status'] = ['LT',2];

        $class_num = model('classes')->where($w)->count();

        $this->bid_row_field_value['class_num'] = $class_num;
        return $class_num;
    }


    protected function count_class($params){
        /*
         *  'class_num'                             =>  ['title'=>'班级数','type'=>Report::FTYPE_INT],
        'class_new'                             =>  ['title'=>'新开班数','type'=>Report::FTYPE_INT],
        'class_plan_student_nums'               =>  ['title'=>'计划招人数','type'=>Report::FTYPE_INT],
        'class_real_student_nums'               =>  ['title'=>'实际人数','type'=>Report::FTYPE_INT],

         */

        $w['bid'] = $params['bid'];
        $w['og_id'] = $params['og_id'];
        $w['status'] = ['LT',2];
        $w['start_lesson_time'] = ['between',$params['between_ts']];

        $class_new = 0;
        $plan_student_nums = 0;
        $real_student_nums = 0;

        foreach(get_all_rows('class',$w) as $cls){
            $class_new++;
            $plan_student_nums += $cls['plan_student_nums'];
            $real_student_nums += $cls['student_nums'];
        }

        $this->bid_row_field_value['class_new'] = $class_new;
        $this->bid_row_field_value['class_plan_student_nums'] = $plan_student_nums;
        $this->bid_row_field_value['class_real_student_nums'] = $real_student_nums;
    }

    /**
     * 获得指定班级学员数
     * @param $cid
     */
    private function get_class_student_nums($ca){
        static $cid_snums = [];
        $cid = $ca['cid'];
        if(isset($cid_snums[$cid])){
            return $cid_snums[$cid];
        }

        $arrange_ts = strtotime($ca['int_day']);
        $nums = 0;
        $where = 'cid = '.$cid.' and in_time < '.$arrange_ts.' and (out_time = 0 or out_time > '.$arrange_ts.')';

        $m_cs = new ClassStudent();
        $need_nums = $m_cs->where($where)->count();

        if(!$need_nums){
            $need_nums = 0;
        }

        $w_cas['ca_id'] = $ca['ca_id'];
        $w_cas['sid']   = ['GT',0];
        $w_cas['is_makeup'] = 0;
        $w_cas['is_trial'] = 0;

        $m_cas = new CourseArrangeStudent();
        $real_nums = $m_cas->where($w_cas)->count();

        if(!$real_nums){
            $real_nums = 0;
        }

        $ret['need'] = $need_nums;
        $ret['real'] = $real_nums;
        $cid_snums[$cid] = $ret;
        return $ret;
    }

    /**
     * 排课统计
     * @param $params
     */
    protected function count_arrange($params){
        $w['bid']       = $params['bid'];
        $w['og_id']     = $params['og_id'];
        $w['int_day']   = ['between',$params['between_int_day']];
        /*
         *
         * 'class_course_arrange_nums'             =>  ['title'=>'排课次数','type'=>Report::FTYPE_INT],
        'class_course_arrange_hours'            =>  ['title'=>'排课课时数','type'=>Report::FTYPE_DECIMAL132],
         */

        $class_need_arrange_nums = 0;
        $class_real_arrange_nums = 0;
        $class_course_arrange_nums = 0;
        $class_course_arrange_hours = 0.00;
        $onebyone_course_arrange_nums = 0;
        $onebyone_course_arrange_hours = 0.00;
        $onebytwo_course_arrange_nums = 0;
        $onebytwo_course_arrange_hours = 0.00;
        $m_ca = new CourseArrange();
        foreach(get_all_rows('course_arrange',$w) as $ca){
            $lesson_hours = $m_ca->getConsumeLessonHour($ca);
            if($ca['lesson_type'] == 0){
                $class_course_arrange_nums++;
                $class_course_arrange_hours += $lesson_hours;
                $arrange_nums = $this->get_class_student_nums($ca);

                $class_need_arrange_nums += $arrange_nums['need'];
                $class_real_arrange_nums += $arrange_nums['real'];

            }elseif($ca['lesson_type'] == 1){
                $onebyone_course_arrange_nums++;
                $onebyone_course_arrange_hours += $lesson_hours;
            }elseif($ca['lesson_type'] == 2){
                $onebytwo_course_arrange_nums++;
                $onebytwo_course_arrange_hours += $lesson_hours;
            }
        }

        $this->bid_row_field_value['class_course_arrange_nums']     = $class_course_arrange_nums;
        $this->bid_row_field_value['class_course_arrange_hours']    = $class_course_arrange_hours;
        $this->bid_row_field_value['onebyone_course_arrange_nums']  = $onebyone_course_arrange_nums;
        $this->bid_row_field_value['onebyone_course_arrange_hours'] = $onebyone_course_arrange_hours;
        $this->bid_row_field_value['onebytwo_course_arrange_nums']  = $onebytwo_course_arrange_nums;
        $this->bid_row_field_value['onebytwo_course_arrange_hours'] = $onebytwo_course_arrange_hours;

        $this->bid_row_field_value['class_need_arrange_nums'] = $class_need_arrange_nums;
        $this->bid_row_field_value['class_real_arrange_nums'] = $class_real_arrange_nums;
    }

    protected function count_attendance($params){
        /*$w['bid']       = $params['bid'];
        $w['og_id']     = $params['og_id'];
        $w['int_day']   = ['between',$params['between_int_day']];*/

        $att_nums = 0;
        $att_need_nums = 0;
        $att_rate = 0;
        $att_unarrange_nums = 0;
        /*foreach(get_all_rows('class_attendance',$w) as $catt){
            $att_nums += $catt['in_nums'];
            $att_need_nums += $catt['need_nums'];
        }*/
        $w['bid'] = $params['bid'];
        $w['int_day'] = ['between',$params['between_int_day']];
        $w['is_in'] = 1;
        $att_nums = model('student_attendance')->where($w)->count();

        unset($w['is_in']);
        $att_need_nums = model('student_attendance')->where($w)->count();

        if($att_need_nums > 0) {
            $att_rate = round((100 * ($att_nums / $att_need_nums)) * 100) / 100;
        }

        $att_unarrange_nums = min_val($this->bid_row_field_value['class_need_arrange_nums'] - $this->bid_row_field_value['class_real_arrange_nums']);

        $this->bid_row_field_value['student_attendance_nums'] = $att_nums;
        $this->bid_row_field_value['student_attendance_need_nums'] = $att_need_nums;
        $this->bid_row_field_value['student_attendance_rate'] = $att_rate;
        $this->bid_row_field_value['student_attendance_unarrange_nums'] = $att_unarrange_nums;
        unset($this->bid_row_field_value['class_need_arrange_nums']);
        unset($this->bid_row_field_value['class_real_arrange_nums']);
    }

    /**
     * 课消统计
     * @param $params
     */
    protected function count_consume($params){
        /**
         * 'consume_nums'                          =>  ['title'=>'总课消笔数','type'=>Report::FTYPE_INT],
        'consume_hours'                         =>  ['title'=>'总课消课时','type'=>Report::FTYPE_DECIMAL132],
        'consume_amount'                        =>  ['title'=>'总课消金额','type'=>Report::FTYPE_DECIMAL156],
        'consume_type0_amount'                  =>  ['title'=>'正课时课耗','type'=>Report::FTYPE_DECIMAL156],
        'consume_type1_amount'                  =>  ['title'=>'副课时消耗','type'=>Report::FTYPE_DECIMAL156],
        'consume_type3_amount'                  =>  ['title'=>'违约课耗','type'=>Report::FTYPE_DECIMAL156],
         */
        $w['bid']       = $params['bid'];
        $w['og_id']     = $params['og_id'];
        $w['int_day']   = ['between',$params['between_int_day']];

        $nums = 0;
        $hours = 0.00;
        $amount = 0.00;
        $type0_amount = 0.00;
        $type1_amount = 0.00;
        $type3_amount = 0.00;

        $class_consume_nums = 0;
        $class_consume_hours = 0.00;
        $class_consume_amount = 0.00;
        $onebyone_consume_nums = 0;
        $onebyone_consume_hours = 0.00;
        $onebyone_consume_amount = 0.00;
        $onebytwo_consume_nums = 0;
        $onebytwo_consume_hours = 0.00;
        $onebytwo_consume_amount = 0.00;

        foreach(get_all_rows('student_lesson_hour',$w) as $slh){
            $nums++;
            $hours += $slh['lesson_hours'];
            $amount += $slh['lesson_amount'];
            if($slh['consume_type'] == 0){
                $type0_amount += $slh['lesson_amount'];

            }elseif($slh['consume_type'] == 1){
                $type1_amount += $slh['lesson_amount'];
            }elseif($slh['consume_type'] == 3){
                $type3_amount += $slh['lesson_amount'];
            }
            if($slh['lesson_type'] == 0 && $slh['cid'] > 0){
                $class_consume_nums ++;
                $class_consume_hours += $slh['lesson_hours'];
                $class_consume_amount += $slh['lesson_amount'];
            }elseif($slh['lesson_type'] == 1){
                $onebyone_consume_nums ++;
                $onebyone_consume_hours += $slh['lesson_hours'];
                $onebyone_consume_amount += $slh['lesson_amount'];
            }elseif($slh['lesson_type'] == 2){
                $onebytwo_consume_nums ++;
                $onebytwo_consume_hours += $slh['lesson_hours'];
                $onebytwo_consume_amount += $slh['lesson_amount'];
            }
        }

        $this->bid_row_field_value['consume_nums'] = $nums;
        $this->bid_row_field_value['consume_hours'] = $hours;
        $this->bid_row_field_value['consume_amount'] = $amount;
        $this->bid_row_field_value['consume_type0_amount'] = $type0_amount;
        $this->bid_row_field_value['consume_type1_amount'] = $type1_amount;
        $this->bid_row_field_value['consume_type3_amount'] = $type3_amount;

        $this->bid_row_field_value['class_consume_nums'] = $class_consume_nums;
        $this->bid_row_field_value['class_consume_hours'] = $class_consume_hours;
        $this->bid_row_field_value['class_consume_amount'] = $class_consume_amount;

        $this->bid_row_field_value['onebyone_consume_nums'] = $onebyone_consume_nums;
        $this->bid_row_field_value['onebyone_consume_hours'] = $onebyone_consume_hours;
        $this->bid_row_field_value['onebyone_consume_amount'] = $onebyone_consume_amount;

        $this->bid_row_field_value['onebytwo_consume_nums'] = $onebytwo_consume_nums;
        $this->bid_row_field_value['onebytwo_consume_hours'] = $onebytwo_consume_hours;
        $this->bid_row_field_value['onebytwo_consume_amount'] = $onebytwo_consume_amount;

    }


    /**
     * 获得剩余学员钱包总余额
     * @param $params
     */
    public function get_student_money_total_value($params){
        $w['bid']       = $params['bid'];
        $w['og_id']     = $params['og_id'];

        $m_student = new Student();

        $amount = $m_student->where($w)->sum('money');

        if(!$amount){
            $amount = 0.00;
        }

        $this->bid_row_field_value['student_money_total'] = $amount;

        return $amount;
    }

    protected function count_money($params){
        /**
         * 'student_money_add'                     =>  ['title'=>'预存增加','type'=>Report::FTYPE_DECIMAL132],
        'student_money_reduce'                  =>  ['title'=>'预存消耗','type'=>Report::FTYPE_DECIMAL132],
        'student_money_total'                   =>  ['title'=>'剩余预存','type'=>Report::FTYPE_DECIMAL132],
         */
        $w['bid']       = $params['bid'];
        $w['og_id']     = $params['og_id'];
        $w['create_time']   = ['between',$params['between_int_day']];

        $money_add = 0.00;
        $money_reduce = 0.00;

        $w['business_type'] = ['IN',['3']];

        $m_smh = new StudentMoneyHistory();

        $money_add = $m_smh->where($w)->sum('amount');

        if(!$money_add){
            $money_add = 0.00;
        }

        $w['business_type'] = ['IN',['4','5','16']];

        $money_reduce = $m_smh->where($w)->sum('amount');
        if(!$money_reduce){
            $money_reduce = 0.00;
        }

        $this->bid_row_field_value['student_money_add']     = $money_add;
        $this->bid_row_field_value['student_money_reduce']  = $money_reduce;

    }

}