<?php
/** 
 * Author: luo
 * Time: 2017-10-24 09:37
**/

namespace app\api\controller;

use app\api\model\HomeworkTask;
use app\api\model\Lesson;
use app\api\model\Student;
use app\api\model\Classes;
use app\api\model\StudentAttendance;
use think\Request;
use app\api\model\ClassAttendance as ClassAttendanceModel;
use app\api\model\TrialListenArrange;

class ClassAttendances extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $m_ca = new ClassAttendanceModel();
        
        $where = [];
        if(isset($input['eid']) && $input['eid'] > 0){
            $eid = $input['eid'];
            $cids = (new Classes)->where('edu_eid',$eid)->whereOr('teach_eid',$eid)->whereOr('second_eid',$eid)->column('cid');

            $m_ca->where(function ($query) use ($eid,$cids){
                $query->where('eid',$eid);
                $query->whereOr('second_eid',$eid);
                if(!empty($cids)){
                    $query->whereOr('cid','in',$cids);
                }
            });
        }
        unset($input['eid']);
	
        if(isset($input['with'])){
            $with = $input['with'];
            if(strpos($with,'review') === false){
                $with .= ',review';
            }
            unset($input['with']);
        }else{
            $with = 'review';
        }

        $ret = $m_ca->with($with)->where($where)->getSearchResult($input);

        // echo $m_ca->getLastSql();exit;
        $mHomework_task = new HomeworkTask();
        foreach ($ret['list'] as &$item) {
            /*为一对一，一对多附加考勤对象信息*/
            if (in_array($item['lesson_type'], [Lesson::LESSON_TYPE_ONE_TO_ONE, Lesson::LESSON_TYPE_ONE_TO_MULTI])) {
                $sid_array = StudentAttendance::where('catt_id', $item['catt_id'])->column('sid');
                asort($sid_array);
                $item['sid_list'] = $sid_array;
                if (!empty($sid_array)) {
                    $item['student_name_list'] = Student::whereIn('sid', $sid_array)->order('sid','asc')->column('student_name');
                } else {
                    $item['student_name_list'] = [];
                }
            }
            $item['homework_task'] = $mHomework_task->where('ca_id',$item['ca_id'])->find();
        }
        return $this->sendSuccess($ret);
    }

    /**
     * 撤销一次（任何考勤都会产生一条class_attendance记录）考勤
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $catt_id = $request->param('id');
        $class_att_model = ClassAttendanceModel::get($catt_id);
        if (empty($class_att_model)) {
            return $this->sendError(404, 'resource not found');
        }
        $rs = $class_att_model->cancelAttendance();
        if (!$rs) {
            return $this->sendError(400, $class_att_model->getError());
        }
        return $this->sendSuccess();
    }

    public function get_list_student_attendances(Request $request)
    {
        $catt_id = $request->param('id');
        $input = $request->get();
        $model = new StudentAttendance();
        $ret = $model->where('catt_id', $catt_id)->getSearchResult($input);
        $ret['trial_list'] = TrialListenArrange::all(['catt_id' => $catt_id], ['student', 'customer']);/*包含试听记录*/
        return $this->sendSuccess($ret);
    }

    /**
     * 批量考勤记录确认
     * @param Request $request
     */
    public function check_attendances(Request $request){

        $input = input();
        if (!isset($input['catt_ids'])) return $this->sendError(400,'catt_ids exists');
        $mClassAttendance = new ClassAttendanceModel();

        $rs = $mClassAttendance->batchConfirmAttendance($input['catt_ids']);
        if ($rs === false) return $this->sendError(400,$mClassAttendance->getError());

        return $this->sendSuccess();
    }

    /**
     * 批量取消考勤记录确认
     * @param Request $request
     */
    public function cancel_check_attendances(Request $request){

        $input = input();
        if (!isset($input['catt_ids'])) return $this->sendError(400,'catt_ids exists');
        $mClassAttendance = new ClassAttendanceModel();

        $rs = $mClassAttendance->batchCalcelConfirmAttendance($input['catt_ids']);
        if ($rs === false) return $this->sendError(400,$mClassAttendance->getError());

        return $this->sendSuccess();
    }
}