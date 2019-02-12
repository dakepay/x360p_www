<?php
namespace app\api\import;

use app\common\Import;
use app\api\model\FranchiseeServiceRecord;
use app\api\model\Franchisee;
use app\api\model\Employee;
use app\api\model\Dictionary;

class FranchiseeServiceRecords extends Import
{
	protected $start_row_index = 3;

	protected $fields = [
        ['field'=>'fc_id','name'=>'加盟商','must'=>true],
        ['field'=>'fc_service_did','name'=>'服务类型'],
        ['field'=>'eid','name'=>'服务员工','must'=>true],
        ['field'=>'int_day','name'=>'完成日期','must'=>true],
        // ['field'=>'int_hour','name'=>'完成时间'],
        ['field'=>'content','name'=>'服务内容','must'=>true],
	];

	public function __init(){}

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
		$m_franchisee = new Franchisee;
		$franchisee = $m_franchisee->where('org_name',$value)->cache(2)->field('fc_id')->find();
		if(empty($franchisee)){
			return false;
		}
		return $franchisee['fc_id'];
	}

	public function convert_eid($value)
	{
		$m_employee = new Employee;
		$employee = $m_employee->where('ename',$value)->cache(2)->field('eid')->field('eid')->find();
		if(empty($employee)){
			return false;
		}
		return $employee['eid'];
	}

	public function convert_fc_service_did($value)
	{
		$m_dictionary = new Dictionary;
		$where['pid'] = 52;
		$where['name'] = $value;
		$dictionary = $m_dictionary->where($where)->cache(2)->field('did')->find();
		if(empty($dictionary)){
			return false;
		}
		return $dictionary['did'];

	}

	public function convert_int_day($value)
	{
		return dage_to_date($value);
	}


	protected function add_data($data,$row_no)
	{
		$m_fsr = new FranchiseeServiceRecord;
		$ret = $m_fsr->data([])->allowField(true)->isUpdate(false)->save($data);

		if(false === $ret){
			$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败：'.m('franchisee_service_record')->getError();
			return 2;
		}

		return 0;
	}





}