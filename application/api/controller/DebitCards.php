<?php
/**
 * Author: luo
 * Time: 2018/8/15 10:16
 */

namespace app\api\controller;


use app\api\model\Branch;
use app\api\model\DebitCard;
use app\api\model\StudentDebitCard;
use think\Request;

class DebitCards extends Base
{

    public function get_list(Request $request)
    {
        $m_dc = new DebitCard();
        $get = $request->get();

        if(!empty($get['dpt_id'])) {
            $dpt_id = $get['dpt_id'];
            $m_dc->where("find_in_set({$dpt_id}, dpt_ids)");
            unset($get['dpt_id']);
        }

        if(!empty($get['bid'])) {
            $bid = $get['bid'];
            $where_str = "find_in_set({$bid}, bids)";
            $area_dpt_id = Branch::GetParentAreaId($bid);
            if($area_dpt_id > 0) {
                $where_str .= " or find_in_set({$area_dpt_id}, dpt_ids)";
            }
            $m_dc->where($where_str);
        }
        $get['bid'] = -1;

        $ret = $m_dc->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_dc = new DebitCard();
        $rs = $m_dc->addCard($post);
        if($rs === false) return $this->sendError(400, $m_dc->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $dc_id = input('id');
        //$m_sdc = new StudentDebitCard();
        //$student_debit_card = $m_sdc->where('dc_id', $dc_id)->find();
        //if(!empty($student_debit_card)) return $this->sendError(400, '有购买记录，无法修改');

        $debit_card = DebitCard::get($dc_id);
        if(empty($debit_card)) return $this->sendError(400, '储蓄卡不存在');
        $put = $request->put();
        $rs = $debit_card->updateCard($put);
        if($rs === false) return $this->sendError(400, $debit_card->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $dc_id = input('id');
        $m_sdc = new StudentDebitCard();
        $student_debit_card = $m_sdc->where('dc_id', $dc_id)->find();
        if(!empty($student_debit_card)) return $this->sendError(400, '有购买记录，无法删除');

        $debit_card = DebitCard::get($dc_id);
        if(empty($debit_card)) return $this->sendSuccess();

        $rs = $debit_card->delete();
        if($rs === false) return $this->sendError(400, $debit_card->getErrorMsg());
        
        return $this->sendSuccess();
    }

}