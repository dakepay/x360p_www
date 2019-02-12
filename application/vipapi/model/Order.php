<?php
namespace app\vipapi\model;

use app\admapi\model\EmployeePerformance;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use app\common\Center;
use payment\alipay;



class Order extends Base{
	/**
	 * 处理付款结果
	 * @param  [type] $result  [description]
	 * @param  [type] $pay_way [description]
	 * @return [type]          [description]
	 */
	public function payResult($result,$pay_way){
		//添加支付日志
		//更新订单状态
		//订单关联业务状态处理
		$order_type = $this->order_type;
		$cid        = $this->cid;
		$int_pay_way = 0;		//线下支付

		if($pay_way == 'wxpay'){
			$amount = $result['total_fee'] / 100;
			$int_pay_way = 1;		//微信支付
			$transaction_no = $result['transaction_id'];
		}else{
			$amount = floatval($result['total_fee']);
			$int_pay_way = 2;		//支付宝支付
			$transaction_no = $result['trade_no'];
		}
		

		$opl['cid'] = $this->cid;
		$opl['vo_id'] = $this->vo_id;
		$opl['uid']   = $this->uid;
		$opl['amount'] = $amount;
		$opl['pay_way'] = $int_pay_way;
		$opl['transcation_no'] = $transaction_no;
		$opl['log'] = json_encode($result,JSON_UNESCAPED_UNICODE);

		$client = Client::get($cid);
        $per['cid'] = $this->cid;
        $per['eid'] = $client['eid'];
        $per['vo_id'] =  $this->vo_id;
        $per['amount'] = $amount;
        $per['consume_type'] = $order_type;

		$m_opl = new OrderPaylog();
		$m_emper = new EmployeePerformance();

		$this->startTrans();
		try{
			$result = $m_opl->save($opl);
            $result = $m_emper->save($per);

			//更新关联的业务
			if($order_type == 1){//初始开通
				$this->do_init_system($cid);
			}elseif($order_type == 2){//续费
				$this->do_charge();
			}elseif($order_type == 3){//扩容
				$this->do_expand();
			}elseif($order_type == 5){//增值服务
				$this->do_openservice();
			}else{//物品
				$this->do_noop();
			}
			$this->pay_way = $int_pay_way;
			$this->pay_status = 1;

			$result = $this->save();

		}catch(PDOException $e){
			$this->rollback();
			return false;
		}
		$this->commit();
		return true;
	}

	/**
	 * 空操作
	 * @return [type] [description]
	 */
	public function do_noop(){
		//todo:
		$this->status = 1;
		
	}

	/**
	 * 开通系统
	 * @param  [type] $cid [description]
	 * @return [type]      [description]
	 */
	public function do_init_system($cid){
		$w['cid'] = $cid;
		$client = Client::get($w);
		$client->is_init_pay = 1;
		$client->save();

		$this->status = 2;

	}

	/**
	 * 续年费
	 * @return [type] [description]
	 */
	public function do_charge(){
		$item = $this->items[0];
		$year_num = $item['nums'];

		$w['cid'] = $this->cid;
		$client   = Client::get($w);

		$old_expire_time = strtotime($client->expire_day);
		$now_time    = time();

		if($old_expire_time < $now_time){
			$old_expire_time = $now_time;
		}

		$year_str = 'years';

		if($year_num == 1){
			$year_str = 'year';
		}

		$new_expire_time = strtotime("+{$year_num} {$year_str}",$old_expire_time);

		$client->expire_day = date('Ymd',$new_expire_time);

		$client->save();

		$this->status = 2;

	}

	/**
	 * 扩容
	 * @return [type] [description]
	 */
	public function do_expand(){
		$item = $this->items[0];

		//$add_account_nums = $item['nums'];
		$add_student_nums = $item['nums'];

		$w['cid'] = $this->cid;
		$client   = Client::get($w);

		/*
		$old_account_num_limit = $client->account_num_limit;

		$new_account_num_limit = $old_account_num_limit + $add_account_nums;

		$client->account_num_limit = $new_account_num_limit;
		*/
		
		$old_student_num_limit = $client->student_num_limit;
		$new_student_num_limit = $old_student_num_limit + $add_student_nums;
		$client->student_num_limit = $new_student_num_limit;
		
		$client->save();

		$this->status = 2;

	}

	/**
	 * 开通增值服务
	 * @return [type] [description]
	 */
	public function do_openservice(){
		//todo:
		
	}



	public function payWxpayResult(&$result){
		//添加支付日志
		
		//更新订单状态
		//订单关联业务状态处理
	}

	public function payAlipayResult(&$result){

	}
	/**
	 * 创建客户初始化订单
	 * @param  Client $client [description]
	 * @return [type]         [description]
	 */
	static public function createInitOrder(Client $client){
		$m_order = new Order();
		$data['cid'] 		= $client->cid;
		$data['order_type'] = 1;
		$data['total_fee']  = $client->init_amount;
		$data['uid']		= request()->user->uid;
		$data['order_no']   = $m_order->make_orderno($data['order_type']);
		$data['pay_status'] = 0;
		$data['status']     = 0;

		$m_order->startTrans();
		$result = $m_order->save($data);

		if(!$result){
			$m_order->rollback();
			throw new Exception('创建订单失败:'.$m_order->getLastSql());
			return false;
		}

		$order_item['vo_id'] = $m_order->vo_id;
		$order_item['gid']   = 1;
		$order_item['nums']  = 1;
		$order_item['unit_price'] = $m_order->total_fee;
		$order_item['sub_amount'] = $m_order->total_fee;

		$result = $m_order->items()->save($order_item);

		if(!$result){
			$m_order->rollback();
			throw new Exception('创建订单条目失败:'.$m_order->items()->getLastSql());
		}

		$m_order->commit();
		return $m_order;
	}
	/**
	 * 创建续费订单
	 * @param  Client $client [description]
	 * @param  [type] $input  [description]
	 * @return [type]         [description]
	 */
	static public function createChargeOrder(Client $client,$input){
		$year = intval($input['year']);



		$unit_price   = $client->getRenewPrice();
		//$unit_price   = $client->init_renew_amount + $client->add_renew_amount;
		$total_amount = $unit_price * $year;

		$m_order = new Order();
		$data['cid'] 		= $client->cid;
		$data['order_type'] = 2;
		$data['total_fee']  = $total_amount;
		$data['uid']		= request()->user->uid;
		$data['order_no']   = $m_order->make_orderno($data['order_type']);
		$data['pay_status'] = 0;
		$data['status']     = 0;

		$m_order->startTrans();
		$result = $m_order->save($data);

		if(!$result){
			$m_order->rollback();
			throw new Exception('创建订单失败:'.$m_order->getLastSql());
			return false;
		}

		$order_item['vo_id'] = $m_order->vo_id;
		$order_item['gid']   = 2;
		$order_item['nums']  = $year;
		$order_item['unit_price'] = $unit_price;
		$order_item['sub_amount'] = $total_amount;

		$result = $m_order->items()->save($order_item);

		if(!$result){
			$m_order->rollback();
			throw new Exception('创建订单条目失败:'.$m_order->items()->getLastSql());
		}

		$m_order->commit();
		return $m_order;

	}

	static public function GetExpandField($input){
        $field = isset($input['f'])?$input['f']:'student';
        $allow_fields = ['student','branch','account'];
        if(!in_array($field,$allow_fields)){
            $field = $allow_fields[0];
        }
        return $field;
    }

	/**
	 * 创建扩容订单
	 * @param  Client $client [description]
	 * @param  [type] $input  [description]
	 * @return [type]         [description]
	 */
	static public function createExpandOrder(Client $client,$input){
	    $field = self::GetExpandField($input);
		$nums = intval($input['nums']);

		//$unit_price   = $client->account_price;
		$unit_price     = $client->getExpandPrice($field);
		if($unit_price == 0){
			$unit_price = 24;
		}
		$total_amount = $unit_price * $nums;

		$m_order = new Order();
		$data['cid'] 		= $client->cid;
		$data['order_type'] = 3;
		$data['total_fee']  = $total_amount;
		$data['uid']		= request()->user->uid;
		$data['order_no']   = $m_order->make_orderno($data['order_type']);
		$data['pay_status'] = 0;
		$data['status']     = 0;

		$m_order->startTrans();
		$result = $m_order->save($data);

		if(!$result){
			$m_order->rollback();
			throw new Exception('创建订单失败:'.$m_order->getLastSql());
			return false;
		}

		$order_item['vo_id'] = $m_order->vo_id;
		$order_item['gid']   = 3;
		$order_item['nums']  = $nums;
		$order_item['unit_price'] = $unit_price;
		$order_item['sub_amount'] = $total_amount;
		$order_item['expand_field'] = $field;

		$result = $m_order->items()->save($order_item);

		if(!$result){
			$m_order->rollback();
			throw new Exception('创建订单条目失败:'.$m_order->items()->getLastSql());
		}

		$m_order->commit();
		return $m_order;
	}

	/**
	 * 更新订单内容
	 * @param  array  $input [description]
	 * @return [type]        [description]
	 */
	public function updateOrderContent(Client $client,$input = array()){
		$order_type = $this->getData('order_type');

		if($this->pay_status == 1){	//如果订单已经支付就不能更新了
			return $this;
		}

		$old_order_no = $this->order_no;			//旧的订单号

		//$this->order_no = $this->make_orderno($order_type);
		if($order_type == 1){//初始开通
			$new_order_amount = $client->init_amount;
			$item = $this->items[0];

			if($item->sub_amount != $new_order_amount){
				$item->unit_price = $new_order_amount;
				$item->sub_amount = $new_order_amount;
				$item->save();

				$this->total_fee = $new_order_amount;

				$this->order_no  = $this->make_orderno($order_type);
			}

		}elseif($order_type == 2){	//续费
			$year = intval($input['year']);
			$item = $this->items[0];

			$old_unit_price = $item->unit_price;
			$new_unit_price   = $client->getRenewPrice();

			//$new_unit_price = $client->init_renew_amount + $client->add_renew_amount;

			if($item->nums != $year || $old_unit_price != $new_unit_price){
				$unit_price = $new_unit_price;
				$item->nums = $year;
				$item->unit_price = $unit_price;
				$item->sub_amount = $unit_price * $year;
				$item->save();

				$this->total_fee = $item->sub_amount;
				$this->order_no  = $this->make_orderno($order_type);
			}
		}elseif($order_type == 3){  //扩容
			$nums = intval($input['nums']);
			$field = self::getExpandField($input);
			$item = $this->items[0];
			$old_unit_price = $item->unit_price;
			//$new_unit_price = $client->account_price;
			$new_unit_price = $client->getExpandPrice($field);
			if($item->nums != $nums || $old_unit_price != $new_unit_price){
				$unit_price = $new_unit_price;
				if($unit_price == 0){
					$unit_price = 24;
				}
				$item->nums = $nums;
				$item->unit_price = $unit_price;
				$item->sub_amount = $item->unit_price * $nums;
				$item->expand_field = $field;
				$item->save();

				$this->total_fee = $item->sub_amount;
				$this->order_no  = $this->make_orderno($order_type);
			}
		}

		$this->save();

		return $this;
	}

	/**
	 * 获取订单二维码
	 * @return [type] [description]
	 */
	public function getPayCode(){
		$ret['alipay_qr_url'] = '';
		$ret['wxpay_qr_url']  = '';
		$ret['vo_id'] = $this->vo_id;
		$ret['order_no'] = $this->order_no;

		$order_no     = $this->order_no;
		$order_body   = $this->items[0]->goods->title;
		$items_count = count($this->items);

		if($items_count > 1){
			$order_body .= '等'.$items_count.'个产品服务条目';
		}
		/*
		$alipay_order = [
            'out_trade_no' => $this->order_no,
            'total_amount' => $this->total_fee * 100,
            'subject'      => $order_body,
        ];

        $alipay_config = Center::alipay_config(APP_DEBUG);
        $alipay = Pay::alipay($alipay_config);
        $alipay_scan_result = $alipay->scan($alipay_order);
        $ret['alipay_qr_url'] = $alipay_scan_result->qr_code;
		*/
		
		$alipay_config 	= Center::old_alipay_config(APP_DEBUG);

		$extra_config 	= array(
			// 即时到账方式
		    'payment_type' => 1,
		    // 传输协议
		    'transport' => 'http',
		    // 编码方式
		    'input_charset' => 'utf-8',
		    // 签名方法
		    'sign_type' => 'MD5',
		    // 支付完成异步通知调用地址
		    'notify_url' => 'https://vip.pro.xiao360.com/vipapi/Alipay/notify',
		    // 支付完成同步返回地址
	        'return_url' => 'https://vip.pro.xiao360.com/vipapi/Alipay/return'
		);

		$alipay_config = array_merge($alipay_config,$extra_config);

		$c_pay = new alipay($alipay_config);

		$formid = 'alipayform';

		$form_html = $c_pay->buildRequestHiddenFormDomHTML([
		    'out_trade_no'      => $order_no,
		    'subject'           => $order_body,
		    'total_fee'         => $this->total_fee,
		    'body'              => $order_body,
		    'show_url'          => 'http://'.$_SERVER['HTTP_HOST'],
		    'anti_phishing_key' => '',
		    'exter_invoke_ip'   => '',
		    'it_b_pay'          => '5m',
		    '_input_charset'    => 'utf-8'
		],$formid);

		$ret['alipay_html']   = $form_html;
		$ret['alipay_formid'] = $formid;
		

        $wxpay_order = [
		    'out_trade_no' => $this->order_no,
		    'body' 		   => $order_body,
		    'total_fee'    => $this->total_fee * 100,
        ];

        $wxpay_config = Center::wxpay_config(APP_DEBUG);

        $wxpay = Pay::wechat($wxpay_config);

        $wxpay_scan_result = $wxpay->scan($wxpay_order);

        $ret['wxpay_qr_url'] = $wxpay_scan_result->code_url;
        $ret['total_fee'] = floatval($this->total_fee);

		return $ret;
	}

	/**
	 * [Items description]
	 */
	public function Items(){
		return $this->hasMany('OrderItem','vo_id','vo_id');
	}


	public function make_orderno($order_type){
		return sprintf("%s%s%s",$order_type,date('dYmih',time()),random(5));
	}
}