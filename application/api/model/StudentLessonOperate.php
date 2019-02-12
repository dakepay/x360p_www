<?php
/**
 * Author: luo
 * Time: 2018/6/4 12:07
 */

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class StudentLessonOperate extends Base
{
    const OP_TYPE_SEND = 1; # 手工赠送
    const OP_TYPE_TRANSFER = 2; # 结转
    const OP_TYPE_REFUND = 3; # 退款
    const OP_TYPE_ORDER = 4; # 跟随订单赠送
    const OP_TYPE_UNTRANSFER = 5; # 撤销结转
    const OP_TYPE_UPGRADE = 6; # 课程升级赠送


    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

    public function createEmployee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('eid,ename,uid,mobile');
    }

    public function studentLesson()
    {
        return $this->hasOne('StudentLesson', 'sl_id', 'sl_id');
    }

    public function addOperation($post, $update_student_lesson_table = false)
    {
        $rule = [
            'sl_id' => 'require',
            'lesson_hours' => 'require|gt:0',
            'op_type' => 'require',
        ];

        $validate = new \think\Validate($rule);
        $rs = $validate->check($post);
        if($rs !== true) return $this->user_error($validate->getError());

        $m_sl = new StudentLesson();
        $student_lesson = $m_sl->where('sl_id', $post['sl_id'])->find();
        if(empty($student_lesson)) return $this->user_error('sl_id不存在');
        array_copy($post, $student_lesson,['bid','sid']);
        $sid = $student_lesson['sid'];
        try {
            $this->startTrans();

            $rs = $this->allowField(true)->isUpdate(false)->data([])->save($post);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            if($post['op_type'] == self::OP_TYPE_SEND && $update_student_lesson_table) {
                $student_lesson->present_lesson_hours = $student_lesson->present_lesson_hours + $post['lesson_hours'];
                $student_lesson->lesson_hours = $student_lesson->lesson_hours + $post['lesson_hours'];
                $student_lesson->remain_lesson_hours = $student_lesson->remain_lesson_hours + $post['lesson_hours'];
                $student_lesson->remain_arrange_hours = $student_lesson->remain_arrange_hours + $post['lesson_hours'];
                $rs = $student_lesson->allowField('present_lesson_hours,lesson_hours,remain_lesson_hours,remain_arrange_hours')->save();
                if($rs === false) throw new FailResult($student_lesson->getError());
            }

            (new Student())->updateLessonHours($sid);

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    //结转导入的课时
    public function transferImportedLessonHours($post)
    {
        $rule = [
            'sl_id' => 'require',
            'lesson_hours' => 'require|gt:0',
            'op_type' => 'require',
            'unit_price' => 'require|gt:0',
        ];

        $validate = new \think\Validate($rule);
        $rs = $validate->check($post);
        if($rs !== true) return $this->user_error($validate->getError());

        $m_sl = new StudentLesson();
        $student_lesson = $m_sl->where('sl_id', $post['sl_id'])->find();
        if(empty($student_lesson) || $student_lesson['import_lesson_hours'] <= 0) {
            return $this->user_error('导入课时小于等于0，无法结转');
        }
        if($post['lesson_hours'] > $student_lesson['import_lesson_hours']) return $this->user_error('结转课时大于导入的课时');

        $post['lesson_amount'] = $post['lesson_hours'] * $post['unit_price'];
        array_copy($post, $student_lesson,['bid','sid']);

        try {
            $this->startTrans();

            $rs = $this->allowField(true)->isUpdate(false)->data([])->save($post);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $student_lesson->import_lesson_hours = $student_lesson->import_lesson_hours - $post['lesson_hours'];
            $student_lesson->lesson_hours = $student_lesson->lesson_hours - $post['lesson_hours'];
            $student_lesson->remain_lesson_hours = $student_lesson->remain_lesson_hours - $post['lesson_hours'];
            $student_lesson->remain_arrange_hours = $student_lesson->remain_arrange_hours - $post['lesson_hours'];
            $rs = $student_lesson->allowField('import_lesson_hours,lesson_hours,remain_lesson_hours,remain_arrange_hours')->save();
            if($rs === false) throw new FailResult($student_lesson->getError());

            if($post['lesson_amount'] > 0) {
                $money_data = [
                    'money' => $post['lesson_amount'],
                    'business_type' => StudentMoneyHistory::BUSINESS_TYPE_TRANSFORM,
                    'remark' => '导入课时结转',
                ];
                (new Student())->changeMoney(Student::get($post['sid']), $money_data);
            }

            (new Student())->updateLessonHours($post['sid']);

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function delOperation($slo_id)
    {
        $m_slo = $this->find(['slo_id' => $slo_id]);
        if (empty($m_slo)){
            return $this->user_error('赠送记录不存在');
        }

        $m_sl = new StudentLesson();
        $m_student_lesson = $m_sl->where('sl_id', $m_slo['sl_id'])->find();
        if(empty($m_student_lesson)) return $this->user_error('sl_id不存在');

        $this->startTrans();
        try {
            if($m_slo['op_type'] == self::OP_TYPE_SEND) {
                $m_student_lesson->present_lesson_hours = $m_student_lesson->present_lesson_hours - $m_slo['lesson_hours'];
                $m_student_lesson->lesson_hours = $m_student_lesson->lesson_hours - $m_slo['lesson_hours'];
                $m_student_lesson->remain_lesson_hours = $m_student_lesson->remain_lesson_hours - $m_slo['lesson_hours'];
                $m_student_lesson->remain_arrange_hours = $m_student_lesson->remain_arrange_hours - $m_slo['lesson_hours'];
                $rs = $m_student_lesson->allowField('present_lesson_hours,lesson_hours,remain_lesson_hours,remain_arrange_hours')->save();
                if($rs === false){
                    return $this->sql_save_error('student_lesson');
                }
            }
            $m_slo->delete();

            (new Student())->updateLessonHours($m_student_lesson['sid']);
        } catch (Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }


}