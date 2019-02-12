<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\Student;


class Students extends Export
{
    protected $res_name = 'student';

    protected $diy_field_name = 'option_fields';
    protected $option_fields = [];
    protected $enable_option_fields = null;
    protected $option_fields_cfg_name = 'student_option_fields';

    protected $columns = [
        ['field'=>'student_name','title'=>'学生姓名','width'=>15],
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'sex','title'=>'性别','width'=>10],
        ['field'=>'birth_time','title'=>'出生日期','width'=>20],
        ['field'=>'status','title'=>'状态','width'=>10],
        ['field'=>'student_lesson_remain_hours','title'=>'剩余课时','width'=>10],
        ['field'=>'student_lesson_hours','title'=>'总课时','width'=>10],
        ['field'=>'money','title'=>'钱包余额','width'=>20],
        ['field'=>'sno','title'=>'学号','width'=>20],
        ['field'=>'card_no','title'=>'卡号','width'=>20],
        ['field'=>'nick_name','title'=>'昵称/英文名','width'=>15],
        ['field'=>'first_tel','title'=>'手机号','width'=>20],
        ['field'=>'vip_level','title'=>'会员等级','width'=>15],
        ['field'=>'service_level','title'=>'服务星级','width'=>10],
        ['field'=>'first_family_rel','title'=>'关系','width'=>20],
        ['field'=>'first_family_name','title'=>'姓名','width'=>20],

        ['field'=>'second_tel','title'=>'第二电话','width'=>20],
        ['field'=>'second_family_rel','title'=>'关系','width'=>20],
        ['field'=>'second_family_name','title'=>'姓名','width'=>20],
        ['field'=>'school_id','title'=>'公立学校','width'=>20],
        ['field'=>'school_grade','title'=>'年级','width'=>10],
        ['field'=>'school_class','title'=>'班级','width'=>20],
        ['field'=>'eid','title'=>'学管师','width'=>20],
        ['field'=>'referer_sid','title'=>'介绍人','width'=>20],
        ['field'=>'in_time','title'=>'报名时间','width'=>20],
        ['field'=>'mc_id','title'=>'市场渠道','width'=>20],
        ['field'=>'from_did','title'=>'报名时间','width'=>20],
        ['field'=>'lids','title'=>'课程','width'=>40],
    ];

    protected function get_title(){
        $title = '学员信息表';
        return $title;
    }

    protected function convert_status($value)
    {
        $map = [1=>'正常',20=>'停课',30=>'休学',90=>'退学',100=>'封存'];
        if(key_exists($value,$map)){
            return $map[$value];
        }
        return '';
    }

    protected function convert_lids($sid)
    {
        $w['sid'] = $sid;
        $w['lesson_status'] = ['lt',2];
        $mStudentLesson = new \app\api\model\StudentLesson();
        $lids = $mStudentLesson->where($w)->column('lid');
        $lids = array_unique($lids);
        $lesson_names = [];
        foreach ($lids as $lid) {
            $lesson_names[] = get_lesson_name($lid);
        }
        return implode(',',$lesson_names);
    }

    protected function convert_vip_level($vip_level)
    {
        if($vip_level == -1){
            return '非会员';
        }
        $config = user_config('params.member.level');
        // print_r($config);exit;
        return $config[$vip_level]['name']; 
    }

    public function get_data()
    {
        $model = new Student();
        $data = $model->getSearchResult($this->params,$this->pagenation);


        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['bid']       = get_branch_name($v['bid']);
            $data['list'][$k]['school_id']  = get_school_name($v['school_id']);
            $data['list'][$k]['school_grade'] = get_grade_name($v['school_grade']);
            $data['list'][$k]['sex'] = get_sex($v['sex']);
            $data['list'][$k]['first_family_rel'] = get_family_rel($v['first_family_rel']);
            $data['list'][$k]['second_family_rel'] = get_family_rel($v['second_family_rel']);
            $data['list'][$k]['status'] = $this->convert_status($v['status']);
            $data['list'][$k]['eid'] = get_teacher_name($v['eid']);
            $data['list'][$k]['lids'] = $this->convert_lids($v['sid']);
            $data['list'][$k]['vip_level'] = $this->convert_vip_level($v['vip_level']);
            $data['list'][$k]['referer_sid'] = get_student_name($v['referer_sid']);
            $data['list'][$k]['mc_id'] = get_mc_name($v['mc_id']);
            $data['list'][$k]['from_did'] = get_did_value($v['from_did']);
        }


        if($this->pagenation){
            return $data;
        }
        if (!empty($data['list'])) {
            return collection($data['list'])->toArray();
        }
        return [];

    }
}