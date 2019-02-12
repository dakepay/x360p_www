<?php

namespace app\api\import;

use app\common\Import;
use app\api\model\FranchiseeContract;
use app\api\model\Franchisee;

class FranchiseeContracts extends Import
{
	protected $start_row_index = 3;
	protected $pagesize = 20;

	protected $fields = [
        ['field'=>'fc_id','name'=>'加盟商名称','must'=>true],
        ['field'=>'contract_no','name'=>'合同号','must'=>true],
        ['field'=>'sign_eid','name'=>'签约员工'],
        ['field'=>'contract_start_int_day','name'=>'合同开始日期','must'=>true],
        ['field'=>'contract_end_int_day','name'=>'合同结束日期','must'=>true],
        ['field'=>'open_int_day','name'=>'开业时间'],
        ['field'=>'region_level','name'=>'区域性质'],
        ['field'=>'join_fee1','name'=>'特许经营费'],
        ['field'=>'join_fee2','name'=>'履约保证金'],
        ['field'=>'join_fee3','name'=>'年度使用费'],
        ['field'=>'join_fee4','name'=>'教育商品款'],
        ['field'=>'contract_amount','name'=>'合同总金额'],
        ['field'=>'all_pay_int_day','name'=>'全款到账日期'],
        ['field'=>'content','name'=>'特殊约定'],
	];


	public function __init()
	{

	}

	protected function get_fields()
	{
		return $this->fields;
	}

	protected function import_row(&$row,$row_no)
	{
		$fields = $this->get_fields();
		$add = [];
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

			if(empty($value)){
				if(isset($f['must']) && $f['must'] === true){
					$this->import_log[] = '第'.$row_no.'行的【'.$name.'】没有填写！';
					return 2;
				}
			}else{
				$add[$field] = trim($value);
				if(method_exists($this,$func)){
					$add[$field] = $this->$func($value);
				}
				if($add[$field] === false){
					$this->import_log[] = '第'.$row_no.'行的【'.$name.'】错误，可能不存在此'.$name;
					return 2;
				}
			}
		}

		return $this->add_data($add,$row_no);
	}

	public function convert_fc_id($value)
	{
		$m_franchisee = m('franchisee');
		$franchisee = $m_franchisee->where('org_name',$value)->cache(2)->field('fc_id')->find();
		if(empty($franchisee)){
			return false;
		}
		return $franchisee['fc_id'];
	}

	public function convert_sign_eid($value)
	{
        $m_employee = m('employee');
        $employee = $m_employee->where('ename',$value)->cache(2)->field('eid')->find();
        if(empty($employee)){
        	return false;
        }
        return $employee['eid'];
	}

	public function convert_contract_start_int_day($value)
	{
		return dage_to_date($value);
	}

	public function convert_contract_end_int_day($value)
	{
		return dage_to_date($value);
	}

	public function convert_open_int_day($value)
	{
		if($value){

			return dage_to_date($value);
		}
		return 0;
	}

    public function convert_all_pay_int_day($value)
	{
		if($value){

			return dage_to_date($value);
		}
		return 0;
	}

	public function convert_region_level($value)
	{
		$map = ['一类'=>1,'二类'=>2,'三类'=>3,'四类'=>4,'五类'=>5];
		if(key_exists($value,$map)){
			return $map[$value];
		}
		return 0;
	}

	protected function add_data($data,$row_no)
	{
		$w['contract_no'] = $data['contract_no'];
		$exist_data = m('franchisee_contract')->where($w)->find();
		if(!empty($exist_data)){
			$this->import_log[] = '第'.$row_no.'行的合同号有重复！';
			return 1;
		}

        $m_fc = new FranchiseeContract;
        $ret = $m_fc->data([])->allowField(true)->isUpdate(false)->save($data);
        if(false === $ret){
        	$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败：'.m('franchisee_contract')->getError();
        	return 2;
        }

        $model = new Franchisee;
        $rt['contract_start_int_day'] = $data['contract_start_int_day'];
        $rt['contract_end_int_day']   = $data['contract_end_int_day'];
        $rt['is_sign'] = 1;
        if($data['open_int_day']){
        	$rt['open_int_day'] = $data['open_int_day'];
        }
        $where['fc_id'] = $data['fc_id'];
        $ret = $model->data([])->allowField(true)->isUpdate(true)->save($rt,$where);
        if(false === $ret){
        	$this->import_log[] = '第'.$row_no.'行的数据更新失败：'.m('franchisee')->getError();
        	return 2;
        }


        return 0;

	}




	

}