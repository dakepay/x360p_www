<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\Classes as ClassModel;


class Classes extends Export
{
    protected $res_name = 'class';

    protected $columns = [
        ['field'=>'class_name','title'=>'班级名称','width'=>20],
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'sj_id','title'=>'科目','width'=>20],
        ['field'=>'teach_eid','title'=>'老师','width'=>20],
        ['field'=>'cr_id','title'=>'教室','width'=>20],
        ['field'=>'status','title'=>'状态','width'=>20],
        ['field'=>'plan_student_nums','title'=>'预招人数','width'=>20],
        // ['field'=>'class_no','title'=>'班级编号','width'=>20],
        ['field'=>'lid','title'=>'所属课程','width'=>20],
        ['field'=>'nums_rate','title'=>'满班率','width'=>20],
        ['field'=>'start_lesson_time','title'=>'开课日期','width'=>20],
    ];



    protected function get_title(){
        $title = '班级列表';
        return $title;
    }

    protected function convert_status($value)
    {
        $map = [0=>'招生中', 1=>'已开课', 2=>'已结课'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    public function get_data()
    {
        $model = new ClassModel();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['bid']       = get_branch_name($v['bid']);
            $list[$k]['sj_id']     = get_subject_name($v['sj_id']);
            $list[$k]['teach_eid'] = get_teacher_name($v['teach_eid']);
            $list[$k]['cr_id']     = get_class_room($v['cr_id']);
            $list[$k]['lid']       = get_lesson_name($v['lid']);
            $list[$k]['status']    = $this->convert_status($v['status']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }





}