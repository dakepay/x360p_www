<?php
/**
 * Author: luo
 * Time: 2018/5/25 17:04
 */

namespace app\api\controller;


use app\api\model\Customer;
use app\api\model\LessonBuySuit;
use think\Request;

class LessonBuySuits extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_lbs = new LessonBuySuit();
        $ret = $m_lbs->getSearchResult($get);

        if(empty($ret['list']) && isset($get['sid'])) {
            $customer = (new Customer())->where('sid', $get['sid'])->field('cu_id')->find();
            if(!empty($customer)) {
                unset($get['sid']);
                $get['cu_id'] = $customer['cu_id'];
                $ret = $m_lbs->getSearchResult($get);
            }
        }

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();

        $m_lbs = new LessonBuySuit();
        $lbs_id = $m_lbs->addLessonBuySuit($post);
        if($lbs_id === false) return $this->sendError(400, $m_lbs->getErrorMsg());
        
        return $this->sendSuccess($lbs_id);
    }

    public function delete(Request $request)
    {
        return parent::delete($request);
    }

}