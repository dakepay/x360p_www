<?php
namespace app\api\export;

use app\common\Export;
use app\api\model\ClassLog;

class ClassLogs extends Export
{
	protected $columns = [
        ['field'=>'create_uid','title'=>'操作人','width'=>20],
        ['field'=>'bid','title'=>'操作校区','width'=>20],
        ['field'=>'cid','title'=>'操作班级','width'=>20],
        ['field'=>'event_type','title'=>'操作类型','width'=>20],
        ['field'=>'desc','title'=>'操作内容','width'=>40],
        ['field'=>'create_time','title'=>'操作时间','width'=>20],
        ['field'=>'remark','title'=>'备注','width'=>40],
	];

	protected function get_title()
	{
		$input = $this->params;
		$branch = get_branch_name($input['bid']);
		$title = $branch.'班级操作日志';
		return $title;
	}

	protected function convert_event_type($event_type)
    {
        $map = [1=>'创建班级',2=>'编辑班级',3=>'删除班级',4=>'学员加入班级',5=>'学员退出班级',6=>'班级结课',7=>'班级排课',8=>'删除班级排课',9=>'班级升班',10=>'服务记录',11=>'服务安排',12=>'班级考勤'];
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
        if(!empty($content)){
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

	protected function get_data()
	{
		$input = $this->params;
        $pagenation = $this->pagenation;
    	$model = new ClassLog;
    	$ret = $model->getSearchResult($input,$pagenation);
    	foreach ($ret['list'] as &$item) {
    		$item['cid'] = get_class_name($item['cid']);
    		$item['bid'] = get_branch_name($item['bid']);
            $item['create_uid'] = get_employee_name($item['create_uid']);
            $item['event_type'] = $this->convert_event_type($item['event_type']);
            $item['remark'] = $this->get_remark($item['content']);
    	}
        if($pagenation){
            return $ret;
        }
    	if(!empty($ret['list'])){
    		return collection($ret['list'])->toArray();
    	}
    	return [];

	}



}