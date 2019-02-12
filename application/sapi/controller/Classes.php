<?php
/**
 * Author: luo
 * Time: 2017-12-22 10:10
**/
namespace app\sapi\controller;

use app\sapi\model\ClassStudent;
use think\Request;
use app\sapi\model\Classes as ClassesModel;

class Classes extends Base
{
    /**
     * @desc  我的班级
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();

        $m_cs = new ClassStudent();
        $ret = $m_cs->where('sid', $sid)->getSearchResult($input);
        $list = [];
        $m_class = new \app\sapi\model\Classes();
        foreach($ret['list'] as $row) {
            $class = $m_class->withCount('student_artwork')
                ->with(['lesson','classroom', 'teacher', 'assistant'])->where('cid', $row['cid'])->find();
            if(empty($class)) continue;
            $class = $class->toArray();
            array_push($list, $class);
        }
        $ret['list'] = $list;

        return $this->sendSuccess($ret);
    }

    /**
     * 获取活动班级排课计划
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function activity_schedule(Request $request)
    {
        $input = $request->param();
        if(isset($input['class_name'])){
            $w_class['class_name'] = ['like','%'.$input['class_name'].'%'];
        }
        $w_class['class_type'] = ClassesModel::CLASS_TYPE_ACTIVITY;

        $model = new ClassesModel;

        $ret = $model->where($w_class)->with('schedule')->getSearchResult($input);

        return $this->sendSuccess($ret);
        
    }



    public function tmp_class(Request $request)
    {
        $input = $request->get();

        $m_class = new \app\sapi\model\Classes();

        if(isset($input['sa_id'])){
            $sa_info = get_sa_info($input['sa_id']);
            $class_config = user_config('params.class');
            $filter_class_rule = $class_config['book_filter_rule'];
            if(!$filter_class_rule){
                $filter_class_rule = 1;
            }
            if($filter_class_rule == 1){    //按年级
                $grade = get_student_absence_grade($input['sa_id']);
                $input['grade'] = $grade;
            }elseif($filter_class_rule == 2){   //按课程
                $lid = get_student_absence_lid($input['sa_id']);
                $input['lid'] = $lid;
            }elseif($filter_class_rule == 3){
                $input['sj_id'] = $sa_info['sj_id'];
            }

            unset($input['sa_id']);
        }
        $ret = $m_class->getSearchResult($input);
        
        return $this->sendSuccess($ret);
    }

    /*班级 -- 详情*/
    public function get_detail(Request $request, $id = 0)
    {
        $cid = $request->param('id');
        $data = ClassesModel::getClassDetail($cid);
        return $this->sendSuccess($data);
    }


}