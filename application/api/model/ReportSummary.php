<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/23
 * Time: 17:33
 */

namespace app\api\model;

use think\Log;

class ReportSummary extends Base
{
    public $append = ['current_lesson_hour_remain', 'current_money_remain', 'current_arrearage_total'];

    public static function getSumFields()
    {
        $class = new \ReflectionClass(new self());
        $methods = $class->getMethods();
        $fields = [];
        foreach ($methods as $method) {
            if (strpos($method->name, 'calc_') !== false) {
                $fields[] = substr($method->name, 5);
            }
        }
        foreach ($fields as $key => $field) {
            $fields["sum({$field})"] = "sum_{$field}";
            unset($fields[$key]);
        }
        return $fields;
    }

    protected function makeReportOfDay($int_day, $bid,$og_id = 0)
    {
        $date_info     = getdate(strtotime($int_day));
        $data          = [];
        $data['og_id'] = $og_id;
        $data['bid']   = $bid;
        $data['year']  = $date_info['year'];
        $data['month'] = $date_info['mon'];
        $data['week']  = $date_info['wday'];
        $data['day']   = $date_info['yday'];
        $data['int_day'] = $int_day;

        $base_w['int_day'] = ['=', $int_day];
        $temp = $this->getTimeCondition($int_day);
        $base_w['create_time'] = ['between', $temp];
        $base_w['bid'] = $bid;

        $fields = $this->getTableFields();
        foreach ($fields as $field) {
            $field_method = 'calc_' . $field;
            if (method_exists($this, $field_method)) {
                $data[$field] = $this->$field_method($base_w);
            }
        }

        $w_ex['og_id']   = $og_id;
        $w_ex['bid']     = $bid;
        $w_ex['int_day'] = $int_day;

        $ex_data = $this->where($w_ex)->find();

        $m = new ReportSummary;
        if($ex_data){
            $w_ex = [];
            $w_ex['id'] = $ex_data['id'];
            $m->save($data,$w_ex);  
        }else{
            $m->isUpdate(false)->save($data);
        }
       
        return $data;
    }

    public static function buildReport($input)
    {
        $og_id = gvar('og_id');
        $int_start = date('Ymd', strtotime($input['start_date']));
        $int_end   = date('Ymd', strtotime($input['end_date']));

        if (!empty($input['bid']) && is_numeric($input['bid'])) {
            $bids = [$input['bid']];
        } else {
            $bids = Branch::where('og_id', $og_id)->column('bid');
        }
        $model  = new self();
        
        try{
            for (; $int_start <= $int_end;) {
                foreach ($bids as $bid) {
                    $model->makeReportOfDay($int_start, $bid,$og_id);
                }
                $int_start = date("Ymd", strtotime("+1 day", strtotime($int_start)));
            }
        }catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return true;
    }

    public function getRealTimeReport($input)
    {

    }

    public static function getTimeCondition($int_start_day, $int_end_day = null)
    {
        $condition = [];
        $condition[] = strtotime($int_start_day);
        if (empty($int_end_day)) {
            $condition[] = strtotime("+1 day",strtotime($int_start_day)) - 1;
        } else {
            $temp = strtotime($int_end_day);
            $condition[] = mktime(23,59,59, date('m', $temp), date('d', $temp), date('Y', $temp));
        }
        return $condition;
    }

    /*新意向客户*/
    protected function calc_customer_num($w)
    {
        unset($w['int_day']);
        return Customer::where($w)->count();
    }

    /*订单数量*/
    protected function calc_order_num($w)
    {
        unset($w['int_day']);
        return Order::where($w)->count();
    }

    /*消耗课时数*/
    protected function calc_lesson_hour_consume($w)
    {
        unset($w['create_time']);
        return StudentLessonHour::where($w)->sum('lesson_hours');
    }

    /*剩余课时数bug*/
    public function getCurrentLessonHourRemainAttr($value)
    {
        $bid = $this->getData('bid');
        return StudentLesson::where('bid', $bid)->where(['lesson_status'=>['LT',2]])->sum('remain_lesson_hours');
    }

    /*消耗课时金额*/
    protected function calc_money_consume($w)
    {
        unset($w['create_time']);
        return StudentLessonHour::where($w)->sum('lesson_amount');
    }

    /*todo:待优化，缓存加缓存字段*/
    /**
     * [getCurrentMoneyRemainAttr description]
     * @return [type] [description]
     */
    public function getCurrentMoneyRemainAttr($value)
    {

        $bid = $this->getData('bid');
        $w['bid'] = $bid;
        $w['lesson_status'] = ['LT',2];
        $m_student_lesson = new StudentLesson();
        $sl_list = $m_student_lesson->where($w)->select();
        $total_money_remain = 0;
        if($sl_list){
            foreach($sl_list as $sl){
                $total_money_remain += $sl->getRemainLessonAmount();
            }
        }
        return format_currency($total_money_remain);
    }

    public function getSumIncomeTotalAttr($value){
        return format_currency($value);
    }

    public function getSumRefundTotalAttr($value){
        return format_currency($value);
    }

    public function getSumOutlayTotalAttr($value){
        return format_currency($value);
    }



    /*教师课酬课时数*/
    protected function calc_lesson_hour_reward($w)
    {
        unset($w['create_time']);
        return EmployeeLessonHour::where($w)->sum('total_lesson_hours');
    }

    /*教师课酬金额*/
    protected function calc_money_reward($w)
    {
        unset($w['create_time']);
        return EmployeeLessonHour::where($w)->sum('total_lesson_amount');
    }

    /*收款合计*/
    protected function calc_income_total($w)
    {
        unset($w['int_day']);
        $w['type'] = Tally::TALLY_TYPE_INCOME;
        //todo
        return Tally::where($w)->sum('amount');
    }

    /*欠款合计*/
    protected function getCurrentArrearageTotalAttr()
    {
        $bid = $this->getData('bid');
        return Order::where('bid', $bid)->sum('unpaid_amount');
    }

    /*退款合计*/
    protected function calc_refund_total($w)
    {
        unset($w['int_day']);
        return OrderRefund::where($w)->sum('refund_amount');
    }

    /*支出合计*/
    protected function calc_outlay_total($w)
    {
        unset($w['int_day']);
        $w['type'] = Tally::TALLY_TYPE_PAYOUT;
        //todo
        return Tally::where($w)->sum('amount');
    }
}