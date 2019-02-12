<?php
namespace app\api\import;

use app\common\Import;
use think\Exception;
use app\api\model\ClassLog;

class Classes extends Import{
	protected $res = 'class';
	protected $start_row_index = 3;
    protected $pagesize = 20;

    protected $fields = [
        ['field'=>'class_name','name'=>'班级名称','must'=>true],
        ['field'=>'sj_id','name'=>'科目','must'=>true],
        ['field'=>'teach_eid','name'=>'老师','must'=>true],
        ['field'=>'cr_id','name'=>'教室','must'=>true],
        ['field'=>'plan_student_nums','name'=>'预招人数','must'=>true],
        ['field'=>'lesson_times','name'=>'上课次数'],
        ['field'=>'class_no','name'=>'班级编号'],
        ['field'=>'lid','name'=>'所属课程', 'must' => true],
        ['field'=>'grade','name'=>'年级'],
        ['field'=>'start_lesson_time','name'=>'开课日期'],
        ['field'=>'bid','name'=>'校区']
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
        if(!isset($data['bid']) || $data['bid'] == 0){
            $data['bid'] = request()->header('x-bid');
        }

		$w['class_name'] = $data['class_name'];
		$w['bid']        = $data['bid'];
		
		$exists_class = m('classes')->where($w)->find();
		if($exists_class){
			$this->import_log[] = '第'.$row_no.'行的'. $data['class_name'] .'已经存在!';
			return 1;
		}

		if($data['cr_id'] > 0){
		    $cr_info = get_classroom_info($data['cr_id']);
		    if($cr_info['bid'] != $data['bid']){
		        $w_cr['bid'] = $data['bid'];
		        $w_cr['room_name'] = $cr_info['room_name'];

		        $cr_info = get_classroom_info($w_cr);

		        if($cr_info){
		            $data['cr_id'] = $cr_info['cr_id'];
                }else{
		            $data['cr_id'] = 0;
                }
            }
        }

        $data['sg_id'] = 0;
		// $cid = m('classes')->createOneClass($data);
        $mClasses = model('classes'); 
        $rs = $mClasses->data([])->allowField(true)->isUpdate(false)->save($data);

		if(!$rs){
			$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败:'.$mClasses->getError();
			return 2;
		}else{
            // 添加一条班级导入记录
            ClassLog::addImportClassLog($mClasses->cid);
            
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
                    try{
                        $add[$field] = $this->$func($value,$add,$row);
                    } catch(Exception $e) {
                        $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']有问题：'.$e->getMessage().'!';
                        return 2;
                    }
                }
            }
        }

        return $this->add_data($add,$row_no);
    }

    public function convert_grade($value)
    {
        static $cache = [];
        if(isset($cache[$value])){
            return $cache[$value];
        }
        $name = 0;
        $w['pid'] = 11;
        $w['title'] = $value;
        $dict = m('dictionary')->where($w)->find();
        if(empty($dict)) exception('年级【'.$value.'】不存在');
        $name = $dict->name;
        $cache[$value] = $name;
        return $name;
    }



    public function convert_sj_id($subject_name, &$add, &$row)
    {
        $m_subject = m('subject');
        $subject = $m_subject->where('og_id', gvar('og_id'))->where('subject_name', trim($subject_name))->cache(2)->field('sj_id')->find();
        if(empty($subject)) exception($subject_name.'科目不存在');
        return $subject['sj_id'];
    }

    public function convert_teach_eid($ename, &$add, &$row)
    {
        $eid = 0;
        $w['ename'] = $ename;
        $w['og_id'] = gvar('og_id');
        $employee_info = get_employee_info($w);
        if($employee_info){
           $eid = $employee_info['eid'];
        }
        return $eid;
    }

    public function convert_cr_id($room_name, &$add, &$row)
    {
        $cr_id = 0;
        $w['room_name'] = $room_name;
        $w['og_id'] = gvar('og_id');
        $room_info = get_classroom_info($w);
        if($room_info){
            $cr_id = $room_info['cr_id'];
        }

        return $cr_id;
    }

    public function convert_lid($lesson_name, &$add, &$row)
    {
        $lid = 0;
        $w['lesson_name'] = trim($lesson_name);
        $w['og_id'] = gvar('og_id');

        $lesson_info = get_lesson_info($w);

        if($lesson_info){
            $lid = $lesson_info['lid'];
        }
        return $lid;
    }

    public function convert_start_lesson_time($value)
    {
        // UNIX_DATE = (EXCEL_DATE - 25569) * 86400
        $timestamp = ($value - 25569) * 86400;
        return date('Y-m-d', $timestamp);
    }

    public function convert_bid($value,&$add,&$row){
	    $bid = 0;
	    $w['short_name|branch_name'] = $value;
	    $w['og_id'] = gvar('og_id');
	    $binfo = get_branch_info($w);
        if($binfo){
            $bid = $binfo['bid'];
        }

        return $bid;
    }

}