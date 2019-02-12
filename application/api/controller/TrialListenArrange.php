<?php
/** 
 * Author: luo
 * Time: 2017-10-11 18:09
**/

namespace app\api\controller;

use app\api\model\CourseArrange;
use app\api\model\Customer;
use think\Request;
use app\api\model\CourseArrange as CourseArrangeModel;
use app\api\model\TrialListenArrange as TrialModel;
use app\api\model\Student;

class TrialListenArrange extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();

        $model = new TrialModel();
        $where = '';
        if(isset($input['name']) && !empty($input['name'])) {

            $name = $input['name'];
            $sids = Student::all(function($query) use($name) {
                $query->field('sid')->where('student_name', 'like', '%'. $name.'%');
            });
            $sids = array_column($sids, 'sid');

            $cu_ids = Customer::all(function($query) use($name) {
                $query->field('cu_id')->where('name', 'like', '%'.$name.'%');
            });
            $cu_ids = array_column($cu_ids, 'cu_id');

            $condition = [];
            if(!empty($sids)) $condition[] = sprintf('sid in (%s)', implode(',', $sids));
            if(!empty($cu_ids)) $condition[] = sprintf('cu_id in (%s)', implode(',', $cu_ids));
            if(empty($condition)) {
                $where = 'sid = -1';
            } else {
                $where .= implode(' or ', $condition);
            }
        }

        $ret = $model->with(['course', 'customer', 'student', 'oneClass'])->where($where)->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  试听安排,跟班试听或者排班试听
     * @author luo
     * @param Request $request
     * @url  trial_listen_arrange
     * @method POST
     */
    public function post(Request $request)
    {
        $input = input();

        $course_data = $input['course'];
        $student_data = isset($input['students']) && !empty($input['students']) ? $input['students'] : [];

        //--1-- 增加试听课并增加试听人员
        if(!isset($course_data['ca_id']) || !$course_data['ca_id']) {
            $course_model = new CourseArrangeModel();
            $ca_id = $course_model->createCourseAndTrial($course_data, $student_data);
            if($ca_id === false) return $this->sendError(400, $course_model->getError());

            //--2-- 跟班试听
        } else {

            if(empty($student_data)) return $this->sendError(400, '学生不能为空');
            $trial_model = new TrialModel();
            $rs = $trial_model->createManyTrial($course_data['ca_id'], $student_data);
            if(!$rs) return $this->sendError(400, $trial_model->getErrorMsg(), 400, $trial_model->getError());
            $ca_id = $course_data['ca_id'];
        }

        if(!empty($input['is_push']) && $input['is_push'] == 1) {
            $trial_model = isset($trial_model) ? $trial_model : new TrialModel();
            $course =  CourseArrange::get($ca_id);
            $employees = [['eid' => $course['teach_eid']]];
            $rs = $trial_model->wechat_tpl_notify_employee('course_remind', TrialModel::makeMsgData($course), $employees);
            if($rs === false) return $this->sendError(400, '试听创建成功，推送老师失败，原因：' . $trial_model->getErrorMsg());
        }

        return $this->sendSuccess();
    }


    /**
     * 登记试听情况
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function put(Request $request)
    {
        $tla_id = input('id/d');
        if(empty($tla_id)){
            return $this->sendError(400,'param error');
        }
        $put = $request->param();

        $trial = TrialModel::get($tla_id);
        if(empty($trial)){
            return $this->sendError(400,'试听不存在，或已经删除');
        }

        $res = $trial->updateTrialStatus($trial,$put);

        if($res === true){
            return $this->sendSuccess('操作成功');
        }

    }



}