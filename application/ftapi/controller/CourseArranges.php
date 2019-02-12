<?php
namespace app\ftapi\controller;

use app\ftapi\model\CourseArrange;
use app\ftapi\model\FtReview;
use think\Request;

class CourseArranges extends Base
{
    /**
     * @desc  外教的课程
     * @method GET
     */
    public function get_list(Request $request)
    {
        $eid = global_eid();
        $input = input();

        $employee_info = get_employee_info($eid);
        $w_ca['teach_eid'] = $employee_info['eid'];

        if (!empty($input['int_day'])){
            $w_ca['int_day'] = intval($input['int_day']);
            unset($input['int_day']);
        }
        $m_ca = new CourseArrange();
        $ret = $m_ca->with(['lesson.attachments', 'teacher'])->where($w_ca)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  外教排课日期
     * @method GET
     */
    public function get_course_day(Request $request)
    {
        $input = input();
        $eid = global_eid();
        $employee_info = get_employee_info($eid);

        $w = [
            'teach_eid' => $employee_info['eid'],
        ];

        $m_ca = new CourseArrange();
        $int_day_list = $m_ca->where($w)->field('int_day')->getSearchResult($input,[],false);

        $list = [];
        foreach ($int_day_list['list'] as $k => $v){
            $list[$k] = $v['int_day'];
        }
        $list = array_values(array_unique($list));

        return $this->sendSuccess(['list' => $list]);
    }

    /**
     * 获取详情
     * @param Request $request
     * @param int $id
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_detail(Request $request, $id = 0)
    {
        $ca_id = $id;
        $m_ca = new CourseArrange();
        $get = $request->get();

        $eid = global_eid();
        $employee_info = get_employee_info($eid);
        $w_ca['teach_eid'] = $employee_info['eid'];

        $with = !isset($get['with']) ? [] : (is_string($get['with']) ? explode(',', $get['with']) : $get['with']);

        if(($key = array_search('students', $with)) !== false) {
            $with_students = true;
            unset($with[$key]);
        }

        /** @var CourseArrange $course */
        $course = $m_ca->where($w_ca)->with($with)->find($ca_id);

        //是否返回排课的班级学员信息
        if(!empty($with_students) && !empty($course)) {
            $course['students'] = $course->getAttObjects();
        }

        return $this->sendSuccess($course);
    }

    public function get_list_last_report(Request $request, $id = 0){
        $input = input();
        $ca_id = $input['id'];
        unset($input['id']);
        $m_ca = CourseArrange::get($ca_id);
        if (!$m_ca) return $this->sendError(400,'Course_arrange exists');

        $eid = global_eid();
        $employee_info = get_employee_info($eid);

        $mFt_review = new FtReview();
        $w = [];

        if ($m_ca['lesson_type'] == 0){
            $w = [
                'cid' =>  $m_ca['cid'],
                'eid' => $employee_info['eid'],
                'lesson_type' => $m_ca['lesson_type']
            ];
        }elseif ($m_ca['lesson_type'] == 1){
            $w = [
                'lid' =>  $m_ca['lid'],
                'eid' => $employee_info['eid'],
                'lesson_type' => $m_ca['lesson_type']
            ];

        }elseif ($m_ca['lesson_type'] == 2){
            $w = [
                'lid' =>  $m_ca['lid'],
                'eid' => $employee_info['eid'],
                'lesson_type' => $m_ca['lesson_type']
            ];
        }
        $ret = $mFt_review->with(['ft_review_file','ft_review_student' => ['student']])->where($w)->order('create_time','desc')->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

}