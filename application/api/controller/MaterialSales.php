<?php

namespace app\api\controller;

use app\api\model\MaterialSale;
use think\Request;

class MaterialSales extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $mMaterialSale = new MaterialSale();
        $ret = $mMaterialSale->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    //物品详情
    public function get_detail(Request $request, $id = 0)
    {
        $mts_id = $id;
        $material = MaterialSale::get(['mts_id' => $mts_id], 'student.mterial');
        return $this->sendSuccess($material);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $mMaterialSale = new MaterialSale();

        $result = $mMaterialSale->addMaterialSale($post);
        if (false === $result){
            return $this->sendError(400,$mMaterialSale->getError());
        }

        return $this->sendSuccess();
    }


    public function put(Request $request)
    {
        $mts_id = input('id/d');
        $put = $request->put();
        unset($put['mts_id']);
        $mMaterialSale = new MaterialSale();
        $material_sale = $mMaterialSale->where('mts_id',$mts_id)->find();
        if (empty($material_sale)){
            return $this->sendError(400, '销售记录不存在');
        }

        $result = $mMaterialSale->updateMaterialSale($mts_id,$put);
        if (false === $result) {
            return $this->sendError(400, $mMaterialSale->getError());
        }

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $mts_id = input('id/d');
        $mMaterialSale = new MaterialSale();

        $result = $mMaterialSale->delMaterialSale($mts_id);
        if (false === $result) {
        return $this->sendError(400, $mMaterialSale->getError());
        }

        return $this->sendSuccess();
    }



}