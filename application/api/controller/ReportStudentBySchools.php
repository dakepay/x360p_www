<?php

namespace app\api\controller;

use app\api\model\Branch;
use app\api\model\ClassAttendance;
use app\api\model\Customer;
use app\api\model\OrderReceiptBill;
use app\api\model\OrderRefund;
use app\api\model\ReportStudentBySchool;
use app\api\model\Student;
use app\api\model\StudentAttendance;
use app\api\model\StudentLessonHour;
use app\api\model\TrialListenArrange as TrialModel;
use think\Request;

class ReportStudentBySchools extends Base
{
    public function get_list(Request $request)
    {  
        $model = new ReportStudentBySchool;
        $input = $request->only(['start_date','end_date']);
        // 日期不得为空
        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
        ];
        $ret = $this->validate($input, $rule);
        if ($ret === false) {
            return $this->sendError(400, $rs);
        }
        // 日期不得为空
        // 查询条件
        $w = [];
        if(!empty($input['start_date'])){
            $w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
        }
        $w['og_id'] = gvar('og_id');
        // 查询条件
        // 查询字段
        $fields = ReportStudentBySchool::getSumFields();
        array_unshift($fields,'school_id');
        
        // 查数据
        $data = $model->where($w)->group('school_id')->field($fields)->order('school_id asc')->getSearchResult();
        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['school_id'] = get_school_name($v['school_id']);
        }
        
        // 创建更新数据
        if(empty($data)){
            $b_w['create_time'] = ['between',[strtotime($input['start_date']),strtotime($input['end_date'])]];
            $bids = Student::where($b_w)->column('bid');
            $input['bid'] = array_unique($bids);
            $school_ids = Student::where($b_w)->column('school_id');
            $input['school_id'] = array_unique($school_ids);
            $ret = ReportStudentBySchool::buildReport($input);
            if($ret === false){
                return $this->sendError(400,$ret);
            }
            $data = $model->where($w)->group('school_id')->field($fields)->order('school_id asc')->getSearchResult();
            foreach ($data['list'] as $k => $v) {
                $data['list'][$k]['school_id'] = get_school_name($v['school_id']);
            }
        }
        // 创建更新数据
  

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

        $b_w['create_time'] = ['between',[strtotime($input['start_date']),strtotime($input['end_date'])]];
        $b_w['og_id'] = gvar('og_id');

        $school_ids = Student::where($b_w)->column('school_id');
        $input['school_id'] = array_unique($school_ids);
        $rs = ReportStudentBySchool::buildReport($input);
        if ($rs !== true) {
            return $this->sendError(400, $rs);
        }
        return $this->sendSuccess();
    }

    

}