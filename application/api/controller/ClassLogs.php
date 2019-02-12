<?php

namespace app\api\controller;

use think\Request;
use app\api\model\ClassLog;
use app\api\model\Classes;

class ClassLogs extends Base
{
    protected function convert_event_type($event_type)
    {
        $map = [1=>'创建班级',2=>'编辑班级',3=>'删除班级',4=>'学员加入班级',5=>'学员退出班级',6=>'班级结课',7=>'班级排课',8=>'删除班级排课',9=>'班级升班',10=>'服务记录',11=>'服务安排',12=>'班级考勤',13=>'导入班级',14=>'创建排课计划',15=>'删除排课计划',16=>'修改排课计划'];
        if(key_exists($event_type,$map)){
            return $map[$event_type];
        }
        return '-';
    }

    protected function convert_field($field)
    {
        $map = ['class_name'=>'班级名称','lid'=>'所属课程','sj_id'=>'科目','grade'=>'年级','teach_eid'=>'老师','edu_eid'=>'班主任','second_eid'=>'中方教师','second_eids'=>'中方教师','cr_id'=>'教室','plan_student_nums'=>'预招人数','consume_source_type'=>'课消来源','start_lesson_time'=>'开课日期','year'=>'年份','season'=>'期段','lesson_times'=>'上课次数'];
        if(key_exists($field,$map)){
            return $map[$field];
        }
        return $field;
    }

    protected function convert_source_type($type)
    {
        $map = [1=>'课时',2=>'电子钱包'];
        if(key_exists($type,$map)){
            return $map[$type];
        }
        return '-';
    }

    protected function get_remark($content)
    {
        $keys = ($content  && is_array($content) && isset($content[0])) ? array_keys($content[0]) : [];
        if(!empty($content) && in_array('field',$keys) && in_array('old_value',$keys) && in_array('new_value',$keys)){
            foreach ($content as $item) {
                $field = $this->convert_field($item['field']);
                $old_value = $item['old_value'];
                $new_value = $item['new_value'];
                switch ($item['field']) {
                    case 'lid':
                        $old_value = get_lesson_name($item['old_value']);
                        $new_value = get_lesson_name($item['new_value']);
                        break;
                    case 'sj_id':
                        $old_value = get_subject_name($item['old_value']);
                        $new_value = get_subject_name($item['new_value']);
                        break;
                    case 'grade':
                        $old_value = get_grade_name($item['old_value']);
                        $new_value = get_grade_name($item['new_value']);
                        break;
                    case 'second_eid':
                        $old_value = get_employee_name($item['old_value']);
                        $new_value = get_employee_name($item['new_value']);
                        break;
                    case 'edu_eid':
                        $old_value = get_employee_name($item['old_value']);
                        $new_value = get_employee_name($item['new_value']);
                        break;
                    case 'teach_eid':
                        $old_value = get_employee_name($item['old_value']);
                        $new_value = get_employee_name($item['new_value']);
                        break;
                    case 'cr_id':
                        $old_value = get_class_room($item['old_value']);
                        $new_value = get_class_room($item['new_value']);
                        break;
                    case 'consume_source_type':
                        $old_value = $this->convert_source_type($item['old_value']);
                        $new_value = $this->convert_source_type($item['new_value']);
                        break;
                    case 'second_eids':
                        $old = [];
                        foreach ($item['old_value'] as $val) {
                            $old[] = get_employee_name($val);
                        }
                        $old_value = implode(',',$old);
                        $new = [];
                        foreach ($item['new_value'] as $val) {
                            $new[] = get_employee_name($val);
                        }
                        $new_value = implode(',',$new);
                        break;
                    default:
                        break;
                }

                $remark[] = '【'.$field.'】 编辑之前：'.$old_value.' 编辑之后：'.$new_value;
            }
            return implode(' ',$remark);
        }
        return '-';
    }

    protected function get_class_withtrashed_name($cid)
    {
        $class = Classes::withTrashed()->where('cid',$cid)->find();
        if(empty($class)){
            return '-';
        }
        return $class->class_name;
    }


    public function get_list(Request $request)
    {
    	$input = $request->param();
    	$model = new ClassLog;
    	$ret = $model->getSearchResult($input);
    	foreach ($ret['list'] as &$item) {
    		$item['cid'] = $this->get_class_withtrashed_name($item['cid']);
            $item['create_uid'] = get_user_name($item['create_uid']);
            $item['event_type'] = $this->convert_event_type($item['event_type']);
            $item['remark'] = $this->get_remark($item['content']);
    	}
    	return $this->sendSuccess($ret);
    }
    
}