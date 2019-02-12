<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/9/6
 * Time: 12:16
 */
namespace app\api\export;

use app\common\Export;
use think\Log;

class EventStudents extends Export
{
    protected $columns = [
        ['title'=>'学生姓名','field'=>'student_name','width'=>20],
        ['title'=>'学生性别','field'=>'sex','width'=>20],
        ['title'=>'报名时间','field'=>'join_time','width'=>20],
        ['title'=>'手机号码','field'=>'first_tel','width'=>20],
    ];

    protected $event;

    protected function __init()
    {
        $event_id = $this->params['event_id'];
        if (empty($event_id) || !is_numeric($event_id)) {
            throw new \InvalidArgumentException('缺少参数event_id或参数不合法');
        }
        $this->event = m('Event')->findOrFail($event_id);
    }

    protected function get_title(){
        $event_name = $this->event['event_name'];
        $title = $event_name . '活动报名学员名册';
        return $title;
    }

    public function get_data()
    {
        $students = $this->event['students'];
        if (empty($students)) {
            return [];
        }
        $data = [];
        foreach ($students as $student) {
            Log::record('-------------------------------');
            Log::record($student['student_name']);
            $temp['student_name'] = $student['student_name'];
            $temp['sex'] = get_sex($student['sex']);
            $temp['join_time'] = $student['pivot']['create_time'];
            $temp['first_tel'] = $student['first_tel'];
            $data[] = $temp;
        }
        return $data;
    }
}