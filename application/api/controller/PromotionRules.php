<?php
namespace app\api\controller;

use app\api\model\PromotionRule;
use think\Request;
class PromotionRules extends Base
{

    /**
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_list(Request $request)
    {
        $input = input();

        $mPromotionRule = new PromotionRule();
        $result = $mPromotionRule->scope('suit_bids,suit_lids,suit_sj_ids')->getSearchResult($input);

        return $this->sendSuccess($result);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_detail(Request $request, $id = 0)
    {
        $pr_id = input('id/d');
        $mPromotionRule = new PromotionRule();
        $result = $mPromotionRule->where('pr_id',$pr_id)->find();

        return $this->sendSuccess($result);
    }

    /**
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function post(Request $request)
    {
        $post = $request->post();

        $mPromotionRule = new PromotionRule();
        $result = $mPromotionRule->addPromotionRule($post);
        if (false === $result){
            return $this->sendError(400,$mPromotionRule->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function put(Request $request)
    {
        $pr_id = input('pr_id/d');
        $put = $request->put();
        unset($put['pr_id']);

        $mPromotionRule = new PromotionRule();
        $result = $mPromotionRule->updatePromotionRule($pr_id,$put);
        if (false === $result){
            return $this->sendError(400,$mPromotionRule->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function delete(Request $request)
    {
        $pr_id = input('id/d');

        $mPromotionRule = new PromotionRule();
        $result = $mPromotionRule->delPromotionRule($pr_id);
        if (false === $result){
            return $this->sendError(400,$mPromotionRule->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * 修改促销状态
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function psot_status(Request $request)
    {
        $pr_id = input('id/d');
        $status = input('status/d');

        $mPromotionRule = new PromotionRule();
        $result = $mPromotionRule->updatePromotionStatus($pr_id,$status);
        if (false === $result){
            return $this->sendError(400,$mPromotionRule->getError());
        }

        return $this->sendSuccess();
    }



}