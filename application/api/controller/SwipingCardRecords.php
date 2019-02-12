<?php
/**
 * Author: luo
 * Time: 2017-10-24 15:42
**/

namespace app\api\controller;

use \think\Request;

class SwipingCardRecords extends Base
{
	/**
     * 学生刷卡
     * 离校刷卡、到校刷卡、刷卡考勤、午晚托签到
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function post(Request $request)
    {
        $card_no = $request->param('card_no');

        $mScr = new \app\api\model\SwipingCardRecord();
      	$result  = $mScr->swipeCard($card_no);
      	if(!$result){
      		return $this->sendError(400,$mScr->getError());
      	}
      	return $this->sendSuccess($result);
    }
}