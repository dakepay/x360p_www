<?php
/**
 * Author: payhon
 * Time: 2017-11-09 15:59
**/

namespace app\api\controller;


use app\api\model\EmployeeStudent;
use app\api\model\Student;
use app\api\model\StudentLesson;
use app\api\model\TextbookSection;
use app\api\model\User;
use app\api\model\StudentAttendance;

use think\Request;

class StudentLessons extends Base
{

    public function get_list(Request $request)
    {

        $input = $request->param();
        $model = new StudentLesson();

        if(!isset($input['sid'])) {
            $m_student    = new Student();
            $w_s['status'] = ['EGT',Student::STATUS_QUIT];

            $quit_student = $m_student->where($w_s)->select();
            $seal_sids = array_column($quit_student,'sid');


            if (!empty($input['my'])) {
                $eid = User::getEidByUid(gvar('uid'));
                if ($eid <= 0) return $this->sendSuccess(['list' => []]);

                $sids = (new EmployeeStudent())->where('eid', $eid)->column('sid');
                
                $mCas = new \app\api\model\CourseArrangeStudent();
                $sids2 = $mCas
                ->distinct(true)
                ->field('cas.sid')
                ->alias('cas')
                ->join('course_arrange ca','cas.ca_id=ca.ca_id','left')
                ->where('ca.teach_eid',$eid)
                ->order('cas.int_day DESC')
                ->column('sid');

                if(!empty($sids2)){
                    $sids = array_merge($sids,$sids2);
                }
               
                if (empty($sids)) return $this->sendSuccess(['list' => []]);
                
                $model->where('sid', 'in', $sids);
            }

            $m_student = new Student;
            if (isset($input['cid']) && $input['cid'] > 0) {
                $w_cs['cid'] = $input['cid'];
                $w_cs['is_end'] = 0;
                $w_cs['status'] = ['LT', 2];

                $cs_list = get_table_list('class_student', $w_cs);
                $sids = [];
                if ($cs_list) {
                    foreach ($cs_list as $cs) {
                        array_push($sids, $cs['sid']);
                    }
                }
                $model->where('sid', 'in', $sids);
                unset($input['cid']);
            }

            if (!empty($input['student_name'])) {
                $student_name = trim($input['student_name']);
                unset($input['student_name']);
                $m_student->where(function ($query) use ($student_name) {
                    $query->where('student_name', 'like', '%' . $student_name . '%');
                    if (preg_match("/^[a-z]*$/i", $student_name)) {/*全英文*/
                        $query->whereOr('pinyin', 'like', '%' . $student_name . '%');
                        $query->whereOr('pinyin_abbr', $student_name);
                    }
                });
                $s_list = $m_student->select();
                $sids = [];
                if($s_list){

                    $sids = array_column($s_list,'sid');
                }
                if (!empty($sids)) {
                    $model->where('sid', 'in', $sids);
                }
            }


            $model->where('sid', 'not in', $seal_sids);
        }


        if(isset($input['lesson_type']) && is_numeric($input['lesson_type'])){
            if(isset($input['is_package'])){
                unset($input['is_package']);
            }
            $model->where(function($query) use ($input){
                $query->where('lesson_type',$input['lesson_type'])->whereOr('is_package',1);
            });
            unset($input['lesson_type']);
        }

        $ret = $model->with(['order_items' => ['order_transfer_item', 'order_refund_item']])->getSearchResult($input);
        if(isset($input['last_tbs']) && $input['last_tbs'] == 1){
            $mTextbookSection = new TextbookSection();
            foreach ($ret['list'] as $k => $v){
                $ret['list'][$k]['last_tbs'] = $mTextbookSection->getLastTbs($v['sid'],$v['lid']);
            }
        }

        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $sl_id = input('id');
        $put = $request->put();
        $student_lesson = StudentLesson::get($sl_id);
        if(empty($student_lesson)) return $this->sendError(400, 'student_lesson不存在');

        $rs = $student_lesson->editStudentLesson($put);
        if($rs === false) return $this->sendError(400, $student_lesson->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  学生绑定老师
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post_employee_student(Request $request)
    {
        $sl_id = input('id');
        if($sl_id <= 0) return $this->sendError(400, 'sl_id错误');

        $m_es = new EmployeeStudent();
        $is_existed = $m_es->where('sl_id', $sl_id)->find();
        if($is_existed) return $this->sendSuccess();

        $post = $request->post();
        $post['sl_id'] = $sl_id;
        $rs = $m_es->addOneEmployeeStudent($post);
        if($rs === false) return $this->sendError(400, $m_es->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  学生解除绑定老师
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete_employee_student(Request $request)
    {
        $sl_id = input('id');
        $sid = input('subid');
        $employee_student = EmployeeStudent::get(['sl_id' => $sl_id, 'sid' => $sid]);
        if(empty($employee_student)) return $this->sendSuccess();

        $rs = $employee_student->delete();
        if($rs === false) return $this->sendError(400, $employee_student->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 学生学习进度
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_list_textbook_section(Request $request)
    {
        $sl_id = input('id/d');

        $page = input('page/d',1);
        $pagesize = input('pagesize/d',10);

        $mStudentAttendance = new StudentAttendance();
        $student_lesson_info = get_student_lesson_info($sl_id);
        $w_sa = [
            'sa.sid' => $student_lesson_info['sid'],
            'slh.sl_id' => $sl_id,
        ];
        $student_attendance_list['list'] = $mStudentAttendance
            ->alias('sa')
            ->join('student_lesson_hour slh','sa.catt_id = slh.catt_id','left')
            ->where($w_sa)
            ->with('student')
            ->order('sa.int_day','desc')
            ->page($page,$pagesize)
            ->select();
        if (!empty($student_attendance_list)){
            $total = $mStudentAttendance->alias('sa')->join('student_lesson_hour slh','sa.catt_id = slh.catt_id','left')->where($w_sa)->count();
            foreach ($student_attendance_list['list'] as $k => $student_attendance){
                $class_attendance = get_class_attendance_info($student_attendance['catt_id']);
                if ($class_attendance['tb_id'] > 0 && $class_attendance['tbs_id'] > 0){
                    $last_tbs = get_last_tbs_info($class_attendance['tbs_id']);
                    $student_attendance['textbook_section'] = $last_tbs;
                }
            }
            $student_attendance_list['page'] = $page;
            $student_attendance_list['pagesize'] = $pagesize;
            $student_attendance_list['total'] = $total;
            return $this->sendSuccess($student_attendance_list);
        }

        return $this->sendSuccess();
    }
}