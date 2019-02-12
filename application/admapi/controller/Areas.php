<?php
/**
 * Author: luo
 * Time: 2017-12-13 16:41
**/

namespace app\admapi\controller;

use think\Request;
use app\admapi\model\Area;


class Areas extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        if(!isset($input['order_field'])){
            $input['order_field'] = 'area_id';
            $input['order_sort'] = 'ASC';
        }
        $mArea = new Area();
        $rs = $mArea->getSearchResult($input);

        return $this->sendSuccess($rs);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $mArea = new Area();
        $rs = $mArea->addArea($input);
        if(!$rs) return $this->sendError(400, $mArea->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = $request->put();
        $mArea = new Area();
        $rs = $mArea->updateArea($input);
        if(!$rs) return $this->sendError(400, $mArea->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request){
        $area_id = input('id/d');
        $mArea = new Area();
        $rs = $mArea->delArea($area_id);
        if(!$rs) return $this->sendError(400, $mArea->getErrorMsg());

        return $this->sendSuccess();
    }

}