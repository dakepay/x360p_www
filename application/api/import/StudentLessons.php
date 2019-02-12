<?php
namespace app\api\import;

use app\api\model\StudentLesson;
use app\common\exception\FailResult;
use app\common\Import;
use think\Exception;

class StudentLessons extends Import{
	protected $res = 'student_lesson';
	protected $start_row_index = 3;
    protected $pagesize = 20;

    protected $fields = [
        ['field'=>'sid','name'=>'学员名称','must'=>true],
        ['field'=>'sj_ids','name'=>'适用科目','must'=>true],
        ['field'=>'lesson_hours','name'=>'导入课时数量', 'must' => true],
        ['field'=>'lid','name'=>'课程名称'],
        ['field'=>'unit_lesson_hour_amount','name'=>'课时单价'],

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

        $m_sl = new StudentLesson();
        try {
            $rs = $m_sl->importStudentLesson($data);
            if($rs === false) throw new FailResult($m_sl->getErrorMsg());
        } catch(Exception $e) {
            $this->import_log[] = '第'.$row_no.'行的数据写入数据库失败:'.$e->getMessage();
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
                    try{
                        $add[$field] = $this->$func($value,$add,$row);
                    } catch(\Exception $e) {
                        $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']有问题：'.$e->getMessage().'!';
                        return 2;
                    }
                }
            }
        }
        return $this->add_data($add,$row_no);
    }

    public function convert_sid($student_name, &$add, $row)
    {
        $m_student = m('student');
        $num = $m_student->where('student_name', trim($student_name))->count();
        if($num == 0) throw new FailResult($student_name.'不存在');
        if($num > 1) throw new FailResult($student_name.'有同名的，请另行处理');

        $student = $m_student->where('student_name', trim($student_name))->field('sid')->cache(2)->find();

        return $student['sid'];
    }

    public function convert_sj_ids($subject_name, &$add, &$row)
    {
        $sj_ids = [];
        $m_subject = m('subject');
        if(strpos($subject_name,',') === false) {
            $subject = $m_subject->where('subject_name', trim($subject_name))->cache(2)->field('sj_id')->find();
            if (empty($subject)) exception($subject_name . '科目不存在');
            array_push($sj_ids,$subject['sj_id']);
        }else{
            $subject_names = explode(',',$subject_name);
            foreach($subject_names as $sn){
                $subject = $m_subject->where('subject_name',trim($sn))->cache(2)->field('sj_id')->find();
                if(!$subject){
                    exception($subject_name . '科目不存在');
                }
                array_push($sj_ids,$subject['sj_id']);
            }
        }
        $sj_ids = implode(',',$sj_ids);
        return $sj_ids;
    }

    public function convert_lid($lesson_name, &$add, $row)
    {
        $m_lesson = m('lesson');
        $lesson = $m_lesson->where('lesson_name', trim($lesson_name))->cache(2)->field('lid')->find();
        if(empty($lesson)) exception($lesson_name.'课程不存在');
        return $lesson['lid'];
    }

}