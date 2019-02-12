<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/4
 * Time: 17:32
 */

namespace app\api\controller;

use app\api\model\ClassStudent;
use app\api\model\LessonMaterial;
use app\api\model\StudentLesson;
use think\Request;
use app\api\model\Classes as ClassesModel;
use app\api\model\ClassSchedule;
use app\api\model\CourseArrange;
use think\Db;

class Classes extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $mClass = new ClassesModel();
        if ($request->isMobile()) {
            $where = [];

            if(isset($input['teach_eid'])){
                $eid = intval($input['teach_eid']);
                $login_user = gvar('user');
                $login_employee = $login_user['employee'];
                $login_eid = $login_employee['eid'];

                if($login_eid == $input['teach_eid'] ){
                    $where = "find_in_set({$eid}, second_eids) or teach_eid = {$eid} or edu_eid = {$eid}";
                    unset($input['teach_eid']);
                }

            }

            $ret = $mClass
                ->with('lesson')
                ->where($where)
                ->getSearchResult($input);
        } else {
            //我的班级
            if(!empty($input['my']) && !empty($input['teach_eid'])) {
                //$eid = \app\api\model\User::getEidByUid(gvar('uid'));
                $eid = $input['teach_eid'];
                $where = sprintf('teach_eid = %s or find_in_set(%s, second_eids) or edu_eid = %s', $eid, $eid, $eid);
                unset($input['teach_eid'],$input['second_eid'],$input['edu_eid']);
                $ret = $mClass->where($where)->getSearchResult($input, true);
            } else {

                if(!empty($input['class_name'])) {
                    $class_name = $input['class_name'];
                    $mClass->where('class_name', 'like', "%{$class_name}%");
                    unset($input['class_name']);
                }
                if(!empty($input['week_day'])) {
                    $cids = (new ClassSchedule())->where('week_day', $input['week_day'])->column('cid');
                    if(!empty($cids)) {
                        $mClass->where('cid', 'in', array_unique($cids));
                    } else {
                        $mClass->where('cid', 'in', [-1]);
                    }
                    unset($input['week_day']);
                }
                $with = [];
                if(isset($input['with'])){
                    $with[] = $input['with'];
                }

                $ret = $mClass->getSearchResult($input, $with,true);
            }
        }

        if(!empty($input['cid'])) {
            (new ClassesModel())->updateArrange($input['cid']);
            (new ClassesModel())->updateStudentNum($input['cid']);
        }

        return $this->sendSuccess($ret);
    }

    public function me(Request $request)
    {
        $input = $request->get();
        $eid = $request->user->employee->eid;
        $where['teach_eid|edu_eid'] = $eid;
        $status = $request->param('status');
        if (isset($status)) {
            $where['status'] = $status;
        }
        $product_level = $request->param('product_level');
        if (isset($product_level)) {
            $where['product_level'] = $product_level;
        }

        $ret = m('Classes')
            ->where($where)
            ->order('create_time', 'desc')
            ->with(['lesson', 'teacher', 'assistant', 'course_arranges'])
            ->getSearchResult($input, true);
        return $this->sendSuccess($ret);
    }
    
    /**
     * 获取班级学员列表
     * api/classes/65/students
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list_students(Request $request)
    {
        $cid = $request->param('id');
        $mClass = new \app\api\model\Classes();

        $ret['list'] = $mClass->getStudents($cid);
        foreach ($ret['list'] as $k => $v){
            if ($v['student']['birth_time'] > 0){
                $ret['list'][$k]['student']['birth_time'] = date('Y-m-d',$v['student']['birth_time']);
            }
        }

        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $mClass = new \app\api\model\Classes();
        $ret = $mClass->with(['teacher', 'assistant', 'course_arranges', 'lesson', 'classroom'])->find($id);
        if(!empty($ret)) {
            $mClass->updateArrange($id);
            $mClass->updateStudentNum($id);
            $ret['students'] = $mClass->getStudents($ret['cid']);
        }

        return $this->sendSuccess($ret);
    }


    /**
     * 获得排课记录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list_arranges(Request $request)
    {
        $id = $request->param('id');
        $class = ClassesModel::get($id);
        if (!$class) {
            return $this->sendError(400, '该班级不存在或已被删除');
        }
        $list = CourseArrange::where('cid', $id)
            ->order('int_day ASC,int_start_hour ASC')
            ->select();
        $ret['list'] = collection($list)->toArray();
        return $this->sendSuccess($ret);
    }
    /**
     * 获得排班记录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list_schedules(Request $request)
    {
        $id = $request->param('id');
        $class = ClassesModel::get($id);
        if (!$class) {
            return $this->sendError(400, '该班级不存在或已被删除');
        }
        $list = ClassSchedule::where('cid', $id)
            ->select();
        $ret['list'] = collection($list)->toArray();
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  创建班级，并根据前端值是否排班，排课
     * @author luo
     * @url   classes
     * @method POST
     */
    public function post(Request $request)
    {
        $input = $request->post();
        if(array_key_exists('cid', $input)) unset($input['cid']);

        //验证参数
        $validate_rs = $this->validate($input,'Classes.post');
        if ($validate_rs !== true) {
            return $this->sendError(400, $validate_rs);
        }

        if(isset($input['second_eids']) && !empty($input['second_eids'])){
            $input['second_eid'] = $input['second_eids'][0];
        }

        $mClass = new ClassesModel();

        $cid = $mClass->createOneClass($input);

        if(!$cid){
            return $this->sendError(400,$mClass->getError());
        }

        return $this->sendSuccess($cid);

        /**
        //2018-4-13 注释掉原来的老的方法 by payhon

        $lesson = Lesson::get($input['lid']);
        if(empty($lesson)) {
            return $this->sendError(400, '不存在这个课程');
        }

        $input['year'] = date('Y', str_to_time($input['start_lesson_time']));

        $class_data = $input;
        $schedule_data = isset($input['schedule']) && !empty($input['schedule']) ? $input['schedule'] : [];
        $exclude_holidays = isset($input['exclude_holidays']) ? (int)$input['exclude_holidays'] : 0;
        $course_data = isset($input['course_arrange']) && !empty($input['course_arrange'])
                        ? ['exclude_holidays' => $exclude_holidays] : [];

        $classes_model = new ClassesModel();
        $cid = $classes_model->createClassAndScheduleAndCourse($class_data, $schedule_data, $course_data);
        if($cid === false) return $this->sendError(400, $classes_model->getErrorMsg(), 400, $classes_model->getError());

        return $this->sendSuccess($cid);
        **/
    }

    /**
     * @desc  临时班级
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function tmp_class(Request $request)
    {
        $input = $request->post();
        if(array_key_exists('cid', $input)) unset($input['cid']);

        $m_classes = new ClassesModel();
        $cid = $m_classes->createOneTmpClass($input);
        if(!$cid){
            return $this->sendError(400,$m_classes->getError());
        }

        return $this->sendSuccess($cid);
    }

    public function post_course_arranges(Request $request)
    {
        $cid = $request->param('id');
        $class = ClassesModel::get($cid);
        if (!$class) {
            return $this->sendError(400, '班级不存在或已删除');
        }
        $course = new CourseArrange;
        $input = $request->post();
        $result = $course->addOneCourseOfClass($class, $input);
        if (!$result) {
            return $this->sendError(400, $course->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  修改班级接口
     * @author luo
     * @method PUT
     */
    public function put(Request $request)
    {
        $input = $request->put();
        $class_data = $input;
        $schedule_data = isset($input['schedules']) ? $input['schedules'] : [];

        $model = new ClassesModel();
        $rs = $model->updateClassAndSchedule($class_data, $schedule_data);
        if(!$rs) return $this->sendError(400, $model->getErrorMsg(), 400, $model->getError());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $cid    = $request->param('id');
        $result = ClassesModel::deleteSingleClass($cid);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }
        return $this->sendSuccess();
    }

    public function post_class_student(Request $request) {
        $class_id = $request->param('id');
        $input = $request->post();
        $input['cid'] = $class_id;

        $validate_rs = $this->validate($input, 'ClassStudent');
        if($validate_rs !== true) {
            return $this->sendError(400, $validate_rs);
        }

        /** @var ClassesModel $class */
        $class = ClassesModel::get($class_id);
        $m_cs = new ClassStudent();
        $rs = $m_cs->addOneStudentToClass($class, $input['sid']);
        if($rs === false) return $this->sendError(400, $m_cs->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 退班操作
     * @param Request $request
     */
    public function delete_students(Request $request)
    {
        $cid = $request->param('id');
        $sid = $request->param('subid');

        $m_cs = new ClassStudent();
        $class_student = $m_cs->where('cid', $cid)->where('sid', $sid)
            ->where('status < ' . $m_cs::STATUS_CLASS_TRANSFER)->find();
        if (empty($class_student)) {
            return $this->sendError(400, '班级不存在此学生');
        }

        $rs = $m_cs->removeStudentFromClass($class_student['cs_id']);
        if(!$rs) return $this->sendError(400, $m_cs->getErrorMsg(), 400, $m_cs->getError());

        return $this->sendSuccess();
    }

    public function get_ext_id(Request $request)
    {
        $name = $request->param('name');
        if (empty($name)) {
            return $this->sendError(400, '参数name不能为空');
        }
        $result = m('Classes')->getExtId($name);
        if (!$result) {
            return $this->sendError(400, m('Classes')->getError());
        }
        $ret['data'] = $result;
        return $this->sendSuccess($ret);
    }

    public function get_dss_students(Request $request)
    {
        $class_ext_id = $request->param('ext_id');
        if (empty($class_ext_id)) {
            return $this->sendError(400, '参数ext_id不能为空');
        }
        $result = m('Classes')->getDssStudents($class_ext_id);
        if ($result === false) {
            return $this->sendError(400, m('Classes')->getError());
        }
        $ret['list'] = $result;
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  为指定班级分配学员
     * @author luo
     * @method POST
     */
    public function do_assign(Request $request)
    {
        $cid = $request->param('id');
        $sids = input('sids/a') ? input('sids/a') : [];
        $cu_ids = input('cu_ids/a') ? input('cu_ids/a') : [];

        $model = new ClassStudent();
        $rs = $model->assignStudentOrCustomer($cid, $sids, $cu_ids);
        if(!$rs) return $this->sendError(400, $model->getErrorMsg(), 400, $model->getError());

        return $this->sendSuccess();
    }

    /**
     * @desc  把学生从班级移除
     * @author luo
     */
    public function do_unassign(Request $request)
    {
        $cs_id = input('cs_id/d');

        $model = new ClassStudent();
        $rs = $model->removeStudentFromClass($cs_id);
        if(!$rs) return $this->sendError(400, $model->getErrorMsg(), 400, $model->getError());

        return $this->sendSuccess();
    }

    public function do_transfer(Request $request)
    {
        $input = $request->post();
        $rule = [
            'sid' => 'require',
            'old_cid' => 'require|number',
            'new_cid' => 'require|number',
        ];
        $rs = $this->validate($input, $rule);
        if($rs !== true) return $this->sendError(400, $rs);

        $new_class = ClassesModel::get(['cid' => $input['new_cid']]);
        if( $new_class['student_nums'] >= $new_class['plan_student_nums'] ) {
            return $this->sendError(400, '转入班级人数已满');
        }

        $old_class = ClassesModel::get($input['old_cid']);
        if($new_class['sj_id'] != $old_class['sj_id']) return $this->sendError(400, '转入的班级科目与原班级科目不一致');

        $is_force = input('force', 0);
        if(($new_class['unit_price'] > 0 || $old_class['unit_price'] > 0) && $new_class['unit_price'] != $old_class['unit_price'] && !$is_force) {
            return $this->sendConfirm('新班级与原班级的课时单价不一样是否仍然转入？');
        }

        if($new_class['unit_price'] == 0 && $old_class['unit_price'] == 0) {
            if($new_class['lid'] != $old_class['lid'] && !$is_force) {
                return $this->sendConfirm('新班级与原班级的课程不一样是否仍然转入？');
            }
        }

        $m_class = new ClassesModel();
        $rs = $m_class->transferClass($input);
        if($rs === false) return $this->sendError(400, $m_class->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  搜索班级，主要是下单搜索班级
     * @author luo
     * @method GET
     */
    public function search(Request $request)
    {
        $input = $request->param();
        $where = [];

        //class表搜索条件
        $schedule_field = Db::name('class_schedule')->getTableFields();
        if(!empty($input)) {
            foreach($input as $key => $val) {
                if(in_array($key, $schedule_field)) {
                    $where['s.'.$key] = $val;
                    unset($input[$key]);
                }
            }
        }

        //排班搜索条件
        $class_field = Db::name('class')->getTableFields();
        if(!empty($input)) {
            $where['c.og_id'] = gvar('og_id') ? gvar('og_id') : 0;
            $where['c.delete_time'] = null;
            $bid = $request->bid;
            if(is_numeric($bid) && $bid > 0) {
                $where['c.bid'] = $bid;
            } else {
                $where['c.bid'] = ['in', explode(',', $bid)];
            }
            foreach($input as $key => $val) {
                if(in_array($key, $class_field)) {
                    if($key == 'class_name') {
                        $where['c.'.$key] = ['like', '%'.$val.'%'];
                    } else {
                        $where['c.'.$key] = $val;
                    }
                }
            }

        }

        $ret['page'] = $page = input('page', 1);
        $ret['pagesize'] = $pagesize = input('pagesize', config('default_pagesize'));
        $ret['total'] = Db::name('class')->alias('c')->join('class_schedule s', 'c.cid = s.cid', 'left')
            ->where($where)->group('c.cid')->count();

        $ret['list'] = Db::name('class')->alias('c')->join('class_schedule s', 'c.cid = s.cid', 'left')
            ->where($where)->where('status','in',[0,1])->group('c.cid')->page($page, $pagesize)->field('c.*')->order('create_time DESC')->select();

        $m_lesson = new \app\api\model\Lesson();

        if(!isset($input['with']) || empty($input['with'])){
            $input['with'] = 'lesson';
        }
        foreach($ret['list'] as &$per_class) {
            $schedule = ClassSchedule::all(['cid' => $per_class['cid']]);
            $per_class['schedules'] = $schedule ? $schedule : [];
            if(isset($input['with']) && $input['with'] == 'lesson'){
                $lesson_info = get_lesson_info($per_class['lid']);
                if($lesson_info) {
                    unset($lesson_info['public_content']);
                    $lesson_info['define_price'] = $m_lesson->getDefinePriceAttr(0, $lesson_info);
                    $lesson_info['define_promotion_rule'] = $m_lesson->getDefinePromotionRuleAttr(0, $lesson_info);
                }else{
                    $lesson_info = null;
                }
                $per_class['lesson'] = $lesson_info;
            }
        }

        return $this->sendSuccess($ret);
    }

    /*班级结课*/
    public function do_close(Request $request)
    {
        /*
        $cid = $request->param('id');
        $cls = ClassesModel::get($cid);
        if (empty($cls)) {
            return $this->sendError(400, 'resource not found!');
        }
        $sl_id = input('sl_id/a') ? input('sl_id/a') : [];
        $rs = $cls->closeClass($sl_id);
        if (!$rs) {
            return $this->sendError(400, $cls->getError());
        }
        return $this->sendSuccess();
        */
        $cid = input('id/d');
        $end_sl_sids = input('post.info/a');
        $mClass = new ClassesModel();
        $result = $mClass->endClass($end_sl_sids,$cid);
        if(!$result){
            return $this->sendError(400,$mClass->getError());
        }
        return $this->sendSuccess();
    }


    public function do_end(Request $request)
    {
        $cid = input('id/d');
        $students = input('post.students');
        $mClass = new ClassesModel();

        $result = $mClass->endClass($students,$cid);

        if(!$result){
            return $this->sendError(400,$mClass->getError());
        }

        return $this->sendSuccess();
    }

    /*班级升班*/
    public function do_upgrade(Request $request)
    {
        $cid = $request->param('cid');  // 新班级CID
        $old_cid = input('id/d');

        // $from_cls = ClassesModel::get('old_cid');
        // $to_cls   = ClassesModel::get('cid');
        $from_cls = (new ClassesModel)->where('cid',$old_cid)->find();
        $to_cls = (new ClassesModel)->where('cid',$cid)->find();

        if($from_cls['sj_id'] != $to_cls['sj_id']){
            return $this->sendError(400,'班级科目不一样，不能升班');
        }

        $cls = ClassesModel::get($cid);   // 新班级 班级信息
        if (empty($cls)) {
            return $this->sendError(400, '目标班级不存在，或已删除');
        }
        $input = $request->post();
        $rule = [
            'cid|新班级ID'        => 'require|number',
            'sid|需要升级的学生ID' => 'require|array',
        ];
        $input['sids'] = array_unique($input['sid']);
        
        $from_class_student_nums = count($input['sids']);
        $to_class_reamin_student_nums = $to_cls->plan_student_nums - $to_cls->student_nums;
        if($from_class_student_nums > $to_class_reamin_student_nums){
            return $this->sendError(400,'目标班级学员数超员，请减少升班学员数量');
        }

        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $input['cid'] = $cid;
        $rs = $cls->upgrade($input,$old_cid);
        if (!$rs) {
            return $this->sendError(400, $cls->getError());
        }
        return $this->sendSuccess();
    }

}