<?php
/**
 * luo 排班
 */

namespace app\api\controller;

use app\api\model\ClassSchedule;
use app\api\model\Classes;
use think\Request;

class ClassSchedules extends Base
{
    public function get_list(Request $request) {
        $input = $request->get();

        $schedules = m('ClassSchedule')->with('classes')->getSearchResult($input);

        return $this->sendSuccess($schedules);
    }

    /**
     * @param bid:1
     * @param class_name:高考小班
     * @param class_no:1
     * @param lid:1
     * @param teach_eid:1
     * @param edu_eid:1
     * @param int_start_hour:1900
     * @param int_end_hour:2000
     * @param start_lesson_time:2017-09-01
     * @param end_lesson_time:2017-12-31
     * @param week_day:4
     * @param time_index:0
     * @param lesson_times:6
     * @desc 增加排班
     */
    public function post(Request $request)
    {
        $input = $request->post();

        //在原有班级基础上增加排班
        if(!empty($input['cid'])) {
            //验证参数
            $rule = [
                ['week_day|星期几', 'require|number'],
                ['int_start_hour|当天开始时间(格式1900)', 'require|number'],
                ['int_end_hour|当天结束时间(格式2100)', 'require|number'],
            ];
            $validate_rs = $this->validate($input,$rule);
            if($validate_rs !== true) {
                return $this->sendError(400, $validate_rs);
            }

            $class = Classes::get($input['cid']);
            if(empty($class)) {
                return $this->sendError(400, '班级不存在,先创建');
            }

            $mClassSchedule = new ClassSchedule();
            $result = $mClassSchedule->addSchedule($class, $input);

        //增加班级、排班
        } else {
            if(isset($input['cid'])){
                unset($input['cid']);
            }
            //验证参数
            $rule = [
                ['class_name|班级名','require'],
                ['cr_id|教室id', 'require|number'],
                ['lesson_times|排课次数', 'require|number'],
                ['week_day|星期几', 'require|number'],
                ['start_lesson_time|上课开始时间', 'require|date'],
                ['end_lesson_time|上课结束时间', 'date'],
                ['int_start_hour|当天开始时间格式1900', 'require|number'],
                ['int_end_hour|当天结束时间格式2100', 'require|number'],
            ];
            $validate_rs = $this->validate($input,$rule);
            if ($validate_rs !== true) {
                return $this->sendError(400, $validate_rs);
            }

            $right = $this->validate($input, 'Classes');
            if ($right !== true) {
                return $this->sendError(400, $right);
            }

            $mClassSchedule = new ClassSchedule();
            $result = $mClassSchedule->addClassAndSchedule($input);
        }

        if(!$result){
            return $this->sendError(400,$mClassSchedule->getError());
        }

        return $this->sendSuccess();
    }


    public function multi_schedules(Request $request){
        $input = $request->post();

        $class = Classes::get($input['cid']);
        if(empty($class)) {
            return $this->sendError(400, '班级不存在,先创建');
        }
        $mClassSchedule = new ClassSchedule();
        $result = $mClassSchedule->batAddSchedule($class,$input['schedules']);
        if(!$result){
            return $this->sendError(400,$mClassSchedule->getError());
        }

        return $this->sendSuccess();
    }


    /**
     * @desc  修改排班
     * @url   /api/class_schedules/:id
     * @method PUT
     */
    public function put(Request $request) {
        $put = $request->put();
        $csd_id = $request->param('id');
        //验证参数
        if(empty($csd_id) || empty($put)) {
            return $this->sendError(400, '排班id不存在或者没有要修改的内容');
        }

        $put['csd_id'] = $csd_id;

        $mClassSchedule = new ClassSchedule();
        $rs = $mClassSchedule->updateSchedule($put);

        if(!$rs) {
            return $this->sendError(400,$mClassSchedule->getError());
        }

        return $this->sendSuccess();

    }

    /**
     * @param id integer 排班id
     * @desc  删除排班
     * @url   /api/class_schedule/:id
     * @method DELETE
     */
    public function delete(Request $request) {
        $input['csd_id'] = $request->param('id');

        $mClassSchedule = new ClassSchedule();
        $rs = $mClassSchedule->deleteSchedule($input);

        if(!$rs) {
            return $this->sendError(400, $mClassSchedule->getError());
        }

        return $this->sendSuccess();
    }


}