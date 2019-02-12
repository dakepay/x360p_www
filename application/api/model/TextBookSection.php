<?php
namespace app\api\model;


class TextbookSection extends Base
{


    public function batchBookSection(array $section_list)
    {
        if (!is_array($section_list)){
            return $this->user_error('params error');
        }

        foreach ($section_list as $book_section){
            $result = $this->addOneBookSection($book_section);
            if (false === $result){
                return $this->user_error($this->getError());
            }
        }

        return true;
    }

    public function addOneBookSection($input)
    {

        if (!isset($input['section_title']) || $input['section_title'] == ''){
            return $this->input_param_error('section_title');
        }
        if (!isset($input['sort'])){
            return $this->input_param_error('sort');
        }
        if (!isset($input['tb_id'])){
            return $this->input_param_error('tb_id');
        }

        $mTextBook = new Textbook();
        $m_test_book = $mTextBook->get($input['tb_id']);
        if (empty($m_test_book)){
            return $this->user_error('教材不存在');
        }

        $w['sort'] = $input['sort'];
        $w['tb_id'] = $input['tb_id'];
        $section_sort = $this->where($w)->find();
        if (!empty($section_sort)){
            return $this->user_error('教材章节序号存在');
        }

        $result = $this->allowField(true)->isUpdate(false)->save($input);
        if (false == $result){
            return $this->sql_add_error('textbook_section');
        }

        return true;
    }


    /**
     * 获取教材和章节信息
     * @param $id
     * @return null
     */
    public function getLastTbs($sid,$sl_id){
        $last_tbs = null;

//        $student_lesson_info = get_student_lesson_info($sl_id);
//        if (empty($student_lesson_info)){
//            return $last_tbs;
//        }
//
//        $mStudentAttendance = new StudentAttendance();
//        $w_sa = [
//            'sid' => $sid,
//            'lid' => $student_lesson_info['lid'],
//        ];
//        $student_attendance = $mStudentAttendance->where($w_sa)->order('int_day','desc')->find();

        $mStudentAttendance = new StudentAttendance();
        $w_sa = [
            'sa.sid' => $sid,
            'slh.sl_id' => $sl_id,
        ];
        $student_attendance = $mStudentAttendance
            ->alias('sa')
            ->join('student_lesson_hour slh','sa.catt_id = slh.catt_id','left')
            ->where($w_sa)
            ->order('sa.int_day','desc')
            ->find();
        if (!empty($student_attendance)){
            $class_attendance = get_class_attendance_info($student_attendance['catt_id']);
            if ($class_attendance['tb_id'] > 0 && $class_attendance['tbs_id'] > 0){
                $last_tbs = get_last_tbs_info($class_attendance['tbs_id']);
            }
        }

        return $last_tbs;
    }


}