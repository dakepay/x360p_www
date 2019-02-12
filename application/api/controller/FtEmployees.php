<?php
namespace app\api\controller;

use app\api\model\FtEmployee;
use think\Request;

class FtEmployees extends Base
{
    public function get_list(Request $request){
        $input = input();

        $mFtEmployee = new FtEmployee();
        $rs = $mFtEmployee->getSearchResult($input);

        return $this->sendSuccess($rs);
    }

	/**
	 * @desc  创建一个外教员工
	 * @method  POST
	 */
	public function post(Request $request)
    {
        $input = $request->post();
        if (empty($input['eid']) || empty($input['origin_country'])) {
            return $this->sendError(400, '缺少参数eid或参数不合法');
        }

        $mFtEmployee = new FtEmployee();
        $result =$mFtEmployee->createFtEmployee($input['eid'],$input['origin_country']);
        if(!$result){
            return $this->sendError(400, $mFtEmployee->getError());
        }

        return $this->sendSuccess();
	}

	/**
	 * @desc  编辑一个员工的基本信息
	 * @method  PUT
	 */
	public function put(Request $request)
    {
        $id = $request->param('id');
        $input = $request->put();
        if (empty($input['origin_country'])){
            return $this->sendError(400,'origin_country is null');
        }

        $mFtEmployee = new FtEmployee();
        $result = $mFtEmployee->editCountry($id, $input['origin_country']);

        if(!$result){
            return $this->sendError(400, $mFtEmployee->getError());
        }
        return $this->sendSuccess();
	}

	/**
	 * @desc  根据员工ID删除一个员工
	 * @method  DELETE
	 */
	public function delete(Request $request)
    {
        $id = $request->param('id');
        $mFtEmployee = new FtEmployee();
        $result = $mFtEmployee->deleteFtEmployee($id);
        if (!$result) {
            return $this->sendError(400, $mFtEmployee->getError());
        }
        return $this->sendSuccess();
	}




}