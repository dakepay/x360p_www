<?php
/**
 * Author: luo
 * Time: 2018/3/22 19:32
 */

namespace app\sapi\controller;


use app\sapi\model\ClassStudent;
use app\sapi\model\HomeworkComplete;
use app\sapi\model\HomeworkPublish;
use app\sapi\model\HomeworkTask;
use think\Request;

class HomeworkCompletes extends Base
{

    public function get_list(Request $request)
    {
        $sid = global_sid();

        $get = $request->get();
        $m_hc = new HomeworkComplete();
        $with = ['homeworkTask.employee'];
        if(isset($get['width'])){
            $input_with = explode(',',$get['with']);
            $with = array_merge($with,$input_with);
        }
        $ret = $m_hc->where('sid', $sid)->with($with)->getSearchResult($get);
        if(!empty($ret['list'])){
            foreach($ret['list'] as $k=>$r){
                if($r['cid'] > 0){
                    $ret['list'][$k]['class_name'] = get_class_name($r['cid']);
                }else{
                    $ret['list'][$k]['class_name'] = '';
                }
            }
        }

        return $this->sendSuccess($ret);
    }

    public function student_status()
    {
        $sid = global_sid();
        if($sid <= 0) return $this->sendError(400, '学生id错误');
        $input_start_day = input('start_day', 'monday this week');
        $input_end_day = input('end_day', 'sunday this week');
        $time_start = strtotime($input_start_day);
        $time_end = strtotime($input_end_day);

        $m_cs = new ClassStudent();
        $cids = $m_cs->where('sid', $sid)->where('status', ClassStudent::STATUS_NORMAL)->column('cid');

        $m_ht = new HomeworkTask();
        if(!empty($cids)) {
            $where = sprintf('cid in (%s) or sid = %s or find_in_set(%s, sids)', implode(',', $cids), $sid, $sid);
        } else {
            $where = sprintf('sid = %s or find_in_set(%s, sids)', $sid, $sid);
        }

        $homework_num = $m_ht->where($where)->where('create_time', 'between', [$time_start, $time_end])
            ->count();

        $m_hc = new HomeworkComplete();
        $complete_num = $m_hc->where('sid', $sid)->where('create_time', 'between', [$time_start, $time_end])->count();

        $m_hp = new HomeworkPublish();
        $publish_num = $m_hp->where('sid', $sid)->where('create_time', 'between', [$time_start, $time_end])->count();

        $complete_list = $m_hc->where('sid', $sid)->where('create_time', 'between', [$time_start, $time_end])
            ->order('hc_id', 'asc')->field('bid,lid,sid,star,is_check,check_level,result_level')->select();

        $data = [
            'homework_num' => $homework_num,
            'complete_num' => $complete_num,
            'publish_num' => $publish_num,
            'complete_list' => $complete_list
        ];

        return $this->sendSuccess($data);
    }

    /**
     * 删除提交作业
     */
    public function delete(Request $request){
        $hc_id = input('id/d');
        $sid = global_sid();

        $mHomeworkComplete = new HomeworkComplete();
        $rs = $mHomeworkComplete->delHomework($hc_id,$sid);
        if($rs === false) return $this->sendError(400, $mHomeworkComplete->getError());
        return $this->sendSuccess();
    }

}