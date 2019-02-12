<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/17
 * Time: 15:16
 */

namespace app\api\controller;

use app\api\model\StudentLessonHour;
use think\Request;

class StudentLessonHours extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $model = new StudentLessonHour();
        $rs = $model->getSearchResult($input);
        foreach ($rs['list'] as &$item) {
            $item['course_arrange'] = $this->m_course_arrange->where('ca_id',$item['ca_id'])->find();
        }
        $rs['sum_lesson_hours']  = $model->autoWhere($input)->sum('lesson_hours');
        $rs['sum_lesson_amount'] = $model->autoWhere($input)->sum('lesson_amount');
        $input['is_pay'] = 0;
        $rs['sum_lesson_amount_unpayed'] = $model->autoWhere($input)->sum('lesson_amount');
        return $this->sendSuccess($rs);
    }

    /**
     * @desc  自由登记课耗
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();

        $m_slh = new StudentLessonHour();
        //处理单个扣除的情况
        if(empty($post[0])) {
            $result = $m_slh->addStudentConsume($post);
            if(!$result){
                return $this->sendError(400,$m_slh->getError());
            }
            return $this->sendSuccess();
        }

        $rs = $m_slh->addMultiConsume($post);
        if($rs === false) return $this->sendError(400, $m_slh->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  未扣课时课耗扣课时
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post_pay(Request $request)
    {
        $slh_id = input('id');
        $student_lesson_hour = StudentLessonHour::get($slh_id);
        if(empty($student_lesson_hour) || $student_lesson_hour['is_pay'])
            return $this->sendError(400, '没有学员课耗或者已经付款');
        
        $post = $request->post();
        if(!isset($post['sl_id'])) return $this->sendError(400, 'sl_id error');
        $rs = $student_lesson_hour->pay($post['sl_id'], true);
        if($rs === false) return $this->sendError(400, $student_lesson_hour->getErrorMsg());
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $slh_id = input('id');
        $put = $request->put();
        $student_lesson_hour = StudentLessonHour::get($slh_id);
        if(empty($student_lesson_hour)) return $this->sendError(400, '课耗记录不存在');

        $rs = $student_lesson_hour->updateStudentLessonHour($put);
        if($rs === false) return $this->sendError(400, $student_lesson_hour->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $slh_id = input('id');
        /** @var StudentLessonHour $student_lesson_hour */
        $student_lesson_hour = StudentLessonHour::get($slh_id);
        if($student_lesson_hour['change_type'] == StudentLessonHour::CHANGE_TYPE_ATTENDANCE) {
            return $this->sendError(400, '考勤课耗不能删除');
        }

        if(empty($student_lesson_hour)) return $this->sendSuccess();

        $rs = $student_lesson_hour->delStudentLessonHour();
        if($rs === false) return $this->sendError(400, $student_lesson_hour->getErrorMsg());

        return $this->sendSuccess();
    }

}