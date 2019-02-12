<?php
namespace app\api\import;

use app\common\Import;
use app\commom\exception\FailResult;
use app\think\Exception;
use app\api\model\Student;


class StudentMoneys extends Import
{
	protected $start_row_index = 3;
	protected $pagesize = 20;

	protected $fields = [
        ['field'=>'sid','name'=>'学生姓名','must'=>true],
        ['field'=>'initial_money','name'=>'期初金额','must'=>true],
        ['field'=>'remark','name'=>'备注'],
	];


	protected function get_fields()
	{
		return $this->fields;
	}
    
    /**
     * 将学员姓名转化成学员Id
     * @param  [type] $student_name [description]
     * @param  [type] &$add         [description]
     * @param  [type] $row          [description]
     * @return [type]               [description]
     */
	protected function convert_sid($student_name, &$add,$row)
	{
        $model = model('student');
        $w['student_name'] = trim($student_name);
        $num = $model->where($w)->count();
        if($num == 0){
            throw new FailResult($studnet_name.'不存在');
        }elseif($num > 1){
        	throw new FailResult($student_name.'有同名的，请另行处理');
        }

        $student = $model->where($w)->field('sid')->cache(2)->find();

        return $student['sid'];

	}


    /**
     * 导入数据
     * @param  [type] &$row   [description]
     * @param  [type] $row_no [description]
     * @return [type]         [description]
     */
	protected function import_row(&$row,$row_no)
	{
        $fields = $this->get_fields();

        $add = [];

        foreach($fields as $index => $f){
        	$field = $f['field'];
        	$cell = $row[$index];
        	if(is_object($cell)){
        		$value = $cell->getPlainText();
        	}else{
        		$value = $cell;
        	}

        	$func = 'convert_'.$field;

        	if(empty($value)){
        		if(isset($f['must']) && $f['must']===true){
        			$this->import_log[] = '第'.$row_no.'行的['.$f['name'].']没有填写！';
        			return 2;
        		}
        	}else{
        		$add[$field] = trim($value);
        		if(method_exists($this,$func)){
        			try{

        				$add[$field] = $this->$func($value,$add,$row);

        			}catch(Exception $e){
        				$this->import_log[] = '第'.$row_no.'行的['.$f['name'].']有问题：'.$e->getMessage().'!';
        				return 2;
        			}
        		}
        	}
        }
        return $this->add_data($add,$row_no);
	}

    
    /**
     * 添加数据到数据库
     * @param [type] $data   [description]
     * @param [type] $row_no [description]
     */
	protected function add_data($data,$row_no)
	{
        $model = new Student;
        try{
        	$res = $model->importMoney($data);
        	if($res === false){
        		throw new FailResult($model->getErrorMsg());
        	}
        }catch(Exception $e){
        	$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败：'.$e->getMessage();
        	return 2;
        }
        return 0;
	}






}