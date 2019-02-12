<?php
/*
 *1. 以下是有关支付的方法，签名方式等可以进行参考，具体的业务逻辑实现还需要参考文档，有不懂的可以和收钱吧技术人员确认。
 *2. 请求支付后，订单的状态信息通过轮询的方式获取。
 * */
namespace app\api\controller;

use think\Request;
use app\api\model\ShouQianBa;
use app\api\model\CenterClientApplySqb;


class ShouQianBas extends Base
{

    public function get_list(Request $request){

        $get = $request->get();
        $mCas = new CenterClientApplySqb();
        $client = gvar('client');
        $result = $mCas->where(['cid'=>$client['cid'],'og_id'=>$client['og_id']])->with('client_apply_sqb_check')->getSearchResult($get);

        return $this->sendSuccess($result);
    }

    /**
     * @desc  商户入网信息提交
     * @param Request $request
     */
    public function post(Request $request){
        $input = $request->post();
        $mCas = new CenterClientApplySqb();
        $result = $mCas->audit($input);
        if(!$result) {
            return $this->sendError(400, $mCas->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * 商户入网信息编辑
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function put(Request $request){
        $input = $request->put();
        $mCas = new CenterClientApplySqb();
        $result = $mCas->updateInfo($input);
        if(!$result) {
            return $this->sendError(400, $mCas->getError());
        }

        return $this->sendSuccess();
    }


    /**
     * @desc  地区
     */
    public function get_area(){
        $mShouQianBa = new ShouQianBa();
        $rs = $mShouQianBa->getArea();
        if(!$rs) {
            return $this->sendError(400, $mShouQianBa->getError());
        }
        return $rs;
    }


    /**
     * @desc  开户银行
     * @param Request $request
     */
    public function get_bank(Request $request){
        $bank_card = $request->get('bank_card/d');
        $mShouQianBa = new ShouQianBa();
        $rs = $mShouQianBa->getBank($bank_card);
        if(!$rs) {
            return $this->sendError(400, $mShouQianBa->getError());
        }
        return $this->sendSuccess($rs);

    }

    /**
     * @desc  支行列表
     * @param Request $request
     */
    public function get_branchs(Request $request){
        $input = input();
        $mShouQianBa = new ShouQianBa();
        $rs = $mShouQianBa->getBranches($input);
        if(!$rs) {
            return $this->sendError(400, $mShouQianBa->getError());
        }
        return $this->sendSuccess($rs);

    }

    /**
     * @desc  上传图片
     * @param Request $request
     */
    public function upload(Request $request){
        $input = $request->file();
        $mShouQianBa = new ShouQianBa();
        $rs = $mShouQianBa->upload($input['file']);
        if(!$rs) {
            return $this->sendError(400, $mShouQianBa->getError());
        }
        return $this->sendSuccess($rs);
    }

}

