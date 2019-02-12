<?php

namespace app\vipapi\controller;

use think\Hook;
use think\Request;
use think\Log;
use app\vipapi\model\Order;
use app\vipapi\model\Client;


/**
 * 付款码控制器
 */
class Paycode extends Base
{
    public $noRest = true;

    /**
     * 初始开通的付款二维码
     * @param Request $request [description]
     */
    public function Initpay(Request $request){
    	//查询订单是否存在
    	$user = $request->user;
    	$client = Client::get($user->cid);
    	
    	$w['cid'] 		 = $user->cid;
    	$w['order_type'] = 1;			//初始付款订单
    	$order = Order::get($w);

    	try{
	    	if(!$order){
	    		$order = Order::createInitOrder($client);
	    	}else{
	    		$order->updateOrderContent($client);
	    	}
	    }catch(Exception $e){
	    	return $this->sendError($e->getMessage());
	    }

    	$ret = $order->getPayCode();

    	return $this->sendSuccess($ret);
    }

    /**
     * 续费的付款二维码
     * @param Request $request [description]
     */
    public function Charge(Request $request){
    	//查询订单是否存在
    	$user = $request->user;
    	$client = Client::get($user->cid);
    	
    	$w['cid'] 		 = $user->cid;
    	$w['order_type'] = 2;			//初始付款订单
    	$w['pay_status'] = 0;			//未付款的

    	$order = Order::get($w);
    	$input = input('post.');

    	if(!$order){
    		$order = Order::createChargeOrder($client,$input);
    	}else{
    		$order->updateOrderContent($client,$input);
    	}	

    	$ret = $order->getPayCode();

    	return $this->sendSuccess($ret);
    }

    /**
     * 扩容的付款二维码
     * @param Request $request [description]
     */
    public function Expand(Request $request){
    	//查询订单是否存在
    	$user = $request->user;
    	$client = Client::get($user->cid);
    	
    	$w['cid'] 		 = $user->cid;
    	$w['order_type'] = 3;			//初始付款订单
    	$w['pay_status'] = 0;			//未付款的

    	$order = Order::get($w);
    	$input = input('post.');

    	if(!$order){
    		$order = Order::createExpandOrder($client,$input);
    	}else{
    		$order->updateOrderContent($client,$input);
    	}

    	$ret = $order->getPayCode();

    	return $this->sendSuccess($ret);
    }

    /**
     * 订单的付款二维码
     * @param Request $request [description]
     */
    public function Order(Request $request){
    	$vo_id = input('get.oid');

    	$order = Order::get($vo_id);

    	if(!$order){
    		return $this->sendError('订单不存在!');
    	}

    	$ret = $order->getPayCode();

    	return $this->sendSuccess($ret);
    }
}
