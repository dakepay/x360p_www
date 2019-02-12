<?php
/**
 * luo
 */
namespace app\api\controller;

use app\api\model\Branch;
use think\Db;
use think\Request;
use app\api\model\Branch as BranchModel;

/**
 * Class Branches
 * @title 校区管理接口
 * @url branches
 * @desc  校区的添加、编辑、删除
 */
class Branches extends Base
{

    public function post(Request $request)
    {
        return $this->sendError(400, '在部门管理那里添加校区');
    }

    /**
     * @title 获取校区信息
     * @desc  根据条件获取单个校区信息
     * @url branches
     * @method GET
     * @readme 详细说明
     */
    protected function get_detail(Request $request,$id = 0){
        $branch = BranchModel::get($id);
        if (!$branch) {
            return $this->sendError(400, '该校区不存在或已删除');
        }

        return $this->sendSuccess($branch);
    }

    /**
     * @title 获取所有校区列表
     * @desc  根据条件获取校区列表
     * @url branches
     * @method GET
     * @readme 详细说明
     */
    protected function get_list(Request $request)
    {
        $input = $request->param();
        $result = model('branch')->getSearchResult($input);
        return $this->sendSuccess($result);
    }

    /**
     * @desc  编辑校区的基本信息
     * @url branches/:id
     * @method  PUT
     */
    public function put(Request $request)
    {
        $id = $request->param('id');

        $branch = BranchModel::get($id);
        if (!$branch) {
            return $this->sendError(400, '该校区不存在或以被删除');
        }

        $data = input('put.');
        $rule = [
            ['branch_name|校区名称', 'require|unique:branch,branch_name,' . $id],
            ['short_name|校区简称', 'require|unique:branch,short_name,' . $id],
            ['branch_type|校区类型', 'in:1,2'],
        ];
        $result = $this->validate($data,$rule);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $result = $branch->allowField(true)->save($data);
        if ($result === false) {
            return $this->sendError(400, $branch->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  根据校区ID删除一个校区
     * @url branches/:id
     * @method  DELETE
     */
    public function delete(Request $request)
    {
        return $this->sendError(400, '请在部门管理那里删除校区');
    }

    public function get_list_materials(Request $request)
    {
        $input = $request->param();
        $bid = input('id/d');


        $branch = Branch::get(['bid' => $bid]);
        if(empty($branch)) return $this->sendError(400, '校区不存在');
        $ms_id = $branch->ms_id;
        $input['ms_id'] = $ms_id;

        $ret['page'] = $page = input('page', 1);
        $ret['pagesize'] = $pagesize = input('pagesize', config('default_pagesize'));

        $ret['list'] = Db::name('material')->alias('m')->join('material_store_qty q', 'm.mt_id = q.mt_id')
            ->where('q.ms_id', $input['ms_id'])->page($page, $pagesize)->field('m.*, q.ms_id,q.num as store_num')
            ->select();

        return $this->sendSuccess($ret);
    }

}