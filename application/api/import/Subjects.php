<?php
namespace app\api\import;

use app\common\Import;

class Subjects extends Import{
	protected $res = 'subject';
	protected $start_row_index = 3;
	protected $pagesize = 20;

	protected $fields = [
		['field'=>'subject_name','name'=>'科目名称','must'=>true],
		['field'=>'unit_price','name'=>'课时单价'],
		['field'=>'per_lesson_hour_minutes','name'=>'每课时分钟数'],
	];

	/**
	 * 添加数据到数据库
	 * @param [type] $data   [description]
	 * @param [type] $row_no [description]
	 * @return  0 成功
	 * @return  2 失败
	 * @return  1 重复
	 */
	protected function add_data($data,$row_no){
		
		$w['subject_name'] = $data['subject_name'];
		

		$exists_subject = m('subject')->where($w)->find();


		if($exists_subject){
			$w_subject['sj_id'] = $exists_subject['sj_id'];

			$update_subject = [];

			$update_fields = ['unit_price','per_lesson_hour_minutes'];

			foreach($update_fields as $f){
				if(!empty($data[$f]) && $data[$f] != $exists_subject[$f]){
					$update_subject[$f] = $data[$f];
				}
			}

			if(!empty($update_subject)){
				$result = $exists_subject->save($update_subject);
				if(false === $result){
					$this->import_log[] = '第'.$row_no.'行的科目资料有更新，但是更新失败,SQL:'.m('subject')->getLastSql().print_r($update_subject,true);
				}else{
					$this->import_log[] = '第'.$row_no.'行的科目资料有更新，更新成功!';
				}
				return 1;
			}
			$this->import_log[] = '第'.$row_no.'行的数据有重复!';
			return 1;
		}

		$rs = m('subject')->data($data)->isUpdate(false)->save();

		if(!$rs){
			$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败:'.m('subject')->getError();
			return 2;
		}

		return 0;

	}

	protected function get_fields(){
		return $this->fields;
	}


	protected function import_row(&$row,$row_no){

        $fields = $this->get_fields();

        $add = [];

        foreach($fields as $index=>$f){
            $field = $f['field'];
            $cell = $row[$index];
            if(is_object($cell)){
                $value = $cell->getPlainText();
            }else{
                $value = $cell;
            }

            $func = 'convert_'.$field;
           
            if(empty($value)){
                if(isset($f['must']) && $f['must'] === true){
                    $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']没有填写!';
                    return 2;
                }
            }else{
                $add[$field] = trim($value);
                if(method_exists($this, $func)){  
                    $add[$field] = $this->$func($value,$add,$row);
                }
            }
        }
        return $this->add_data($add,$row_no);
    }
}