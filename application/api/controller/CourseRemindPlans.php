<?php
namespace app\api\controller;
use think\Request;
use app\api\model\CourseRemindPlan;


class CourseRemindPlans extends Base
{
    /**
     * 读本校区的配置
     */
    public function get_list_configs(Request $request){
        $ret = [
            'day0_push'=>0,
            'day1_push'=>0,
            'day2_push'=>0,
            'day3_push'=>0,
            'day0_push_int_hour'    => 800,
            'dayn_push_int_hour'    => 2000
        ];

        $mCrp = new CourseRemindPlan();
        $bid = auto_bid();
        $w['bid'] = $bid;
        $crp_list = $mCrp->where($w)->select();

        if(!$crp_list){
            return $this->sendSuccess($ret);
        }

        return $this->sendSuccess($crp_list[0]);
    }

    /**
     * 写本校区的配置
     */
    public function post_configs(){
        $input = input('post.');
        $mCrp = new CourseRemindPlan();
        $og_id = gvar('og_id');
        $bid   = auto_bid();

        $result = $mCrp->setAutoPushRemindCourseTask($og_id, $bid,$input);
        if(false === $result){
            return $this->sendError('设置自动推送计划失败!'.$mCrp->getError());
        }

        return $this->sendSuccess();
    }

}