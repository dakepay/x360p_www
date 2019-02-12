<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\StudentLesson;


class StudentLessons extends Export
{
    protected $res_name = 'student_lesson';

    protected $columns = [
        ['field'=>'name','title'=>'姓名/校区','width'=>30],
        ['field'=>'course_lesson','title'=>'课程/科目','width'=>30],
        ['field'=>'nums','title'=>'剩余课时/总课时','width'=>20],
        ['field'=>'import_present','title'=>'导入/赠送','width'=>30],
        ['field'=>'transfer_refund','title'=>'结转/退费','width'=>30],
        ['field'=>'expire_time','title'=>'有效期','width'=>20],
    ];

    protected function get_title(){
        $title = '学生课时';
        return $title;
    }

    protected function get_names($sid,$bid){
    	return get_student_name($sid).'('.get_branch_name($bid).')';
    }

    protected function get_course_lesson($lid,$sj_ids){
    	$subjects = m('subject')->where('sj_id','in',$sj_ids)->column('subject_name');
    	$name = implode('、',$subjects);
    	return get_lesson_name($lid).'('.$name.')';
    }

    protected function get_nums($remain_lesson_hours,$lesson_hours){
    	return $remain_lesson_hours.'/'.$lesson_hours;
    }

    protected function get_import_present($import,$present){
    	return $import.'/'.$present;
    }

    protected function get_transfer_refund($transfer,$refund){
    	return $transfer.'/'.$refund;
    }

    protected function get_expire_time($expire_time){
    	if($expire_time==0){
    		return '无限制';
    	}else{
    		return $expire_time;
    	}
    }


    public function get_data()
    {
        $model = new StudentLesson();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
        	$list[$k]['name'] = $this->get_names($v['sid'],$v['bid']);
        	$list[$k]['course_lesson'] = $this->get_course_lesson($v['lid'],$v['sj_ids']);
        	$list[$k]['nums'] = $this->get_nums($v['remain_lesson_hours'],$v['lesson_hours']);
        	$list[$k]['import_present'] = $this->get_import_present($v['import_lesson_hours'],$v['present_lesson_hours']);
        	$list[$k]['transfer_refund'] = $this->get_transfer_refund($v['transfer_lesson_hours'],$v['refund_lesson_hours']);
        	$list[$k]['expire_time'] = $this->get_expire_time($v['expire_time']);

        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}