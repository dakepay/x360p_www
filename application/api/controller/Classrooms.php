<?php
/**
 * luo
 */
namespace app\api\controller;

use app\api\model\Classroom as ClassroomModel;
use app\api\model\CourseArrange;
use DateInterval;
use DatePeriod;
use DateTime;
use think\Request;

class Classrooms extends Base
{

    /**
     * @desc  获取教室的排课
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_course(Request $request)
    {
        $mode = input('mode/d', 1);
        $apn = input('apn','0,1,2');
        $date = input('date', date('Y-m-d', time()));
        $cr_id = input('cr_id');

        //获取某天一周的开始与结束
        $year = date('Y', time());
        $week = date('W', strtotime($date));
        $week_time_arr = weekday($year, $week);
        $start_day = date('Ymd', $week_time_arr['start']);
        $end_day = date('Ymd', $week_time_arr['end']);
        $start_day_obj = new DateTime($start_day);
        $end_day_obj = new DateTime(($end_day + 1));
        $interval = new DateInterval('P1D');
        $range = new DatePeriod($start_day_obj, $interval, $end_day_obj);

        $m_ca = new CourseArrange();
        $cr_id_arr = explode(',', $cr_id);
        $list = $m_ca->with('oneClass')->where('cr_id', 'in', $cr_id_arr)
            ->where('int_day', 'between', [$start_day, $end_day])->select();
        $data = [];
        if($mode === 1) {   #  星期为横轴
            foreach($cr_id_arr as $per_cr_id) {
                $classroom = ClassroomModel::get($per_cr_id);
                if(empty($classroom)) continue;
                $tmp = [];

                foreach($range as $dt) {
                    $day = $dt->format('Ymd');
                    $tmp['AM'][$day]['list'] = [];
                    $tmp['PM'][$day]['list'] = [];
                    $tmp['NM'][$day]['list'] = [];
                    foreach($list as $row) {
                        if($row['int_day'] == $day && $row['cr_id'] == $per_cr_id) {
                            $int_start_hour = $row->getData('int_start_hour');
                            if(600 <= $int_start_hour && $int_start_hour <= 1200) {
                                $tmp['AM'][$day]['list'][] = $row;
                            } elseif(1200 < $int_start_hour && $int_start_hour <= 1800) {
                                $tmp['PM'][$day]['list'][] = $row;
                            } else {
                                $tmp['NM'][$day]['list'][] = $row;
                            }
                        }
                    }
                }
                $tmp_list = [];
                $tmp_list['AM'] = array_values($tmp['AM']);
                $tmp_list['PM'] = array_values($tmp['PM']);
                $tmp_list['NM'] = array_values($tmp['NM']);
                $tmp_list['room_name'] = $classroom['room_name'];
                $data[] = $tmp_list;
            }

        } else {    # 星期为纵轴
            foreach($cr_id_arr as $per_cr_id) {
                $tmp = [];
                $classroom = ClassroomModel::get($per_cr_id);
                if(empty($classroom)) continue;

                foreach($range as $dt) {
                    $day = $dt->format('Ymd');
                    $tmp[$day]['AM'] = [];
                    $tmp[$day]['PM'] = [];
                    $tmp[$day]['NM'] = [];
                    foreach($list as $row) {
                        if($row['int_day'] == $day && $row['cr_id'] == $per_cr_id) {
                            $int_start_hour = $row->getData('int_start_hour');
                            if(600 <= $int_start_hour && $int_start_hour <= 1200) {
                                $tmp[$day]['AM'][] = $row;
                            } elseif(1200 < $int_start_hour && $int_start_hour <= 1800) {
                                $tmp[$day]['PM'][] = $row;
                            } else {
                                $tmp[$day]['NM'][] = $row;
                            }
                        }
                    }
                }
                $tmp['room_name'] = $classroom['room_name'];
                $data[] = $tmp;
            }

        }

        return $this->sendSuccess($data);
    }

}