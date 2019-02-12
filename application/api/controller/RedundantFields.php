<?php
/**
 * Author: luo
 * Time: 2018/1/18 10:37
 */

namespace app\api\controller;


use app\api\model\CourseArrangeStudent;
use app\api\model\Lesson;
use app\api\model\Order;
use app\api\model\OrderItem;
use app\api\model\OrderRefundItem;
use app\api\model\OrderTransferItem;
use app\api\model\Student;
use app\api\model\StudentLesson;
use app\api\model\StudentLessonHour;
use think\Exception;
use think\Log;
use think\Request;

class RedundantFields extends Base
{

    private $redis = null;

    private $update_fields = [
        'lesson_hours'      => '课时缓存字段',
        'remain_arrange_times' => '学生购买的课程未排课次数'
    ];
    private $redis_key;
    private $start = 0; # 更新开始的位置
    private $offset = 30; # 更新位移

    /**
     * @desc  更新数据库冗余数据
     * @author luo
     * @param Request $request
     * @method PUT
     */
    public function put(Request $request)
    {
        $client = gvar('client');
        if(empty($this->update_fields)) return $this->sendSuccess($this->setReturnInfo('程序没设定要更新的数据'));
        if(is_null(gvar('og_id'))) return $this->sendError($this->setReturnInfo('机构id信息错误'));

        $cid = $client['cid'];
        $og_id = gvar('og_id');

        //redis相关数据，分多次更新
        $redis_key = sprintf('upcache-%s-%s',$cid,$og_id);
        $this->redis_key = $redis_key;
        $this->redis = $this->redis ? $this->redis : redis();
        $this->start = $this->redis->hGet($redis_key, 'start') ? $this->redis->hGet($redis_key, 'start') : $this->start;
        $field = $this->redis->hGet($redis_key, 'field');

        if($this->redis->hGet($redis_key, 'done')) return $this->sendSuccess($this->setReturnInfo('所有更新完成'));

        if(!empty($field)) {
            //从redis中继续上一次的更新
            $method = 'update_' . $field;
        } else {
            //如果是第一次，则从头开始
            $first_field_row = array_slice($this->update_fields,0, 1, true);
            $field = array_keys($first_field_row)[0];
            $method = 'update_' . $field;
        }

        $rs = method_exists($this, $method);
        if(!$rs) return $this->sendSuccess($this->setReturnInfo('没有需要更新的信息'));

        $rs = $this->$method();
        return $this->sendSuccess($this->setReturnInfo($rs, 0));
    }

    //返回的数据格式，status: 0前端继续，1前端终止
    private function setReturnInfo($msg, $status = 1)
    {
        $info = [
            'status' => $status,
            'msg' => $msg,
        ];
        return $info;
    }

    private function update_lesson_hours(){
        $sql = <<<EOF
update `x360p_student_lesson` sl
left join (
  select sl_id,sum(lesson_hours) as use_lesson_hours
  from `x360p_student_lesson_hour`
  where is_delete = 0 group by sl_id
) c
ON sl.sl_id = c.sl_id
set
sl.use_lesson_hours = c.use_lesson_hours,
sl.remain_lesson_hours = sl.lesson_hours - sl.transfer_lesson_hours - sl.refund_lesson_hours - c.use_lesson_hours
where sl.use_lesson_hours <> c.use_lesson_hours
;
EOF;
        db()->execute($sql);

        $sql = <<<EOF
update `x360p_student` s
LEFT JOIN (
  select sid,
        sum(lesson_hours) as lesson_hours,
        sum(transfer_lesson_hours) as transfer_lesson_hours,
        sum(refund_lesson_hours) as refund_lesson_hours,
        sum(remain_lesson_hours) as remain_lesson_hours
  from `x360p_student_lesson`
  where is_delete = 0 and lesson_status < 2
  group by sid
) c
ON s.sid = c.sid
set
s.student_lesson_remain_hours = c.remain_lesson_hours,
s.student_lesson_hours = c.lesson_hours - c.transfer_lesson_hours - c.refund_lesson_hours
where s.student_lesson_remain_hours <> c.remain_lesson_hours
;
EOF;
        db()->execute($sql);
        $this->set_redis_next_update('student_lesson', 0, 0);
        return '课时缓存更新成功!';
    }

    private function update_remain_arrange_times()
    {
        $field = str_replace('update_','', __FUNCTION__);
        $start = $this->start;
        $end = $start + $this->offset;

        $m_sl = new StudentLesson();
        $where = sprintf('(lesson_type  = %s or lesson_type = %s) and sl_id >= %d and sl_id < %d',
            Lesson::LESSON_TYPE_ONE_TO_ONE, Lesson::LESSON_TYPE_ONE_TO_MULTI, $start, $end);
        //$total = $m_sl->count();
        $total = ($m_sl->order('sl_id desc')->field('sl_id')->find())['sl_id'];
        $list = $m_sl->where($where)->select();
        $m_cas = new CourseArrangeStudent();
        foreach ($list as $student_lesson) {
            if($student_lesson['lid'] <= 0) continue;
            $list = $m_cas->where('sid', $student_lesson['sid'])->where('is_trial = 0')->where('lid', $student_lesson['lid'])->select();
            if(empty($list)) continue;
            $arranged_hours = 0;
            foreach($list as $obj) {
                $arranged_hours += $obj->lesson_hour;
            }

            $remain_arrange_hours = $student_lesson->lesson_hours - $arranged_hours;
            $student_lesson->remain_arrange_hours = $remain_arrange_hours <= 0 ? 0 : $remain_arrange_hours;
            //$total_arrange_times = $m_cas->where('sid', $student_lesson['sid'])->where('lid', $student_lesson['lid'])
            //    ->count();
            //$student_lesson->remain_arrange_times = $student_lesson->lesson_times - $total_arrange_times;
            $student_lesson->save();
        }

        $this->set_redis_next_update($field, $end, $total);
        return sprintf('%s更新成功，从第%s条到第%s条...', $this->update_fields[$field], $start, $end);
    }



    //设置下次更新的信息与位置
    private function set_redis_next_update($cur_field, $cur_end, $total)
    {
        $start = $cur_end;

        $done = 0;
        if($cur_end >= $total) {
            $keys = array_keys($this->update_fields);
            $i = array_search($cur_field, $keys);
            if($i >= count($keys) - 1) {
                $done = 1;
                $field = $cur_field;
            } else {
                //更新下一字段，从0开始
                $field = $keys[$i + 1];
                $start = 0;
            }
        } else {
            $field = $cur_field;
        }
        $this->redis->hSet($this->redis_key, 'field', $field);
        $this->redis->hSet($this->redis_key, 'start', $start);
        $this->redis->hSet($this->redis_key, 'done', $done);
    }


}