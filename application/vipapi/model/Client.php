<?php

namespace app\vipapi\model;

use think\Db;

class Client extends Base{
	protected $table = 'pro_client';

	/**
	 * 根据客户ID获得客户信息
	 * @param  [type] $cid [description]
	 * @return [type]      [description]
	 */
	static public function getByCid($cid){
		$w['cid'] = $cid;
		$m_client = new Self;
		$client = $m_client->where($w)->find();
		return $client->append(['student_num_current','account_num_current','branch_num_current','saleman'],true)->toArray();
	}
	/**
	 * 获得当前账号数数量
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function getAccountNumCurrentAttr($value,$data){
		$dc = $this->DatabaseConfig;
		$dbpre = $dc->prefix;
		$db  = Db::connect($dc->toArray());
        $og_id = $this->og_id;
        $where_og_id = '';
        if($og_id > 0){
            $where_og_id = ' and `og_id` = '.$og_id;
        }
		$row = $db->query("select count(*) as total from {$dbpre}user where user_type = 1".$where_og_id." and `is_delete`=0");
		$db->close();
		return $row[0]['total'];
	}

	public function getBranchNumCurrentAttr($value,$data){
        $dc = $this->DatabaseConfig;
        $dbpre = $dc->prefix;
        $db  = Db::connect($dc->toArray());
        $og_id = $this->og_id;
        $where_og_id = '';
        if($og_id > 0){
            $where_og_id = ' and `og_id` = '.$og_id;
        }
        $row = $db->query("select count(*) as total from {$dbpre}branch where 1=1".$where_og_id." and `is_delete`=0");
        $db->close();
        return $row[0]['total'];
    }

	/**
	 * 获得当前学员数许可数量
	 * @param  [type] $value [description]
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	public function getStudentNumCurrentAttr($value,$data){
		$dc = $this->DatabaseConfig;
		$dbpre = $dc->prefix;
		$db  = Db::connect($dc->toArray());
		$og_id = $this->og_id;
		$where_og_id = '';
		if($og_id > 0){
			$where_og_id = ' and `og_id` = '.$og_id;
		}
		$row = $db->query("select count(*) as total from {$dbpre}student where `status` < 90".$where_og_id." and `is_delete`=0");
		$db->close();
		return $row[0]['total'];
	}

	/**
	 * 获得业务员联系方式
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	public function getSalemanAttr($value,$data){
		$eid  = $this->getData('eid');
		if(!$eid){
			return null;
		}
		$employee = Employee::get($eid);
		if(!$employee){
			return null;
		}
		return $employee->append(['user'])->toArray();
	}

	public function DatabaseConfig(){
		return $this->hasOne('DatabaseConfig','cid','cid');
	}

    /**
     * 获得扩容单价
     * @param $field
     * @return float
     */
	public function getExpandPrice($field){
        $unit_price = $this->getUnitPrice($field);
        $data = $this->getData();
        $expire_time = strtotime(int_day_to_date_str($data['expire_day']));
        $now_time    = time();

        $limit_time = $expire_time - $now_time;

        $limit_days = ceil($limit_time / 86400);


        $price = round($limit_days / 365 * $unit_price,2);


        return $price;


    }

    /**
     * 获得计费字段
     * @return mixed|string
     */
    public function getCacuAmountField(){
	    $data   = $this->getData();
	    $fields = ['account','branch','student'];
	    $field  = 'student';
	    foreach($fields as $f){
	        $ff = 'is_'. $f .'_limit';
	        if(isset($data[$ff]) && $data[$ff] == 1){
	            $field = $f;
	            break;
            }
        }
        return $field;
    }

    public function getUnitPrice($field){
        $f = $field.'_price';
        $config = config('price');
        $standard_price = $config[$f];
        $data = $this->getData();
        $unit_price = $data[$f];
        if($unit_price == 0){
            $unit_price = $standard_price;
        }
        return $unit_price;
    }


    /**
     * 获得续费单价
     * @return float|int
     */
    public function getRenewPrice(){
        $data = $this->getData();
        $field = $this->getCacuAmountField();
        $num_field = $field.'_num_limit';
        $unit_price = $this->getUnitPrice($field);
        $nums = $data[$num_field];
        return $unit_price * $nums;
    }
}