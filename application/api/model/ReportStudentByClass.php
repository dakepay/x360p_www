<?php


namespace app\api\model;

use think\Log;

class ReportStudentByClass extends Base
{

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

    protected function get_current_lid($cid){
        $class_info = get_class_info($cid);
        return $class_info['lid'];
    }

    protected function makeReportOfDay($int_day,$cid,$og_id = 0)
    {
        $date_info     = getdate(strtotime($int_day));
        $cinfo = get_class_info($cid);
        $data          = [];
        $data['og_id'] = $og_id;
        $data['bid']   = $cinfo['bid'];
        $data['cid'] = $cid;
        $data['lid'] = $this->get_current_lid($cid);
        $data['year']  = $date_info['year'];
        $data['month'] = $date_info['mon'];
        $data['week']  = $date_info['wday'];
        $data['day']   = $date_info['yday'];
        $data['int_day'] = $int_day;

        $base_w['int_day'] = ['=', $int_day];
        $temp = $this->getTimeCondition($int_day);
        $base_w['create_time'] = ['between', $temp];
        $base_w['bid'] = $cinfo['bid'];
        $base_w['cid'] = $cid;

        $fields = $this->getTableFields();

        foreach ($fields as $field) {
            $field_method = 'calc_' . $field;
            if (method_exists($this, $field_method)) {
                $data[$field] = $this->$field_method($base_w);
            }
        }

        $model = new ReportStudentByClass;

        $w_ex['og_id']     = $og_id;
        $w_ex['bid']       = $cinfo['bid'];
        $w_ex['cid'] = $cid;
        $w_ex['int_day']   = $int_day;

        $exist_data = $model->where($w_ex)->find();

        if($exist_data){
            $w_ex = [];
            $w_ex['id'] = $exist_data['id'];
            $model->save($data,$w_ex);  
        }else{
            $ret = $model->isUpdate(false)->save($data);
        }
       
        return $data;
    }

    public static function buildReport($input)
    {
        // print_r($input);exit;
        $og_id = gvar('og_id');
        $int_start = date('Ymd',strtotime($input['start_date']));
        $int_end = date('Ymd',strtotime($input['end_date']));
        $cids = $input['cid'];
        $model = new self();

        try{
            for(; $int_start <= $int_end;){

                foreach ($cids as $cid) {
                   $model->makeReportOfDay($int_start,$cid,$og_id);
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

    protected function calc_in_student_num($w)
    {
        unset($w['int_day']);
        $w['status'] = 1;
        return ClassStudent::where($w)->count();
    }


    protected function calc_out_student_num($w)
    {
        unset($w['int_day']);
        $w['status'] = 2;
        return ClassStudent::where($w)->count();
    }
    


}