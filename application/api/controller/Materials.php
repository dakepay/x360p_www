<?php
/**
 * Author: luo
 * Time: 2017-11-20 20:53
**/

namespace app\api\controller;

use app\api\model\LessonMaterial;
use app\api\model\Material;
use app\api\model\MaterialStore;
use app\api\model\MaterialStoreQty;
use app\api\model\MaterialHistory;
use think\Request;

class Materials extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_material = new Material();
        $with_arr = isset($get['with']) ? explode(',', $get['with']) : [];
        if(($key = array_search('children_num', $with_arr)) !== false) {
            unset($with_arr[$key]);
            $get['with'] = implode(',', $with_arr);
            $with_children_num = true;
        }

        $ret = $m_material->with('materialStoreQty.store')->getSearchResult($get);
        foreach($ret['list'] as &$row) {
            if(isset($with_children_num) && $with_children_num == true) {
                $row['children_num'] = $m_material->where('parent_id', $row['mt_id'])->count();
            }
            if(!empty($get['parent_name'])) {
                $row = $this->_plusParentName($row);
            }
        }

        return $this->sendSuccess($ret);
    }

    //物品加上上一级名字
    private function _plusParentName($material)
    {
        if(empty($material) || empty($material['parent_id'])) return $material;

        $list = m('Material')->limit(400)->cache(2)->select();
        if(empty($list)) return $material;

        foreach($list as $row) {
            if($row['mt_id'] == $material['parent_id']) {
                if($row['parent_id'] > 0)  {
                    $row = $this->_plusParentName($row);
                }
                $material['name'] = $row['name'] . '-' . $material['name'];
                break;
            }
        }

        return $material;
    }

    //物品详情
    public function get_detail(Request $request, $id = 0)
    {
        $mt_id = $id;
        $material = Material::get(['mt_id' => $mt_id], 'materialStoreQty.store');
        return $this->sendSuccess($material);
    }

    public function post(Request $request)
    {
        return parent::post($request);
    }

    /**
     * @desc  物品的进出库记录
     * @author luo
     * @method POST
     */
    public function post_history(Request $request)
    {
        $input = $request->post();
        $m_mh = new MaterialHistory();
        $rs = $m_mh->addBatchHisOfMaterial($input);
        if(!$rs) return $this->sendError(400, $m_mh->getError());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return parent::put($request);
    }

    public function delete(Request $request)
    {
        $mt_id = input('id/d');
        $material = Material::get(['mt_id' => $mt_id]);
        if(empty($material)) return $this->sendError(400, '物品不存在');

        $history = MaterialHistory::get(['mt_id' => $mt_id]);
        if(!empty($history)) return $this->sendError(400, '物品有出入库记录，不能删除');

        $is_exist = LessonMaterial::get(['mt_id' => $mt_id]);
        if(!empty($is_exist)) return $this->sendError(400, '有关联课程，不能删除');

        $rs = $material->delMaterial($material);
        if($rs === false) return $this->sendError(400, $material->getError());

        return $this->sendSuccess();
    }

    public function get_list_store(Request $request)
    {
        $mt_id = input('id/d');
        $input = $request->param();

        $m_store = new MaterialStore();
        $ret = $m_store->getSearchResult($input);

        $m_msq = new MaterialStoreQty();
        foreach($ret['list'] as &$per) {
            $row = $m_msq->where('mt_id', $mt_id)->where('ms_id', $per['ms_id'])->column('num');
            $per['num'] = isset($row[0]) ? $row[0] : 0;
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  各个仓库的物品数量
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function store(Request $request)
    {
        $get = $request->get();
        $m_msq = new MaterialStoreQty();
        $ret = $m_msq->with(['material','store'])->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

}