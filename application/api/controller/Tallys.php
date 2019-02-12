<?php
/**
 * Author: luo
 * Time: 2017-11-21 12:19
**/

namespace app\api\controller;

use app\api\model\InputTemplate;
use app\api\model\OrderPaymentHistory;
use app\api\model\OrderRefund;
use app\api\model\OrderRefundHistory;
use app\api\model\Tally;
use think\Request;
use app\api\model\Tally as TallyModel;

class Tallys extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();

        $model = new TallyModel();
        $ret = $model->getSearchResult($input);

        foreach($ret['list'] as &$row) {
            if($row['relate_id'] > 0) {
                if($row['type'] === Tally::TALLY_TYPE_INCOME) {
                    $row['order_payment_history'] =
                        OrderPaymentHistory::get(['oph_id' => $row['relate_id']],['order_receipt_bill.student']);
                }
                if($row['type'] === Tally::TALLY_TYPE_PAYOUT) {
                    $row['order_refund_history'] =
                        OrderRefundHistory::get(['orh_id' => $row['relate_id']],['order_refund.student']);
                }
            }
        }

        $payout_amount = $model->where('type', TallyModel::TALLY_TYPE_PAYOUT)->autoWhere($input)->sum('amount');
        $income_amount = $model->where('type', TallyModel::TALLY_TYPE_INCOME)->autoWhere($input)->sum('amount');

        $ret['payout_amount'] = $payout_amount;
        $ret['income_amount'] = $income_amount;

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $tally_file = isset($input['tally_file']) ? $input['tally_file'] : [];

        $m_tally = new TallyModel();
        if(isset($input['multi'])){
            $multi = $input['multi'];
            unset($input['multi']);
            $inputs = [];
            if(isset($multi['amount']) && $multi['amount'] > 0 && !empty($multi['items'])){
                $input['amount'] = $input['amount'] - $multi['amount'];
                if($input['amount'] < 0){
                    return $this->sendError(400,'分摊金额有误!');
                }else {
                    if ($input['amount'] > 0) {
                        array_push($inputs, $input);
                    }
                }
                foreach($multi['items'] as $item){
                    $input_item = array_merge($input,[
                        'amount'    => $item['amount'],
                        'int_day'   => $item['day'],
                        'remark'    => $input['remark'].'分摊'
                    ]);
                    array_push($inputs,$input_item);
                }

                if(!empty($inputs)){
                    array_push($inputs,$tally_file);
                    $result = $m_tally->batCreateTally($inputs);
                    if(false === $result){
                        return $this->sendError(400,$m_tally->getError());
                    }
                    return $this->sendSuccess($result);
                }
            }
        }

        $result = $m_tally->createOneTally($input,$tally_file);
        if(false === $result) return $this->sendError(400, $m_tally->getError());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $tid = input('id/d');
        $tally = Tally::get(['tid' => $tid]);
        if(empty($tally)) return $this->sendSuccess();
        if($tally['relate_id'] > 0) return $this->sendError(400, '无法撤销业务相关的流水账');

        $rs = $tally->delTally();
        if(!$rs) return $this->sendError(400, '删除失败');

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $tid = input('id');
        $tally = Tally::get($tid);
        if(empty($tally)) return $this->sendError(400, '流水不存在');

        $put = $request->put();

        $result = $tally->editTally($put);
        if(!$result){
            return $this->sendError($tally->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  收支汇总表
     * @author luo
     * @param Request $request
     */
    public function do_stats(Request $request)
    {
        $input = $request->param();
        if(!isset($input['group'])) return $this->sendError(400, '参数错误');
        $group = $input['group'];

        $m_tally = new TallyModel();
        $fields = $m_tally->getTableFields();
        if(!in_array($group, $fields)) return $this->sendError(400, '参数错误');

        $income_list = $m_tally->where('type', TallyModel::TALLY_TYPE_INCOME)->autoWhere($input)
            ->group($group)->field("$group, sum(amount) as income")->select();
        $payout_list = $m_tally->where('type', TallyModel::TALLY_TYPE_PAYOUT)->autoWhere($input)
            ->group($group)->field("$group, sum(amount) as payout")->select();

        $data = [];
        foreach($income_list as $per) {
            $per['payout'] = isset($per['payout']) ? $per['payout'] : 0;
            $per['income'] = isset($per['income']) ? $per['income'] : 0;
            $data[$per[$group]] = $per;
        }

        foreach($payout_list as $per) {
            $per['payout'] = isset($per['payout']) ? $per['payout'] : 0;
            if(isset($data[$per[$group]])) {
                $data[$per[$group]]['payout'] = $per['payout'];
            } else {
                $data[$per[$group]]['payout'] = $per['payout'];
                $data[$per[$group]]['income'] = 0;
                $data[$per[$group]][$group] = $per[$group];
            }
        }

        $data = array_values($data);

        return $this->sendSuccess($data);
    }

    /**
     * 移动端新收支汇总表
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function incomeandpayout(Request $request)
    {
        $input = $request->param();

        $w = [];
        
        $start_int_day = '19700101';
        $end_int_day = '99999999';
        if(isset($input['start_date'])){
            $start_int_day = format_int_day($input['start_date']);
            $end_int_day = format_int_day($input['end_date']);
        }
        $w['int_day'] = ['between',[$start_int_day,$end_int_day]];

        if(isset($input['bids'])){
            $bids = explode(',',$input['bids']);
            $w['bid'] = ['in',$bids];
        }

        $mTally = new TallyModel;

        $data = $mTally->where($w)->field('bid')->group('bid')->skipBid()->getSearchResult($input);

        foreach ($data['list'] as &$item) {
            $item['income'] = $mTally->skipBid()->where(['int_day'=>$w['int_day'],'bid'=>$item['bid'],'type'=>TallyModel::TALLY_TYPE_INCOME])->sum('amount');
            $item['payout'] = $mTally->skipBid()->where(['int_day'=>$w['int_day'],'bid'=>$item['bid'],'type'=>TallyModel::TALLY_TYPE_PAYOUT])->sum('amount');
        }
        $data['total_income'] = $mTally->skipBid()->where(['int_day'=>$w['int_day'],'bid'=>$w['bid'],'type'=>TallyModel::TALLY_TYPE_INCOME])->sum('amount');
        $data['total_payout'] = $mTally->skipBid()->where(['int_day'=>$w['int_day'],'bid'=>$w['bid'],'type'=>TallyModel::TALLY_TYPE_PAYOUT])->sum('amount');

        return $this->sendSuccess($data);

    }




}