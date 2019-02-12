<?php
/**
 * Author: luo
 * Time: 2018/8/15 11:10
 */

namespace app\api\controller;


use app\api\model\StudentDebitCard;
use think\Request;

class StudentDebitCards extends Base
{

    public function  get_list(Request $request)
    {
        $m_sdc = new StudentDebitCard();
        $get = $request->get();

        if(!empty($get['expire_day'])) {
            $m_sdc->where('expire_int_day = 0 or expire_int_day >=' . format_int_day($get['expire_day']));
            unset($get['expire_day']);
        }
        $ret = $m_sdc->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();

        $m_sdc = new StudentDebitCard();
        $sdc_id = $m_sdc->addCard($post);
        if($sdc_id === false) return $this->sendError(400, $m_sdc->getErrorMsg());

        return $this->sendSuccess(['sdc_id' => $sdc_id]);
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $mStudentDebitCard = new StudentDebitCard();
        $result = $mStudentDebitCard->updateExpireDay($input);
        if(false === $result) return $this->sendError(400, $mStudentDebitCard->getError());
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $sdc_id = input('id');

        $student_debit_card = StudentDebitCard::get($sdc_id);
        if(empty($student_debit_card)) return $this->sendSuccess();

        $rs = $student_debit_card->delCard();
        if($rs === false) return $this->sendError(400, $student_debit_card->getErrorMsg());

        return $this->sendSuccess();
    }



}