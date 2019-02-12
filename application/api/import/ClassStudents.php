<?php
namespace app\api\import;

use app\common\Import;
use app\api\model\ClassStudent;

class ClassStudents extends Import
{
	protected $start_row_index = 3;

	protected $fields = [
        ['field'=>'student_name','name'=>'学员姓名','must'=>true],
        ['field'=>'first_tel','name'=>'联系电话','must'=>true],
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

    protected function add_data($data,$row_no)
    {
    	$w['student_name'] = $data['student_name'];
    	$w['first_tel']    = $data['first_tel'];
    	$student = m('student')->where($w)->find();
    	if(empty($student)){
    		$this->import_log[] = '第'.$row_no.'行的学员--'.$data['student_name'].'不存在学员档案中';
    		return 2;
    	}

    	$cid = input('cid',0);

    	$data['sid'] = $student->sid;
    	$data['status'] = $student->status;

    	$class_student = m('class_student')->where(['cid'=>$cid,'sid'=>$data['sid']])->find();
    	if(!empty($class_student)){
            $this->import_log[] = '第'.$row_no.'行的学员--'.$data['student_name'].'已经存在班级中';
    		return 1;
    	}

    	$data['in_time'] = time();
    	$data['in_way'] = ClassStudent::IN_WAY_DSS;

        
    	$class = m('classes')->where('cid',$cid)->find();
        $class_info = $class->toArray();
    	$data = array_copy($data,$class_info,['cid','og_id','bid']);

    	$w_l['sid'] = $data['sid'];
    	$w_l['lid'] = $class_info['lid'];
    	$data['sl_id'] = m('student_lesson')->where($w_l)->value('sl_id');

    	$model = new ClassStudent;
    	$ret = $model->data([])->allowField(true)->isUpdate(false)->save($data);

        if(false === $ret){
        	$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败：'.m('class_student')->getError();
			return 2;
        }

        return 0;


    }






}