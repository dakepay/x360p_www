<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/7
 * Time: 16:33
 */

namespace app\api\model;

use think\Exception;

class EmployeeLessonHour extends Base
{
    protected $append = ['class_name'];


    public static $detail_fields = [
        ['type'=>'index','width'=>60,'align'=>'center'],
        ['title'=>'校区','key'=>'bid','align'=>'center'],
        ['title'=>'员工姓名','key'=>'eid','align'=>'center'],
        ['title'=>'上课时段','key'=>'time_section','align'=>'center','width'=>170],
        ['title'=>'课程类型','key'=>'lesson_type','align'=>'center'],
        ['title'=>'总课时数','key'=>'total_lesson_hours','align'=>'center'],
        ['title'=>'总课时金额','key'=>'total_lesson_amount','align'=>'center'],
    ];

    public function getClassNameAttr($value,$data){
        $course_name = get_class_name($data['cid']);

        return $course_name;
    }

    protected function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    protected function setIntStartHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    protected function setIntEndHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function cls()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

    /**
     * 根据学生的一条考勤记录创建或动态更新教师在该次上课中的产能
     * @param StudentAttendance $attendance
     */
    public static function addBenefitByAttendance(StudentAttendance $attendance)
    {
//        if (empty($attendance['is_consume'])) {
//            return false;
//        }
        $w = [];
        $w['catt_id'] = $attendance['catt_id'];
        $model = self::get($w);

//        $w = [];
//        $w['eid'] = $attendance['eid'];
//        if (isset($attendance['ca_id'])) {
//            $w['ca_id'] = $attendance['ca_id'];
//        }
//        $w['int_day']        = $attendance['int_day'];
//        $w['int_end_hour']   = $attendance['int_end_hour'];
//        $w['int_start_hour'] = $attendance['int_start_hour'];
//        $model = self::get($w);//todo 优化

        $data = [];
        $table_field = self::getTableInfo()['fields'];
        $omit_field  = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];
        foreach ($table_field as $key => $value) {
            if (in_array($value, $omit_field)) {
                unset($table_field[$key]);
            }
        }
        foreach ($table_field as $field) {
            if (isset($attendance[$field])) {
                $data[$field] = $attendance[$field];
            }
        }
        unset($data['sid'], $data['sids']);

        if (($attendance['lesson_type'] == Lesson::LESSON_TYPE_ONE_TO_ONE)) {

            $data['sid'] = $attendance['sid']; /*学生ID(1对1课有效)*/
            $data['satt_id'] = $attendance['satt_id']; /*学生考勤记录ID(1对1课有效)*/

        } elseif (($attendance['lesson_type'] == Lesson::LESSON_TYPE_ONE_TO_MULTI)) {
            if (empty($model['sids'])) {
                $data['sids'] = $attendance['sid'];
            } else {
                $sids = explode(',', $model['sids']);
                array_push($sids, $attendance['sid']);
                $data['sids'] = join(',', $sids);
            }
        }

        /*获取student_attendance消耗的money*/
        $consume_money = $attendance['consume'];

        /*上课学生数，出勤的人数统计[student_attendance]*/
//        $data['student_nums']        = count($ca['student_attendance']);
        if (empty($model)) {
            if ($attendance['is_in']) {
                $data['student_nums'] = 1;
            } else {
                $data['student_nums'] = 0;
            }
            $data['total_lesson_amount'] = $consume_money;
        } else {
            if ($attendance['is_in']) {
                $data['student_nums'] = $model['student_nums'] + 1;
            } else {
                $data['student_nums'] = $model['student_nums'];
            }
            $data['total_lesson_amount'] = $model['total_lesson_amount'] + $consume_money;
        }

        $data['lesson_hours']       = $attendance['lesson']['unit_lesson_hours']; /*单次课课时数*/
        $data['lesson_minutes']     = $attendance['lesson']['unit_lesson_minutes']; /*课时长(小时数)*/
        $data['total_lesson_hours'] = $attendance['lesson']['unit_lesson_hours'] * $data['student_nums']; /*总计课时数*/

        if ($model) {
            $model->allowField(true)->isUpdate(true)->save($data);
        } else {
            $model = new self();
            $model->allowField(true)->isUpdate(false)->save($data);
        }
        return $model;
    }

    public static function rollbackConsume(StudentAttendance $attendance)
    {
        $w = [];
        $w['catt_id'] = $attendance['catt_id'];
        $model = self::get($w);
        if (empty($model)) {
            throw new Exception('resource [employee_lesson_hour] not found!catt_id' . $attendance['catt_id']);
        }

//        if (empty($attendance['is_consume'])) {
//            return false;
//        }
//        $w = [];
//        $w['eid'] = $attendance['eid'];
//        $w['ca_id']   = $attendance['ca_id'];
//        $w['int_day'] = $attendance['int_day'];
//        $w['int_start_hour'] = $attendance['int_start_hour'];
//        $w['int_end_hour']   = $attendance['int_end_hour'];
//        $model = self::get($w);
//        if (empty($model)) {
//            throw new Exception('resource not found!');
//        }

        $data = [];
        $student_nums = $model['student_nums'];
        $lesson_hours = $model['lesson_hours'];
        if ($attendance['is_in']) {
            $data['student_nums'] = $student_nums - 1;
        } else {
            $data['student_nums'] = $student_nums;
        }
//        $data['student_nums'] = $student_nums - 1;
        $data['total_lesson_hours']  = $lesson_hours * $data['student_nums'];
        if ($attendance['is_consume']) {
            if (!empty($attendance['student_lesson_hour'])) {
                $amount = $attendance['student_lesson_hour']['lesson_amount'];
            } else {
                throw new Exception('撤销学生考勤记录：计费考勤没有查询到学生的考勤对应的课耗记录！');
            }
            $data['total_lesson_amount'] = $model['total_lesson_amount'] - $amount;
        }
        $model->allowField(true)->save($data);
        return $model;
    }
}