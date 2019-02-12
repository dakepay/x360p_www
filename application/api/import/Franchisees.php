<?php 

namespace app\api\import;

use app\common\Import;
use app\api\model\Franchisee;

class Franchisees extends Import
{
	protected $start_row_index = 3;
	protected $pagesize = 20;

	protected $fields = [
	    ['field'=>'org_name','name'=>'加盟商名称','must'=>true],
	    ['field'=>'org_address','name'=>'详细地址','must'=>true],
	    ['field'=>'mobile','name'=>'联系电话','must'=>true],
	    ['field'=>'org_email','name'=>'邮箱'],
	    ['field'=>'business_license','name'=>'营业执照号'],
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
		// print_r($row);exit;
		$fields = $this->get_fields();
		$add = [];
		foreach ($fields as $index => $f) {
			$field = $f['field'];
			/*if(!isset($row[$index])){
				continue;
			}*/

			$cell = $row[$index];
			if(is_object($cell)){
				$value = $cell->getPlainText();
			}else{
				$value = $cell;
			}

			$func = 'convert_'.$field;

			if(empty($value)){ //如果字段没有填写 判断是否必填
				if(isset($f['must']) && $f['must'] === true){
					$this->import_log[] = '第'.$row_no.'行的【'.$f['name'].'】没有填写！';
					return 2;
				}
			}else{ // 如果字段填写了，判断填写是否正确
                $add[$field] = trim($value);
                if(method_exists($this,$func)){
                	$add[$field] = $this->$func($value);
                }
                if($add[$field] === false){
                    $this->import_log[] = '第'.$row_no.'行的【'.$f['name'].'】错误，可能不存在此'.$f['name'];
                    return 2;
                }
			}
		}

		return $this->add_data($add,$row_no);
	}


	protected function add_data($data,$row_no)
	{
		$w['org_name'] = $data['org_name'];
		$exists_data = m('franchisee')->where($w)->find();
		if(!empty($exists_data)){
			$this->import_log[] = '第'.$row_no.'行的加盟商名称已被使用！';
			return 1;
		}
        
        $m_franchisee = new Franchisee;
		$ret = $m_franchisee->data([])->allowField(true)->isUpdate(false)->save($data);
		if(false === $ret){
			$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败：'.m('franchisee')->getError();
			return 2;
		}

		return 0;

	}

}