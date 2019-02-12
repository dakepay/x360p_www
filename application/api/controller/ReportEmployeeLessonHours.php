<?php

namespace app\api\controller;

use app\api\model\EmployeeLessonHour;
use app\api\model\CourseArrange;
use app\api\model\CourseArrangeStudent;
use think\Request;

class ReportEmployeeLessonHours extends Base
{
    protected function get_lesson_num($start,$end,$teach_eid,$bid)
    {
        $model = new EmployeeLessonHour;
        $w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
        $w['eid'] = $teach_eid;
        $w['bid'] = $bid;
        return $model->where($w)->sum('total_lesson_hours');
    }

    protected function get_trial_num($start,$end,$teach_eid,$bid)
    {
        $model = new CourseArrange;
        $w['teach_eid'] = $teach_eid;
        $w['bid'] = $bid;
        $w['int_day'] = ['between',[date('Ymd',strtotime($start)),date('Ymd',strtotime($end))]];
        $ca_ids = $model->where($w)->column('ca_id');

        unset($w['bid']);
        unset($w['teach_eid']);
        $w['ca_id'] = ['in',$ca_ids];
        $w['is_trial'] = 1; 
        $w['is_attendance'] = 1;
        
        return (new CourseArrangeStudent)->where($w)->count();
    }

    /**
     * 按月统计教师产出
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        // $model = new EmployeeLessonHour;
        $model = new CourseArrange;

        $rule = [
            'start_date|开始日期' => 'require|date',
            'end_date|结束日期'   => 'require|date',
        ];
        $ret = $this->validate($input, $rule);
        if ($ret === false) {
            return $this->sendError(400, $rs);
        }
        $w = [];
        if(!empty($input['start_date'])){
            $w['int_day'] = ['between',[date('Ymd',strtotime($input['start_date'])),date('Ymd',strtotime($input['end_date']))]];
        }
        if(isset($input['eid'])){
            $w['teach_eid'] = $input['eid'];
        }
 
        $group = 'teach_eid';
        $fields = ['teach_eid','bid'];
        $data = $model->where($w)->field($fields)->group($group)->order('teach_eid asc')->getSearchResult($input);

        $start = strtotime($input['start_date']);
        $end = strtotime($input['end_date']);
        $mode = 0;
        $week_section = get_week_section($start,$end,$mode);

        $data['week_section'] = $week_section;

        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['lesson_nums'] = $this->get_lesson_num($input['start_date'],$input['end_date'],$v['teach_eid'],$v['bid']);
            $data['list'][$k]['trial_nums'] = $this->get_trial_num($input['start_date'],$input['end_date'],$v['teach_eid'],$v['bid']);
            $data['list'][$k]['total_nums'] = $data['list'][$k]['lesson_nums']+$data['list'][$k]['trial_nums'];
            $data['list'][$k]['eid'] = $data['list'][$k]['teach_eid'];

            foreach ($week_section as $k1 => $v1) {
                $data['list'][$k]['weeks'][$k1]['lesson_num'] = $this->get_lesson_num($v1['start'],$v1['end'],$v['teach_eid'],$v['bid']);
                $data['list'][$k]['weeks'][$k1]['trial_num'] = $this->get_trial_num($v1['start'],$v1['end'],$v['teach_eid'],$v['bid']);
                $data['list'][$k]['week_'.$k1.'_lesson_num'] = $this->get_lesson_num($v1['start'],$v1['end'],$v['teach_eid'],$v['bid']);
                $data['list'][$k]['week_'.$k1.'_trial_num'] = $this->get_trial_num($v1['start'],$v1['end'],$v['teach_eid'],$v['bid']);
            }


        }

        return $this->sendSuccess($data);
    }
}