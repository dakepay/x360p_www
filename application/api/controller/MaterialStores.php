<?php
/**
 * Author: luo
 * Time: 2017-11-23 15:49
**/

namespace app\api\controller;

use app\api\model\MaterialStoreQty;
use app\api\model\MaterialStore;
use think\Request;

class MaterialStores extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->param();
        $m_store = new MaterialStore();
        $ret = $m_store->with('branch')->getSearchResult($input);

        $ret['list'] = array_map(function($val){
            $bids = array_reduce($val['branch'], function($bids, $val){
                $bids[] = $val['bid'];
                return $bids;
            });

            $val['bids'] = $bids ? $bids : [] ;
            return $val;
        },$ret['list']);

        return $this->sendSuccess($ret);
    }

    public function get_list_materials(Request $request)
    {
        $input = $request->param();
        $ms_id = input('id/d');
        $input['ms_id'] = $ms_id;

        $model = new MaterialStoreQty();
        $ret = $model->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();

        $m_store = new MaterialStore();
        $rs = $m_store->createOneStore($input);
        if(!$rs) return $this->sendError(400, $m_store->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $ms_id = input('id/d');
        $m_msq = new MaterialStoreQty();
        $is_exist =  $m_msq->where('ms_id', $ms_id)->find();
        if(!empty($is_exist)) return $this->sendError(400, '仓库有物品不能删除');

        $store = MaterialStore::get(['ms_id' => $ms_id]);
        $rs = $store->delOneStore($store);
        if(!$rs) return $this->sendError(400, $store->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $ms_id = input('id/d');

        $store = MaterialStore::get(['ms_id' => $ms_id]);
        $rs = $store->updateStore($store, $input);
        if(!$rs) return $this->sendError(400, $store->getErrorMsg());

        return $this->sendSuccess();
    }



}