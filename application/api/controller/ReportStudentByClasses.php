<?php

namespace app\api\controller;

use app\api\model\Branch;
use app\api\model\ClassAttendance;
use app\api\model\Customer;
use app\api\model\Classes;
use app\api\model\OrderReceiptBill;
use app\api\model\OrderRefund;
use app\api\model\ReportStudentByClass;
use app\api\model\ClassStudent;
use app\api\model\StudentAttendance;
use app\api\model\StudentLessonHour;
use app\api\model\TrialListenArrange as TrialModel;
use think\Request;

class ReportStudentByclasses extends Base
{
    protected function get_current_bid($cid){
        $class_info = get_class_info($cid);
        if(!$class_info){
            return '-';
        }
        return $class_info['bid'];
    }


    public function get_list(Request $request)
    {  
        $model = new ReportStudentByClass;
        $input = $request->get();

        // print_r($input['page']);exit;


        // 日期不得为空
        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
        ];
        $ret = $this->validate($input, $rule);
        if ($ret === false) {
            return $this->sendError(400, $ret);
        }
        // 日期不得为空
        // 查询条件
        $w = [];
        if(!empty($input['start_date'])){
            $w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
        }
        // 查询条件
        // 查询字段
        $fields = ReportStudentByClass::getSumFields();
        array_unshift($fields,'cid');
        
        // 查数据
        $x_bids = request()->header('x-bid');

        $w_x['bid'] = ['in',$x_bids];
        $w_x['create_time'] = ['lt',strtotime($input['start_date'])];
        $w_x['status'] = ['in',['0','1']];
        $w_cids = Classes::where($w_x)->column('cid');
        $w['cid'] = ['in',$w_cids];

        $data = $model->where($w)->group('cid')->field($fields)->order('cid asc')->getSearchResult($input);

        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['bid'] = $this->get_current_bid($v['cid']);
              
            $w_i['status'] = 1;
            $w_i['cid'] = $v['cid'];
            $w_i['in_time'] = ['lt',strtotime($input['start_date'])];
            $initial_student_num = ClassStudent::where($w_i)->count();
            $data['list'][$k]['initial_student_num'] = $initial_student_num + $v['sum_out_student_num'];
            $data['list'][$k]['final_student_num'] = $initial_student_num + $v['sum_out_student_num'] + $v['sum_in_student_num'];

            $cinfo = get_class_info($v['cid']);
            $data['list'][$k]['class_type'] = $cinfo['class_type'];
            $data['list'][$k]['class_name'] = $cinfo['class_name'];
        }
 
        return $this->sendSuccess($data);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
        ];
        $rs = $this->validate($input, $rule);

        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }

        // $b_w['create_time'] = ['between',[strtotime($input['start_date']),strtotime($input['end_date'])]];
        $b_w['create_time'] = ['lt',strtotime($input['start_date'])];
        $b_w['status'] = ['in',['0','1']];
        $b_w['og_id'] = gvar('og_id');
        $bids = $request->header('x-bid');
        $bids = explode(',',$bids);
        $b_w['bid'] = ['in',$bids];
        // $bids = ClassStudent::where($b_w)->column('bid');
        // $input['bid'] = array_unique($bids);
        $cids = Classes::where($b_w)->column('cid');

        $input['cid'] = array_unique($cids);
        $rs = ReportStudentByClass::buildReport($input);
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }
        return $this->sendSuccess();
    }

    

}