<?php
/**
 * Author: luo
 * Time: 2017-12-19 16:08
**/

namespace app\sapi\controller;

use app\sapi\model\ClassStudent;
use app\sapi\model\Classes;
use app\sapi\model\CourseArrange;
use app\sapi\model\CourseArrangeStudent;
use app\sapi\model\StudentLeave;
use think\Request;

class CourseArranges extends Base
{
    /**
     * @desc  学生的课程
     * @author luo
     * @method GET
     */
    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();

        $w_cs['sid']    = $sid;
        $w_cs['status'] = 1;
        $w_cs['is_end'] = 0;

        $m_cs  = new ClassStudent();
        $m_cas = new CourseArrangeStudent();

        $cids = $m_cs->where($w_cs)->column('cid');
        $ca_ids = $m_cas->where('sid', $sid)->column('ca_id');

        $where = '';
        if(!empty($cids)) {
            $where .= sprintf('cid in (%s)', implode(',', $cids));
        }
        if(!empty($ca_ids)) {
            if(!empty(trim($where))) {
                $where .= ' or ';
            }
            $where .= sprintf('ca_id in (%s)', implode(',', $ca_ids));
        }
        if(!empty(trim($where))) $where = '(' . $where . ')';

        if(empty($where)) return $this->sendSuccess(['list' => []]);

        $m_ca = new CourseArrange();
        $ret = $m_ca->with(['lesson.attachments', 'teacher'])->where($where)->getSearchResult($input);

        $slv_list = [];
        $allow_leave_times = 0;
        if(!empty($ret['list'])){
            $params = user_config('params');
            $allow_leave_times = $params['student_leave']['times_limit'];
            if($params['student_leave']['enable'] && $allow_leave_times > 0){
                $w_slv['sid'] = $sid;
                $slv_list     = get_table_list('student_leave',$w_slv);
                if(!$slv_list){
                    $slv_list = [];
                }
            }
        }
        foreach($ret['list'] as &$row) {
            $row['leave_times']   = $this->cacu_leave_times($row['lid'],$row['cid'],$slv_list);
            if($allow_leave_times > 0 && $row['leave_times'] >= $allow_leave_times){
                $row['allow_leave'] = 0;
            }else{
                $row['allow_leave'] = 1;
            }
            $row['student_leave'] = StudentLeave::get(['ca_id' => $row['ca_id'], 'sid' => $sid]);
        }
        $ret['slvs'] = $slv_list;

        return $this->sendSuccess($ret);
    }


    /**
     * 获取活动班级排课
     * @param  Request $request [description]
     * @return [type]           [get]
     */
    public function activity_course_arranges(Request $request)
    {
        $input = $request->param();
        $model = new CourseArrange;
        $bid = $request->header('x-bid');
 
        $w_class['class_type'] = Classes::CLASS_TYPE_ACTIVITY;
        $w_class['bid'] = $bid;
        $w_class['og_id'] = gvar('og_id');
        if(isset($input['class_name'])){
            $w_class['class_name'] = ['like','%'.$input['class_name'].'%'];
        }

        $cids = (new Classes)->where($w_class)->column('cid');
        if(empty($cids)){
            return $this->sendSuccess();
        }

        $w = [];
        $w['cid'] = ['in',$cids];

        $ret = $model->where($w)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

    protected function cacu_leave_times($lid,$cid,&$slv_list){
        static $cache = [];
        if($lid > 0){
            $cache_key = 'l'.$lid;
        }else{
            $cache_key = 'c'.$cid;
        }
        if(isset($cache[$cache_key])){
            return $cache[$cache_key];
        }

        $times = 0;
        if(empty($slv_list)){
            return $times;
        }
        foreach($slv_list as $slv){
            if($slv['cid'] == $cid && $slv['lid'] == $lid){
                $times++;
            }
        }

        $cache[$cache_key] = $times;
        return $times;
    }

    /**
     * @desc  学生的课程日期
     * @author luo
     * @method GET
     */
    public function get_course_day(Request $request) {
        $sid = global_sid();

        $cids = (new ClassStudent())->where('sid', $sid)->column('cid');
        $ca_ids = (new CourseArrangeStudent())->where('sid', $sid)->column('ca_id');

        $where = '';
        if(!empty($cids)) {
            $where .= sprintf('cid in (%s)', implode(',', $cids));
        }
        if(!empty($ca_ids)) {
            if(!empty(trim($where))) {
                $where .= ' or ';
            }
            $where .= sprintf('ca_id in (%s)', implode(',', $ca_ids));
        }
        if(!empty(trim($where))) $where = '(' . $where . ')';

        if(empty($where)) return $this->sendSuccess(['list' => []]);

        $m_ca = new CourseArrange();
        $int_day_list = $m_ca->where($where)->column('int_day');
        $int_day_list = array_values(array_unique($int_day_list));
        return $this->sendSuccess(['list' => $int_day_list]);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $ca_id = input('id/d');
        $course = CourseArrange::get($ca_id, ['lesson.attachments', 'teacher']);
        return $this->sendSuccess($course);
    }

    /**
     * @desc  方法描述
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method POST
     */
    public function student_num(Request $request)
    {
        $post = $request->post();
        if(empty($post)) return $this->sendError(400, '参数错误');

        $rule = [
            'cid' => 'require',
            'int_day' => 'require',
            'int_start_hour' => 'require',
            'int_end_hour' => 'require',
        ];
        $i = 1;
        foreach($post as $row) {
            $rs = $this->validate($row, $rule);
            if($rs !== true) return $this->sendError(400, $rs);
            if($i > 100) return $this->sendError(400, '参数错误');
        }

        $m_cas = new CourseArrangeStudent();
        foreach($post as &$row) {
            $where = [
                'cid' => $row['cid'],
                'int_day' => format_int_day($row['int_day']),
                'int_start_hour' => format_int_hour($row['int_start_hour']),
                'int_end_hour' => format_int_hour($row['int_end_hour']),
            ];
            $student_num = $m_cas->where($where)->count();
            $row['student_num'] = $student_num;
        }

        return $this->sendSuccess($post);
    }


}