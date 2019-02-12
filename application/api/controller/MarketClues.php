<?php
/**
 * Author: luo
 * Time: 2018/4/20 14:57
 */

namespace app\api\controller;


use app\api\model\MarketChannel;
use app\api\model\MarketClue;
use app\api\model\MarketClueLog;
use app\api\model\CustomerFollowUp;
use think\Request;
use app\api\model\WebcallCallLog;

class MarketClues extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $mMarketClue = new MarketClue();

        if(isset($input['create_eid'])){
            $get['create_uid'] = Employee::getUidByEid($input['create_eid']);
            unset($input['create_eid']);
        }
        $w = [];
        if(isset($input['age_start']) && isset($input['age_end']) && $input['age_start'] == $input['age_end']){
            $months = age_to_months($input['age_start']);

            $age_time_start = strtotime("-$months months");
            $age_time_end   = strtotime("+1 month",$age_time_start) - 1;

            $w['birth_time'] = ['BETWEEN',[$age_time_start,$age_time_end]];
            unset($input['age_start']);
        }else{
            if(isset($input['age_start'])) {
                $months = age_to_months($input['age_start']);
                $age_start_time = strtotime("-$months months");
                $w['birth_time'] = ['elt', $age_start_time];
                unset($input['age_start']);
            }
            if(isset($input['age_end'])) {
                $months = age_to_months($input['age_end']);
                $age_end_time = strtotime("-$months months");
                $w['birth_time'] =  ['egt', $age_end_time];
                unset($input['age_end']);
            }
        }
        $ret = $mMarketClue->with('market_channel')->getSearchResult($input);
        foreach($ret['list'] as &$row) {
            if(!empty($row['employee']) && $row['employee']['uid'] == 0) {
                $row['employee'] = null;
            }
            $row['channel_name'] = get_mc_name($row['mc_id']);
            $row['from_did_name'] = get_did_value($row['from_did']);
        }
        return $this->sendSuccess($ret);
    }

    /**
     * 市场名单 查看 转化 客户信息
     * method get: api/market_clues/1/customer
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list_customer(Request $request)
    {
        /*$mcl_id = input('id/d');
        $market = MarketClue::get($mcl_id);
        $cu_id = $market->cu_id;*/
        $cu_id = input('id/d');
        if(!$cu_id){
            return $this->sendError(400,'param error');
        }
        $model = new MarketClue;
        $ret = $model->where('cu_id',$cu_id)->find();
        $m_cfu = new CustomerFollowUp();
        $customer_follow_up = $m_cfu->where('cu_id',$cu_id)->select();
        if(!empty($customer_follow_up)){
            $ret['customer_follow_up'] = $customer_follow_up;
        }

        return $this->sendSuccess($ret);

    }


    /**
     * @desc  添加
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $m_mc = new MarketClue();
        $rs = $m_mc->addClue($post);
        if($rs === false) return $this->sendError(400, $m_mc->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $id = input('id');
        $put = $request->put();

        $clue = MarketClue::get($id);
        $mc_id = $clue['mc_id'];

        if(empty($clue)) return $this->sendError(400, '市场名单不存在');

        // 确认有效性时  相对应的市场渠道有效性人数+1
        if($clue['is_valid'] == 0 && isset($put['is_valid']) && $put['is_valid'] == 1){
            (new MarketChannel)->where('mc_id',$clue['mc_id'])->setInc('valid_num');
        }
        
        if (isset($put['token'])){
            $mWbl = new WebcallCallLog();
            $rs = $mWbl->addRelateCmtId($put['token'],$clue['mcl_id']);
            if (!$rs) $this->sendError(400, $mWbl->getErrorMsg());
        }

        $old_data = get_mcl_info($id);
        $content = get_array_diff_value($old_data,$put);
        // 添加一条市场名单编辑日志
        MarketClueLog::addMarketClueEditLog($id,$content);

        $result = $clue->isUpdate(true)->allowField(true)->save($put);

        if(false === $result){
            return $this->sendError(400, $clue->getError());
        }

        if(isset($put['mc_id']) && $put['mc_id'] != $mc_id){
            // 更新市场渠道人数
            MarketClue::UpdateNumOfChannel($mc_id);
            MarketClue::UpdateNumOfChannel($put['mc_id']);
        }
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $mcl_id = input('id');
        $m_mc = new MarketClue();
        $rs = $m_mc->delOneClue($mcl_id);
        if($rs === false) return $this->sendError(400, $m_mc->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete_batch(Request $request)
    {
        $post = $request->post();
        $mcl_ids = $post['mcl_ids'] ?? [];
        if(empty($mcl_ids)) return $this->sendError(400, 'mcl_ids error');

        $m_mc = new MarketClue();
        $mcl_ids = is_string($mcl_ids) ? explode(',', $mcl_ids) : $mcl_ids;
        $rs = $m_mc->multiDel($mcl_ids);
        if($rs === false) return $this->sendError(400, $m_mc->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**删除一个时间段市场名单
     * @param Request $request
     */
    public function delete_condition(Request $request)
    {
        $post = $request->post();

        $is_force_del = input('force/d', 0);

        $bid = isset($post['bid']) ? $post['bid'] : 0;
        $mc_id = isset($post['mc_id']) ? $post['mc_id'] : 0;
        $get_start_time = isset($post['get_start_time']) ? strtotime($post['get_start_time']) : 0;
        $get_end_time = isset($post['get_end_time']) ? str_to_time($post['get_end_time'],true) : 0;

        $create_start_time = isset($post['create_start_time']) ? strtotime($post['create_start_time']) : 0;
        $create_end_time = isset($post['create_end_time']) ? str_to_time($post['create_end_time'],true) : 0;

        $mMarktClue = new MarketClue();
        $result = $mMarktClue->deleteCondition($bid,$mc_id,$get_start_time,$get_end_time,$create_start_time,$create_end_time,$is_force_del);
        if($result === false){
            if($mMarktClue->get_error_code() == $mMarktClue::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($mMarktClue->getError());
            }
            return $this->sendError(400, $mMarktClue->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  分配员工
     * @author luo
     * @method POST
     */
    public function assign(Request $request)
    {
        $post = $request->post();

        if(empty($post['mcl_ids'])) return $this->sendError(400, 'mcl_ids error');

        $assign_type = isset($post['assign_type'])?intval($post['assign_type']):2;     //1:市场，2:销售
        $assign_scope = isset($post['assign_scope'])?intval($post['assign_scope']):1;   //1:明确到人,2:只分配到校区

        $to_bid = isset($post['to_bid'])?intval($post['to_bid']):0; //分配都校区ID

        $eid = intval($post['eid']);

        if($assign_scope == 1){
            $to_bid = 0;
            if(!$eid){
                return $this->sendError('请选择分配跟进人!');
            }
        }else{
            $eid = 0;
            if(!$to_bid){
                return $this->sendError(400,'请选择分配到校区!');
            }
            $branch_info = get_branch_info($to_bid);
            if(!$branch_info){
                return $this->sendError(400,'校区ID不存在!');
            }
        }

        $mcl_ids = is_string($post['mcl_ids']) ? explode(',', $post['mcl_ids']) : $post['mcl_ids'];

        $m_mc = new MarketClue();

        $rs = $m_mc->multiAssignEmployee($mcl_ids, $eid,$assign_type,$to_bid);

        if($rs === false){
            return $this->sendError(400, $m_mc->getErrorMsg());
        }
        
        return $this->sendSuccess();
    }

    /**
     * @desc  使名单生效
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function change_to_valid(Request $request)
    {
        $post = $request->post();
        $m_mc = new MarketClue();
        $rs = $m_mc->multiChangeToValid($post);
        if($rs === false) return $this->sendError(400, $m_mc->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  成为客户
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function change_to_customer(Request $request)
    {
        $post = $request->post();
        $m_mc = new MarketClue();
        $rs = $m_mc->multiChangeToCustomer($post);
        if($rs === false) return $this->sendError(400, $m_mc->getErrorMsg());

        return $this->sendSuccess();
    }

}