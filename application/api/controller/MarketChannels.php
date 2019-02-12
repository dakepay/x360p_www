<?php
/**
 * Author: luo
 * Time: 2018/4/20 11:15
 */

namespace app\api\controller;


use app\api\model\MarketChannel;
use app\api\model\MarketClue;
use think\Request;

class MarketChannels extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_mc = new MarketChannel();
        if(!empty($get['create_uid'])) {
            $m_mc->where('is_share = 1 or create_uid = ' . $get['create_uid']);
            unset($get['create_uid']);
        }

        $ret = $m_mc->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        return parent::post($request);
    }

    public function put(Request $request)
    {
        return parent::put($request);
    }



    public function delete(Request $request)
    {
        $mc_id = input('id');
        $clue = MarketClue::get(['mc_id' => $mc_id]);
        if(!empty($clue)) return $this->sendError(400, '渠道有相关的名单不能删除');

        $channel = MarketChannel::get($mc_id);
        $rs = $channel->delete();
        if($rs === false) return $this->sendError(400, $channel->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * 合并渠道
     * @param Request $request
     */
    public function do_merge(Request $request)
    {
        $input = input();

        if(empty($input['mc_id'])) return $this->sendError(400, 'mc_id not exits');
        if(empty($input['mc_ids'])) return $this->sendError(400, 'mc_ids not exits');
        $mMarketClue = new MarketChannel();
        $rs = $mMarketClue->mergeChannel($input['mc_id'],$input['mc_ids']);
        if($rs === false) return $this->sendError(400, $mMarketClue->getError());

        return $this->sendSuccess();
    }

}