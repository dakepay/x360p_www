<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 17:38
 */
namespace app\api\import;

use app\common\Import;
use app\api\model\Student;
use app\api\model\Customer;
use app\api\model\MarketClue;
use app\api\model\StudentLog;
use app\api\model\PublicSchool;

class Students extends Import
{
    protected $res = 'student';
    protected $start_row_index = 3;
    protected $pagesize = 20;
    protected $diy_field_name = 'option_fields';
    protected $option_fields = [];
    protected $enable_option_fields = null;
    protected $option_fields_cfg_name = 'student_option_fields';

    protected $fields = [
        ['field'=>'student_name','name'=>'学生姓名','must'=>true],
        ['field'=>'first_tel','name'=>'首选手机号','must'=>true],
        ['field'=>'sex','name'=>'性别'],
        ['field'=>'nick_name','昵称'],
        ['field'=>'birth_time','name'=>'出生日期'],
        ['field'=>'first_family_name','name'=>'第一联系人姓名'],
        ['field'=>'first_family_rel','name'=>'第一联系人关系'],
        ['field'=>'second_family_name','name'=>'第二联系人姓名'],
        ['field'=>'second_tel','name'=>'第二联系人电话'],
        ['field'=>'school_class','name'=>'班级'],
        ['field'=>'school_id','name'=>'学校名称'],
        ['field'=>'sno','name'=>'学号'],
        ['field'=>'card_no','name'=>'考勤卡号']
        // ['field'=>'family_address','name','家庭地址'],
    ];

    protected function get_fields()
    {
        return $this->fields;
    }

    protected function convert_sex($value)
    {
        $map = [1 => '男', 2 => '女'];

        if($key = array_search($value, $map)) return $key;
        return 0;
    }

    protected function convert_birth_time($value)
    {
        return dage_to_date($value);
    }

    protected function convert_first_family_rel($value)
    {
        return get_family_rel_id($value);
    }

    protected function convert_school_id($value)
    {
        return PublicSchool::findOrCreate($value);
    }


    protected function import_row(&$row,$row_no){
        $fields = $this->get_fields();
        $add = [];
        $regular_fields_count = count($this->fields);
        if(count($row) < $regular_fields_count){
            $this->import_log[] = '导入的模板不正确，请下载系统最新模板导入！';
            return 2;
        }

        foreach($fields as $index => $f){
            $field = $f['field'];
            $cell = $row[$index];
            if(is_object($cell)){
                $value = $cell->getPlainText();
            }else{
                $value = $cell;
            }

            $func = 'convert_'.$field;

            if(empty($value)){
                if(isset($f['must']) && $f['must'] === true){
                    $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']没有填写!';
                    return 2;
                }
            }else{
                $add[$field] = trim($value);

                if($field == 'first_tel'){
                    if(!is_mobile($value)){
                        $this->import_log[] = '第'.$row_no.'行的【'.$f['name'].'】格式不正确';
                        return 2; 
                    }
                    // 检测重复
                    try{
                        $add[$field] = $this->check_first_tel_repeat($value);
                    }catch(\Exception $e){
                        $this->import_log[] = '第'.$row_no.'行的【'.$f['name'].'】'.$e->getMessage();
                        return 1; 
                    }
                }  

                if($field == 'second_tel'){
                    if(!is_mobile($value)){
                        $this->import_log[] = '第'.$row_no.'行的【'.$f['name'].'】格式不正确';
                        return 2; 
                    }
                } 

                if(method_exists($this, $func)){
                    try {
                        $add[$field] = $this->$func($value, $add, $row);
                    } catch (\Exception $e) {
                        $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']' . $e->getMessage();
                        return 2;
                    }
                }
            }
        }

        if(!empty($add['birth_time'])) {
            list($year,$month,$day) = explode('-',$add['birth_time']);

            $add['birth_year'] = intval($year);
            $add['birth_month'] = intval($month);
            $add['birth_day']  = intval($day);

            if($month == 0){
                $month = date('n',time());
            }
            if($day == 0){
                $day = date('j',time());
            }
            $add['birth_time'] = mktime(0,0,0,$month,$day,$year);
        }


        if($this->diy_field_name != '' && $this->is_enable_diy_fields()){
            $diy_field_value = $this->getDiyFieldValue($row);
            if(!empty($diy_field_value)){
                $add[$this->diy_field_name] = $diy_field_value;
            }

        }


        return $this->add_data($add,$row_no);
    }

    // 检测第一联系人电话是否重复
    protected function check_first_tel_repeat($value)
    {
        $mMarketClue = new MarketClue;
        $market = $mMarketClue->where('tel',$value)->find();
        if(!empty($market)) exception($value.'已存在市场名单中，属于重复名单');

        $mCustomer = new Customer;
        $customer = $mCustomer->where('first_tel',$value)->find();
        if(!empty($customer))  exception($value.'已存在客户名单中，属于重复名单');
 
        return $value;
    }



    /**
     * 添加数据到数据库
     * @param [type] $data   [description]
     * @param [type] $row_no [description]
     * @return  0 成功
     * @return  2 失败
     * @return  1 重复
     */
    protected function add_data($data,$row_no){
        $w['first_tel'] = $data['first_tel'];
        $w['student_name'] = $data['student_name'];
        $mStudent = new Student();
        $ex_student = $mStudent->where($w)->find();
        if($ex_student){
            $ex_msg = [];
            $update_student = [];
            if(!empty($data['sno']) && $data['sno'] != $ex_student['sno']){
                $update_student['sno'] = $data['sno'];
                $ex_msg[] = sprintf('学号更新:%s=>%s',$ex_student['sno'],$data['sno']);
            }
            if(!empty($data['card_no']) && $data['card_no'] != $ex_student['card_no']){
                $update_student['card_no'] = $data['card_no'];
                $ex_msg[] = sprintf('考勤卡号更新:%s=>$s',$ex_student['card_no'],$data['card_no']);
            }

            if(!empty($ex_msg)){
                $ex_msg = ',更新数据:'.implode(',',$ex_msg);
            }else{
                $ex_msg = '';
            }

            if(!empty($update_student)){
                $w_student_update['sid'] = $ex_student['sid'];
                $result = $mStudent->save($update_student,$w_student_update);
                if(false === $result){
                    $this->import_log[] = '第'.$row_no.'行的学员更新数据出错!';
                }
            }
            $this->import_log[] = '第' . $row_no . '行的学员重复'.$ex_msg;
            return 1;
        }else{
            $result = $mStudent->createOneStudent($data);
            //$result = $m_student->data([])->allowField(true)->isUpdate(false)->save($data);

            if (!$result) {
                $this->import_log[] = '第' . $row_no . '行的学员数据写入数据库失败:' . $mStudent->getError();
                return 2;
            }else{
                // 添加一条学员导入日志
                $sid = $result;
                StudentLog::addStudentImportLog($sid);
            }
        }

        return 0;
    }
}
