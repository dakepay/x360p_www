<?php
namespace app\api\import;

use app\common\Import;
use app\api\model\Order;
use app\api\model\Student;
use app\api\model\Customer;
use app\api\model\MarketClue;
use app\api\model\Lesson;
use app\api\model\Classes;
use app\api\model\AccountingAccount;
use app\api\model\Employee;

class Orders extends Import
{

	protected $fields = [
        ['field'=>'studnent_name','name'=>'学员姓名','must'=>true],
        ['field'=>'mobile','name'=>'联系电话','must'=>true],
        ['field'=>'lid','name'=>'课程'],
        ['field'=>'cid','name'=>'班级'],
        ['field'=>'lesson_hours','name'=>'课时数','must'=>true],
        ['field'=>'origin_price','name'=>'原单价','must'=>true],
        ['field'=>'discount_price','name'=>'折扣单价'],
        ['field'=>'present_lesson_hours','name'=>'赠送课时'],
        ['field'=>'consume_type','name'=>'报名类型'],
        ['field'=>'accounting_account','name'=>'收款账户','must'=>true],
        ['field'=>'salesman','name'=>'业绩归属'],
	];

	protected function get_fields()
	{
		return $this->fields;
	}

	protected function convert_mobile($value)
	{
		if(!is_mobile($value)) exception('联系电话格式不对');
		return $value;
	}

	protected function convert_lid($value)
	{
		static $cache = [];
		if(isset($cache[$value])){
			return $cache[$value];
		}
		$lid = 0;
		$m_lesson = new Lesson;
		$bid = request()->bid;
		$w[] = ['exp', "find_in_set({$bid},bids)"];
		$w['lesson_name'] = $value;
		$lesson = $m_lesson->where($w)->find();
		if(empty($lesson)) exception(get_branch_name($bid).'不存在课程【'.$value.'】');
		$lid =  $lesson->lid;
		$cache[$value] = $lid;

		return $lid;
	}

	protected function convert_cid($value)
	{
		static $cache = [];
		if(isset($cache[$value])){
			return $cache[$value];
		}
		$cid = 0;
		$m_classes = new Classes;
		$bid = request()->bid;
		$w['bid'] = $bid;
		$w['class_name'] = $value;
		$classes = $m_classes->where($w)->find();
		if(empty($classes)) exception(get_branch_name($bid).'不存在班级【'.$value.'】');
		$cid = $classes->cid;
		$cache[$value] = $cid;

		return $cid;
	}

	

	protected  function convert_lesson_hours($value)
	{
		if(!is_numeric($value)) exception('课时数格式不正确');
		return $value;
	}

	protected  function convert_origin_price($value)
	{
		if(!is_numeric($value)) exception('原单价格式不正确');
		return $value;
	}

	protected  function convert_discount_price($value)
	{
		if(!is_numeric($value)) exception('折扣单价格式不正确');
		return $value;
	}

	protected  function convert_present_hours($value)
	{
		if(!is_numeric($value)) exception('赠送课时格式不正确');
		return $value;
	}

	protected function convert_consume_type($value)
	{
		$map = ['新报'=>1,'续报'=>2,'扩科'=>3];
		if(key_exists($value,$map)){
			return $map[$value];
		}
		return 1;
	}

	protected function get_student_sid($student_name,$first_tel)
	{
		$m_student = new Student;
		$student = $m_student->where('first_tel',$first_tel)->find();

		// 忽略导入的学员名单在市场名单和客户名单中
		$m_customer = new Customer;
		$customer = $m_customer->where('first_tel',$first_tel)->find();
		if(!empty($customer) && empty($student)) exception('学员【'.$student_name.'：'.$first_tel.'】在客户名单中，暂不支持导入订单');

		$m_mc = new MarketClue;
		$market = $m_mc->where('tel',$first_tel)->find();
		if(!empty($market) && empty($student)) exception('学员【'.$student_name.'：'.$first_tel.'】在市场名单中，暂不支持导入订单');

		// 如果学员在学员名单不存在 创建一条学员信息
		if(empty($student)){
			$data['student_name'] = $student_name;
			$data['first_tel'] = $first_tel;
            return $m_student->createOneStudent($data);
		}
		return $student->sid;
	}

	protected function convert_accounting_account($value)
	{
		static $cache = [];
		if(isset($cache[$value])){
			return $cache[$value];
		}
		$aa_id = 0;
		$model = new AccountingAccount;
		$bid = request()->bid;
		$w[] = ['exp', "find_in_set({$bid},bids)"];
		$w['name'] = $value;
		$account = $model->where($w)->find();
		if(empty($account)) exception(get_branch_name($bid).'不存在账户【'.$value.'】');
		$aa_id = $account->aa_id;
		$cache[$value] = $aa_id;

		return $aa_id;
	}

	protected function convert_salesman($value)
	{
		$employees = explode(',',$value);
		$m_employee = new Employee;
		foreach ($employees as $k => $per_employee) {
			$per_employee = trim($per_employee);
			static $cache = [];
			if(isset($cache[$per_employee])){
				$ret[$k]['eid'] = $cache[$per_employee];
			}else{
				$employee = $m_employee->where('ename',$per_employee)->find();
				if(empty($employee)) exception('员工'.$per_employee.'不存在！');
				$ret[$k]['eid'] = $employee->eid;
				$cache[$per_employee] = $ret[$k]['eid'];
			}
		}

        return $ret;
	}



	protected function import_row(&$row,$row_no)
	{
		// print_r($row);exit;
		$fields = $this->get_fields();
		$add = [];
		$regular_fields_count = count($fields);
		if(count($row) < $regular_fields_count){
			$this->import_log[] = '导入的模板不正确，请下载系统最新模板导入！';
			return 2;
		}

		foreach ($fields as $index => $f) {
			$field = $f['field'];
			$name  = $f['name'];
			$cell = $row[$index];
			if(is_object($cell)){
				$value = $cell->getPlainText();
			}else{
				$value = $cell;
			}

			$func = 'convert_'.$field;

			//课程和科目必须填一个
			if(empty(trim($row[2])) && empty(trim($row[3]))){
				$this->import_log[] = '第'.$row_no.'行的【课程】【班级】必须填写一个';
				return 2;
			}

			if(empty($value)){
				if(isset($f['must']) && $f['must'] === true){
					$this->import_log[] = '第'.$row_no.'行的【'.$name.'】没有填写';
					return 2;
				}
			}else{
				$add[$field] = trim($value);
                if(method_exists($this,$func)){
	                try{
	                	$add[$field] = $this->$func($value);
	                }catch(\Exception $e){
	                	$this->import_log[] = '第'.$row_no.'行的【'.$name.'】'.$e->getMessage();
	                	return 2; 
	                }
	            }
			}
            
            // 根据 学员姓名和联系方式获取 sid
			$student_name = trim($row[0]);
			$mobile = trim($row[1]);
			try{
				$add['sid'] = $this->get_student_sid($student_name,$mobile);
			}catch(\Exception $e){
				$this->import_log[] = '第'.$row_no.'行的'.$e->getMessage();
				return 2;
			}
			

		}

		return $this->build_orders($add,$row_no);
	}
    

	protected function build_orders($data,$row_no)
	{
		$input = [];

		$discount_price = isset($data['discount_price']) ? $data['discount_price'] : $data['origin_price'];
		if($discount_price > $data['origin_price']){
			$this->import_log[] =  '第'.$row_no.'行的折扣单价不得大于原单价';
			return 2;
		}

		$role = user_config('params.default_sale_role_did');
		if($data['consume_type'] == 1){
			$sale_role_did = $role['new'];
		}else{
			$sale_role_did = $role['renew'];
		}

		foreach ($data['salesman'] as &$sale) {
			$sale['amount'] = $discount_price * $data['lesson_hours'];
			$sale['sale_role_did'] = $sale_role_did;
		}
        
        // 收款账户信息
		$account = get_accounting_info($data['accounting_account']);

		$input['order'] = [
		    'is_import' => 1,  //区别是导入的订单
            'paid_time' => date('Y-m-d',time()),
            'money_pay_amount' => $discount_price * $data['lesson_hours'],
            'paid_amount' => 0,
            'order_amount' => $discount_price * $data['lesson_hours'],
            'origin_amount' => $data['origin_price'] * $data['lesson_hours'],
            'order_discount_amount' => ($data['origin_price'] - $discount_price) * $data['lesson_hours'],
            'items' => [
                [
                    'lid' => isset($data['lid']) ?  $data['lid'] : 0,
                    'gid' => 0,
                    'pi_id' => 0,
                    'item_name' => get_lesson_name($data['lid']),
                    'name' => get_lesson_name($data['lid']),
                    'gtype' => 0,
                    'nums' => $data['lesson_hours'],
                    'nums_unit' => 2,
                    'expire_time' => '',
                    'origin_price' => $data['origin_price'],
                    'price' => $discount_price,
                    'origin_amount' => $data['origin_price'] * $data['lesson_hours'],
                    'paid_amount' => 0,
                    'discount_amount' => ($data['origin_price'] - $discount_price) * $data['lesson_hours'],
                    'present_lesson_hours' => isset($data['present_lesson_hours']) ? $data['present_lesson_hours'] : 0,

                    'origin_lesson_times' => 0,
	                'present_lesson_times' => 0,
	                'lesson_times' => 0,
	                'origin_lesson_hours' => 0,
	                'lesson_hours' => 0,
	                'subtotal' => $discount_price * $data['lesson_hours'],
	                'reduced_amount' => ($data['origin_price'] - $discount_price) * $data['lesson_hours'],


                    'cid' => isset($data['cid']) ? $data['cid'] : 0,
                    'consume_type' => $data['consume_type'],
                    'is_demo' => 0,
                ],
            ],
            'payment' => [
                [
                    'aa_id' => $account['aa_id'],
                    'name' => $account['name'],
                    'pay_amount' => $discount_price * $data['lesson_hours'],
                    'type' => $account['type'],
                    'cp_id' => $account['cp_id'],
                ]
            ],
            'is_demo' => 0,
            'is_submit' => 1,
		];

		$input['salesman'] = $data['salesman'];
		$input['student']['sid'] = $data['sid'];
        
        // 如果同时 填写了课程和班级 以班级为准
        $lid = $input['order']['items'][0]['lid'];
        $cid = $input['order']['items'][0]['cid'];
		if($lid && $cid){
			$input['order']['items'][0]['lid'] = 0;
		}

        $m_order = new Order;
		$res = $m_order->createOrder($input);

		if(!$res){
			$this->import_log[] = '第' . $row_no . '行的学员导入订单失败，检测班级是否满员';
            return 2;
		}

		return 0;

	}








}