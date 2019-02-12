<?php

namespace app\api\model;

use app\common\Report;

/**
 * 加盟商运营表
 * Class ReportBusinessSummary
 * @package app\api\model
 */
class ReportOrgSummary extends Report
{
    protected $report_name = '加盟商总表';
    protected $report_table_name = 'report_org_summary';
    protected $extra_export_fields = [
        'org_name'      =>  ['title'=>'加盟商名称'],
        'join_int_day'  =>  ['title'=>'加盟日期'],
        'open_int_day'  =>  ['title'=>'开业日期']
    ];
    protected $report_fields = [
        'employee_nums' => ['title' => '员工数量', 'type' => Report::FTYPE_INT],
        'student_nums' => ['title' => '学员总数', 'type' => Report::FTYPE_INT],
        'lesson_hour_total' => ['title' => '剩余课时', 'type' => Report::FTYPE_DECIMAL132],
        'money_total' => ['title' => '剩余金额', 'type' => Report::FTYPE_INT],
        'channel_nums' => ['title' => '渠道数量', 'type' => Report::FTYPE_INT],
        'market_clue_nums' => ['title' => '市场名单数', 'type' => Report::FTYPE_INT],
        'followup_nums' => ['title' => '有效沟通数', 'type' => Report::FTYPE_INT],
        'promise_visit_nums' => ['title' => '诺到人数', 'type' => Report::FTYPE_INT],
        'trial_visit_nums' => ['title' => '试听人数', 'type' => Report::FTYPE_INT],
        'oi_student_type1_nums' => ['title' => '新签学员', 'type' => Report::FTYPE_INT],
        'oi_student_type2_nums' => ['title' => '续报学员', 'type' => Report::FTYPE_INT],
        'oi_student_type3_nums' => ['title' => '扩科学员', 'type' => Report::FTYPE_INT],
        'recommend_student_nums' => ['title' => '转介绍学员', 'type' => Report::FTYPE_INT],
        'consume_lesson_hours' => ['title' => '消耗课时数', 'type' => Report::FTYPE_DECIMAL132],
        'consume_lesson_amount' => ['title' => '课耗金额', 'type' => Report::FTYPE_DECIMAL156],
        'income_total_amount' => ['title' => '收费总金额', 'type' => Report::FTYPE_DECIMAL156]
    ];

    /**
     * 创建表格
     * @throws \Exception
     */
    protected function create_table()
    {
        if (empty($this->report_table_name)) {
            exception('report_table_name empty!');
        }
        $table_pre = config('database.prefix');
        $field_sql = $this->build_create_table_field_sql();
        $sql = <<<EOF
CREATE TABLE `{$table_pre}{$this->report_table_name}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `start_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始日期',
  `end_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束日期',
  {$field_sql}
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='{$this->report_name}';
EOF;
        db()->execute($sql);
    }

    /**
     * 获取查询报表
     * @param $input
     * @param $pagenation
     */
    public function getDaySectionReport($input, $pagenation = false)
    {
        if (!isset($input['start_date'])) {
            $ds = current_week_ds();
            $input['start_date'] = $ds[0];
            $input['end_date'] = $ds[1];
        }
        $w['start_int_day'] = format_int_day($input['start_date']);
        $w['end_int_day'] = format_int_day($input['end_date']);

        $day_diff = int_day_diff($w['start_int_day'], $w['end_int_day']);

        if ($day_diff > 31) {
            return $this->user_error('查询时间间隔不可超过1个月');
        }


        if(isset($input['eid'])){
          $input['charge_eid'] = $input['eid'];
          unset($input['eid']);
        }

        $refresh = isset($input['refresh']) ? boolval($input['refresh']) : false;

        $m_org = new Org();

        $ret = $m_org->getSearchResult($input, $pagenation);


        $build_og_ids = [];
        $query_og_ids = [];
        $result_og_ids = [];
        foreach ($ret['list'] as $org) {
            $query_og_ids[] = $org['og_id'];

        }
        $w['og_id'] = ['in', $query_og_ids];
        $db = db($this->report_table_name);
        $result = $db->where($w)->select();

        if (!$result || $refresh) {
            $result = [];
            $build_og_ids = $query_og_ids;
        } else {
            if (count($result) < count($query_og_ids)) {
                $result_og_ids = array_column($result, 'og_id');
                $build_og_ids = array_values(array_diff($query_og_ids,$result_og_ids));

            }
        }

        if (!empty($build_og_ids)) {
            foreach ($build_og_ids as $og_id) {
                $result[] = $this->buildDaySectionReport($input['start_date'], $input['end_date'], $og_id);
            }
        }

        if(!empty($result)){
            foreach($result as $k=>$r){
                $og_info = get_org_info($r['og_id']);
                $result[$k]['org_name']     = $og_info['org_name'];
                $result[$k]['join_int_day'] = isset($og_info['join_int_day'])?$og_info['join_int_day']:0;
                $result[$k]['open_int_day'] = isset($og_info['open_int_day'])?$og_info['open_int_day']:0;
            }
        }

        $ret['list'] = $result;
        $ret['params'] = $input;

        return $ret;

    }

    /**
     * 构建区间报表
     * @param $start_date
     * @param $end_date
     * @param $bid
     */
    public function buildDaySectionReport($start_date, $end_date, $og_id)
    {
        $start_ts = strtotime($start_date . ' 00:01');
        $end_ts = strtotime($end_date . ' 23:59');

        $start_int_day = format_int_day($start_date);
        $end_int_day = format_int_day($end_date);

        $params['between_ts'] = [$start_ts, $end_ts];
        $params['between_int_day'] = [$start_int_day, $end_int_day];
        $params['og_id'] = $og_id;

        $this->init_bid_row_field($params);
        $this->build_day_section_report_before($params);
        $this->build_day_section_report_center($params);
        $this->build_day_section_report_after($params);

        return $this->save_day_section_report($params);


    }

    /**
     * 初始化一行校区统计数据
     * @param $params
     */
    protected function init_bid_row_field(&$params)
    {
        $this->bid_row_field_value = [];
        $this->bid_row_field_value['og_id'] = $params['og_id'];
        $this->bid_row_field_value['start_int_day'] = $params['between_int_day'][0];
        $this->bid_row_field_value['end_int_day'] = $params['between_int_day'][1];
        return $this;
    }

    /**
     * 生成报表前段
     * @param $params
     */
    protected function build_day_section_report_before(&$params)
    {
        $this->count_order($params);
        $this->count_consume($params);
    }

    /**
     * 获得员工总数量
     * @param $params
     */
    public function get_employee_nums_value($params)
    {
        $w_e['og_id'] = $params['og_id'];
        $w_e['is_on_job'] = 1;
        $count = model('employee')->skipOgId(true)->where($w_e)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得学员总数
     * @param $params
     * @return int|string
     */
    public function get_student_nums_value($params)
    {
        $w_s['og_id'] = $params['og_id'];
        $w_s['status'] = ['LT', 90];
        $count = model('student')->skipOgId(true)->where($w_s)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得总剩余课时
     * @param $params
     * @return float|int
     */
    public function get_lesson_hour_total_value($params)
    {
        $w_sl['og_id'] = $params['og_id'];
        $w_sl['lesson_status'] = ['LT', 2];
        $hours = model('student_lesson')->skipOgId(true)->where($w_sl)->sum('remain_lesson_hours');
        return $hours;
    }

    /**
     * 获得总剩余金额
     * @param $params
     * @return float|int
     */
    public function get_money_total_value($params)
    {
        $w_s['status'] = ['LT', 90];
        $w_s['og_id'] = $params['og_id'];
        $total = model('student')->skipOgId(true)->where($w_s)->sum('money');
        return $total;
    }

    /**
     * 获得渠道总数
     * @param $params
     * @return int|string
     */
    public function get_channel_nums_value($params)
    {
        $w_mc['og_id'] = $params['og_id'];
        $count = model('market_channel')->skipOgId(true)->where($w_mc)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得市场名单数量
     * @param $params
     * @return int|string
     */
    public function get_market_clue_nums_value($params)
    {
        $w_mc['og_id'] = $params['og_id'];
        $w_mc['create_time'] = ['between', $params['between_ts']];
        $count = model('market_clue')->skipOgId(true)->where($w_mc)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得有效沟通数数量
     * @param $params
     */
    public function get_followup_nums_value($params)
    {
        $w_cf['og_id'] = $params['og_id'];
        $w_cf['is_connect'] = 1;
        $w_cf['create_time'] = ['between', $params['between_ts']];
        $count = model('customer_follow_up')->skipOgId(true)->where($w_cf)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得诺到人数
     * @param $params
     */
    public function get_promise_visit_nums_value($params)
    {
        $w_cf['og_id'] = $params['og_id'];
        $w_cf['is_promise'] = 1;
        $w_cf['create_time'] = ['between', $params['between_ts']];
        $count = model('customer_follow_up')->skipOgId(true)->where($w_cf)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 获得试听次数
     * @param $params
     * @return int|string
     */
    public function get_trial_visit_nums_value($params)
    {
        $w_tla['og_id'] = $params['og_id'];
        $w_tla['is_attendance'] = 1;
        $w_tla['attendance_status'] = 1;
        $w_tla['int_day'] = ['between', $params['between_int_day']];

        $count = model('trial_listen_arrange')->skipOgId(true)->where($w_tla)->count();
        return $count;
    }

    /**
     * @param $params
     */
    public function get_recommend_student_nums_value($params)
    {
        $w_cu['og_id'] = $params['og_id'];
        $w_cu['create_time'] = ['between', $params['between_ts']];
        $w_cu['referer_sid'] = ['GT', 0];
        $w_cu['sid'] = ['GT', 0];
        $count = model('customer')->skipOgId(true)->where($w_cu)->count();
        return $count;
    }

    /**
     * 获得总收入
     * @param $params
     * @return float|int
     */
    public function get_income_total_amount_value($params)
    {
        $w_tly['og_id'] = $params['og_id'];
        $w_tly['int_day'] = ['between', $params['between_ts']];
        $w_tly['type'] = 1;
        $w_tly['cate'] = 1;
        $total = model('tally')->skipOgId(true)->where($w_tly)->sum('amount');
        return $total;
    }

    protected function count_order($params)
    {
        $w_o['og_id'] = $params['og_id'];
        $w_o['paid_time'] = ['between', $params['between_ts']];


        $nums = 0;
        $student_nums = 0;
        $lesson_hours = 0;
        $present_hours = 0;
        $amount = 0;
        $payment_amount = 0.00;
        $unpaid_amount = 0.00;
        $deduct_amount = 0.00;
        $discount_amount = 0.00;

        $ct1_nums = 0;
        $ct2_nums = 0;
        $ct3_nums = 0;
        $ct1_amount = 0.00;
        $ct2_amount = 0.00;
        $ct3_amount = 0.00;


        $sid_order = [];
        foreach (get_all_rows('order', $w_o) as $o) {
            if ($o['money_pay_amount'] > 0) {
                $nums++;
            }
            $sid = $o['sid'];
            if (!isset($sid_order[$sid])) {
                $student_nums++;
            }
            $sid_order[$sid] = 1;
            $w_oi = [];
            $w_oi['oid'] = $o['oid'];
            $oi_list = get_table_list('order_item', $w_oi);
            foreach ($oi_list as $oi) {
                if ($oi['gtype'] == 0) {
                    $lesson_hours += $oi['origin_lesson_hours'];
                    $present_hours += $oi['present_lesson_hours'];

                    if ($oi['consume_type'] == 1) {
                        $ct1_nums++;
                        $ct1_amount += $oi['subtotal'];
                    } elseif ($oi['consume_type'] == 2) {
                        $ct2_nums++;
                        $ct2_amount += $oi['subtotal'];
                    } elseif ($oi['consume_type'] == 3) {
                        $ct3_nums++;
                        $ct3_amount += $oi['subtotal'];
                    }
                }
                $deduct_amount += $oi['reduced_amount'];
                $discount_amount += $oi['discount_amount'];
            }
            $amount += $o['money_pay_amount'];
            $payment_amount += $o['paid_amount'];
            $unpaid_amount += $o['unpaid_amount'];
        }

        $this->bid_row_field_value['oi_student_type1_nums'] = $ct1_nums;
        $this->bid_row_field_value['oi_student_type2_nums'] = $ct2_nums;
        $this->bid_row_field_value['oi_student_type3_nums'] = $ct3_nums;

    }

    /**
     * 课消统计
     * @param $params
     */
    protected function count_consume($params)
    {

        $w['og_id'] = $params['og_id'];
        $w['int_day'] = ['between', $params['between_int_day']];

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

        foreach (get_all_rows('student_lesson_hour', $w) as $slh) {
            $nums++;
            $hours += $slh['lesson_hours'];
            $amount += $slh['lesson_amount'];
            if ($slh['consume_type'] == 0) {
                $type0_amount += $slh['lesson_amount'];

            } elseif ($slh['consume_type'] == 1) {
                $type1_amount += $slh['lesson_amount'];
            } elseif ($slh['consume_type'] == 3) {
                $type3_amount += $slh['lesson_amount'];
            }
            if ($slh['lesson_type'] == 0 && $slh['cid'] > 0) {
                $class_consume_nums++;
                $class_consume_hours += $slh['lesson_hours'];
                $class_consume_amount += $slh['lesson_amount'];
            } elseif ($slh['lesson_type'] == 1) {
                $onebyone_consume_nums++;
                $onebyone_consume_hours += $slh['lesson_hours'];
                $onebyone_consume_amount += $slh['lesson_amount'];
            } elseif ($slh['lesson_type'] == 2) {
                $onebytwo_consume_nums++;
                $onebytwo_consume_hours += $slh['lesson_hours'];
                $onebytwo_consume_amount += $slh['lesson_amount'];
            }
        }


        $this->bid_row_field_value['consume_lesson_hours'] = $hours;
        $this->bid_row_field_value['consume_lesson_amount'] = $amount;
    }

    /**
     * @param $params
     * @return array|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function save_day_section_report(&$params){
        if(!$this->save_to_table){
            return array_merge($this->bid_row_field_value,['id'=>0]);
        }

        $model  = new static();
        $w_ex['og_id'] = $params['og_id'];
        $w_ex['start_int_day']  = $params['between_int_day'][0];
        $w_ex['end_int_day']    = $params['between_int_day'][1];
        $ex_model = $model->where($w_ex)->find();
        if($ex_model){
            foreach($this->report_fields as $field=>$r){
                if(isset($this->bid_row_field_value[$field])){
                    $ex_model[$field] = $this->bid_row_field_value[$field];
                }
            }
            $result = $ex_model->save();
            $result = $ex_model->toArray();
        }else{
            $result = $model->save($this->bid_row_field_value);
            if(!$result){
                return [];
            }
            $result = $model->toArray();
        }
        return $result;
    }


}