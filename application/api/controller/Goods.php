<?php

namespace app\api\controller;

use think\Request;
use app\api\model\Goods as GoodsModel;

class Goods extends Base
{
    /**
     * @title 获取商品信息
     * @desc  根据条件获取商品详细信息
     * @url goods/:id
     * @method GET
     */
    protected function get_detail(Request $request,$id = 0){
        $goods = GoodsModel::get($id);
        if (!$goods) {
            return $this->sendError(400, '该商品不存在或已删除');
        }

        return $this->sendSuccess($goods);
    }

    /**
     * @title 获取所有商品列表
     * @desc  根据条件获取商品列表
     * @url goods
     * @method GET
     */
    protected function get_list(Request $request)
    {
        $input = $request->get();
        if ($request->isMobile()) {
            $where['status'] = 1;
            //todo 校区过滤
            $ret = model('Goods')->where($where)->getSearchResult($input, false);
            return $this->sendSuccess($ret);
        } else {
            $ret = model('Goods')->getSearchResult($input, ['lesson']);
            return $this->sendSuccess($ret);
        }
    }

    /**
     * @desc  商品上架
     * @author luo
     * @url   /api/goods/:id/doup
     * @method GET
     */
    public function do_up(Request $request)
    {
        $id = $request->param('id');
        $map = [];
        $map['status'] = 1;
        $map['on_time'] = time();
        GoodsModel::where('gid', $id)->update($map);
        return $this->sendSuccess();
    }

    /**
     * @desc  商品下架
     * @author luo
     * @param Request $request
     * @url   /api/goods/:id/dodown
     * @method GET
     */
    public function do_down(Request $request)
    {
        $id = $request->param('id');
        $map = [];
        $map['status'] = 0;
        $map['off_time'] = time();
        GoodsModel::where('gid', $id)->update($map);
        return $this->sendSuccess();
    }

}