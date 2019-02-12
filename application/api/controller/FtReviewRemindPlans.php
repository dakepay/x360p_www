<?php
namespace app\api\controller;
use think\Request;
use app\api\model\FtReviewRemindPlan;

class FtReviewRemindPlans extends Base
{
    /**
     * 读本校区的配置
     */
    public function get_list_configs(Request $request){

        $mCrp = new FtReviewRemindPlan();
        $bid = auto_bid();
        $w['bid'] = $bid;
        $crp_list = $mCrp->where($w)->with('employee')->getSearchResult();

        return $this->sendSuccess($crp_list);
    }

    /**
     * 写本校区的配置
     */
    public function post(Request $request){
        $input = input('post.');
        $mFrrp = new FtReviewRemindPlan();
        $og_id = gvar('og_id');
        $bid   = auto_bid();

        $result = $mFrrp->setAutoPushFtReview($og_id, $bid, $input);
        if(false === $result){
            return $this->sendError('设置自动推送计划失败!'.$mFrrp->getError());
        }

        return $this->sendSuccess();
    }

}