<?php


namespace app\api\model;

use think\Log;

class ReportStudentBySchool extends Base
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

    protected function makeReportOfDay($int_day,$school_id,$og_id = 0)
    {
        $date_info     = getdate(strtotime($int_day));
        $psinfo = get_public_school_info($school_id);
        $data          = [];
        $data['og_id'] = $og_id;
        $data['bid']   = $psinfo['bid'];
        $data['school_id'] = $school_id;
        $data['year']  = $date_info['year'];
        $data['month'] = $date_info['mon'];
        $data['week']  = $date_info['wday'];
        $data['day']   = $date_info['yday'];
        $data['int_day'] = $int_day;

        print_r($data);exit;

        $base_w['int_day'] = ['=', $int_day];
        $temp = $this->getTimeCondition($int_day);
        $base_w['create_time'] = ['between', $temp];
        $base_w['bid'] = $psinfo['bid'];
        $base_w['school_id'] = $school_id;

        $fields = $this->getTableFields();

        foreach ($fields as $field) {
            $field_method = 'calc_' . $field;
            if (method_exists($this, $field_method)) {
                $data[$field] = $this->$field_method($base_w);
            }
        }

        $model = new ReportStudentBySchool;

        $w_ex['og_id']     = $og_id;
        $w_ex['bid']       = $psinfo['bid'];
        $w_ex['school_id'] = $school_id;
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
        $school_ids = $input['school_id'];
        $model = new self();

        try{
            for(; $int_start <= $int_end;){

                foreach ($school_ids as $school_id) {
                   $model->makeReportOfDay($int_start,$school_id,$og_id);
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


    protected function calc_student_num($w)
    {
        unset($w['int_day']);
        return Student::where($w)->count();
    }


}