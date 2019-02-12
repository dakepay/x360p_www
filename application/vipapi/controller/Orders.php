<?php
namespace app\vipapi\controller;

use think\Request;
use app\vipapi\model\Order;

class Orders extends Base
{
	public function get_list(Request $request){
		$m = new Order;

		$input = $request->get();

		$input['cid'] = $this->user->cid;

		$result = $m->getSearchResult($input,true);

		return $this->sendSuccess($result);

	}


	public function get_detail(Request $request,$id = 0){
		$ret = Order::get($id);
        return $this->sendSuccess($ret);
	}
}