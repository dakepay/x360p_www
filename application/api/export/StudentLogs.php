<?php
namespace app\api\export;

use app\common\Export;
use app\api\model\StudentLog;

class StudentLogs extends Export
{
	protected $columns = [
        ['field'=>'create_uid','title'=>'操作人','width'=>20],
        ['field'=>'bid','title'=>'操作校区','width'=>20],
        ['field'=>'sid','title'=>'被操作学员','width'=>20],
        ['field'=>'op_type','title'=>'操作类型','width'=>20],
        ['field'=>'desc','title'=>'操作内容','width'=>40],
        ['field'=>'create_time','title'=>'操作时间','width'=>20],
        ['field'=>'status','title'=>'操作状态','width'=>15],
        ['field'=>'remark','title'=>'备注','width'=>30],
	];

	protected function get_title()
	{
		$input = $this->params;
		$branch = get_branch_name($input['bid']);
		$title = $branch.'学员操作日志';
		return $title;
	}

	protected function convert_op_type($op_type)
    {
        $map = [1=>'回流为客户',2=>'转校区',10=>'结课',20=>'停课',21=>'复课',30=>'休学',31=>'复学',40=>'请假',41=>'撤销请假',50=>'转班',51=>'编辑',52=>'缴费',53=>'退费',54=>'结转',55=>'转让课时',56=>'服务记录',57=>'服务安排',58=>'转让金额',59=>'分配班主任',60=>'编辑头像',90=>'退学',91=>'入学'];
        if(key_exists($op_type,$map)){
                return $map[$op_type];
        }
        return '-';
    }

    protected function convert_field($field)
    {
        $map = ['student_name'=>'姓名','birth_time'=>'出生日期','card_no'=>'卡号','first_tel'=>'首选手机号','first_family_rel'=>'关系','first_family_name'=>'亲属姓名','nick_name'=>'昵称','sex'=>'性别','sno'=>'学号','second_tel'=>'第二电话','second_family_rel'=>'第二关系','second_family_name'=>'第二亲属姓名','school_id'=>'公立学校','school_grade'=>'学校年级','school_class'=>'班级','referer_sid'=>'介绍人','mc_id'=>'市场渠道','from_did'=>'招生来源','vip_level'=>'会员等级','service_level'=>'服务星级'];
        if(key_exists($field,$map)){
            return $map[$field];
        }
        return $field;
    }


    protected function get_remark($extra_param)
    {
        $time_fields = ['birth_time','last_attendance_time'];
        $student_fields = ['referer_sid'];
        $rel_fields = ['first_family_rel','second_family_rel'];
        $sex_fields = ['sex'];
        $grade_fields = ['school_grade'];
        if(!empty($extra_param)){
            $remark = [];
            foreach ($extra_param as $item) {
                $field = $this->convert_field($item['field']);
                $old_value = $item['old_value'];
                $new_value = $item['new_value'];
                if(in_array($item['field'],$time_fields)){
                    $old_value = date('Y-m-d',$old_value);
                    $new_value = date('Y-m-d',$new_value);
                }
                if(in_array($item['field'],$student_fields)){
                    $old_value = get_student_name($old_value);
                    $new_value = get_student_name($new_value);
                }
                if(in_array($item['field'],$rel_fields)){
                    $old_value = get_family_rel($old_value);
                    $new_value = get_family_rel($new_value);
                }
                if(in_array($item['field'],$sex_fields)){
                    $old_value = get_sex($old_value);
                    $new_value = get_sex($new_value);
                }
                if(in_array($item['field'],$grade_fields)){
                    $old_value = get_grade_title($old_value);
                    $new_value = get_grade_title($new_value);
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
		$model = new StudentLog;
        $ret = $model->getSearchResult($input,$pagenation);

        foreach ($ret['list'] as &$item) {
        	$item['sid'] = get_student_name($item['sid']);
        	$item['bid'] = get_branch_name($item['bid']);
        	$item['status'] = '成功';
            $item['create_uid'] = get_employee_name($item['create_uid']);
            $item['op_type'] = $this->convert_op_type($item['op_type']);
            $item['remark'] = $this->get_remark($item['extra_param']);
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