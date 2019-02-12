<?php
/**
 * Author: luo
 * Time: 2018-04-24 16:58
**/
namespace app\api\import;

use app\api\model\ClassStudent;
use app\api\model\Dictionary;
use app\api\model\StudentExam;
use app\api\model\StudentExamScore;
use app\common\Import;
use util\excel;

class StudentExamScores extends Import
{
    protected $res = 'student_exam_score';
    protected $start_row_index = 2;
    protected $pagesize = 20;
    protected $return_list = [];
    protected $class_sids = [];

    protected $fields = [];

    protected $default_fields =  [
        ['field'=>'student_name','name'=>'学生姓名','must'=>true],
        ['field'=>'remark','name'=>'备注'],
    ];

    //通过第一列设置字段关系
    protected function set_fields($first_row)
    {
        $se_id = input('se_id', 0);
        if($se_id <= 0) return '参数se_id错误';

        if(!empty($this->fields)) return true;

        $exam = StudentExam::get($se_id);
        if(empty($exam['exam_subject_dids'])) return '考试没有科目';
        $subject_dids = is_string($exam['exam_subject_dids']) ? explode(',', $exam['exam_subject_dids']) : $exam['exam_subject_dids'];
        $subjects = (new Dictionary())->where('did', 'in', $subject_dids)->select();
        if(empty($subjects)) return '考试字典科目不存在';
        $subjects = collection($subjects)->toArray();
        $subjects_name = array_column($subjects, 'title');

        $default_fields_name = array_column($this->default_fields, 'name');
        foreach($first_row as $index => $row_value) {
            $key = array_search(trim($row_value), $default_fields_name);
            $default_field = [];
            if($key !== false) {
                $default_field = $this->default_fields[$key];
            }

            if(!empty($default_field)) {
                $field = $default_field['field'];
            } else {
                $key = array_search(trim($row_value), $subjects_name);
                if($key === false) return $row_value.'导入列科目与考试科目名称不对应';
                $subject = $subjects[$key];
                $field = 'score_' . $subject['did'];
            }

            $this->fields[$index]  = [
                'field' => $field,
                'name' => $row_value,
                'must' => $default_field['must'] ?? false,
            ];
        }

        return true;
    }

    protected function get_fields()
    {
        return $this->fields;
    }

    public function import(){
        $xls_file = $this->params['local_file'];
        $excel = new excel();
        $xcount = $excel->getExcelCount($xls_file,$this->start_row_index);

        $page       = isset($_GET['page'])?intval($_GET['page']):1;
        $pagesize   = isset($_GET['pagesize'])?intval($_GET['pagesize']):$this->pagesize;

        if($page == 1){
            if(!$this->check_import($xcount)){
                return false;
            };
        }

        $first_row = $excel->readAsRow($xls_file, 1, 1);
        $rs = $this->set_fields($first_row[0]);
        if($rs !== true) {
            exception($rs);
        }

        $start_row = ($page - 1)*$pagesize + $this->start_row_index;

        $end_row = $start_row + $pagesize;

        $rows = $excel->readAsRow($xls_file,$start_row,$pagesize);

        $success = 0;
        $repeat  = 0;
        $failure = 0;

        if(count($rows) > 0){
            $row_no = $start_row;
            foreach($rows as $row){
                $result = $this->import_row($row,$row_no);
                if($result === 0){
                    $success++;
                }elseif($result === 1){
                    $repeat++;
                }else{
                    $failure++;
                }
                $row_no++;
            }
        }

        $ret['success'] = $success;
        $ret['repeat']  = $repeat;
        $ret['failure'] = $failure;
        $ret['next'] = 0;
        $ret['log']     = $this->import_log;
        $ret['list'] = array_values($this->return_list);
        $ret['field_title'] = $this->fields;

        if($end_row > $xcount['rows']){
            $ret['deal'] = $xcount['rows'] - $this->start_row_index+1;
            $ret['next'] = 0;
        }else{
            $ret['next'] = $page + 1;
            $ret['deal'] = $end_row - $this->start_row_index+1;
        }

        return $ret;
    }

    protected function import_row(&$row,$row_no){
        $fields = $this->get_fields();
        $add = [];

        $this->set_return_list($row_no, [
            'msg' => '导入成功',
            'is_success' => 0,
            'import_data' => $row,
        ]);
        foreach($fields as $index => $f){
            $field = $f['field'];
            $cell = $row[$index];
            if(is_object($cell)){
                $value = $cell->getPlainText();
            }else{
                $value = $cell;
            }

            $func = 'convert_'.$field;

            if(is_null($value)){
                if(isset($f['must']) && $f['must'] === true){
                    $msg = '第'.$row_no.'行的['.$f['name'].']没有填写!';
                    $this->import_log[] = $msg;
                    $this->set_return_list($row_no, ['msg' => $msg]);
                    return 2;
                }
            }else{

                $add[$field] = trim($value);
                if(method_exists($this, $func)){
                    try {
                        $add[$field] = $this->$func($value, $add, $row);
                    } catch (\Exception $e) {
                        $msg = '第'.$row_no.'行的['.$f['name'].']' . $e->getMessage();
                        $this->import_log[] = $msg;
                        $this->set_return_list($row_no, ['msg' => $msg]);
                        return 2;
                    }
                }

                if(strpos($field, 'score_') !== false) {
                    $add['score_info'][] = [
                        'exam_subject_did' => str_replace('score_', '', $field),
                        'score' => (float)$value,
                    ];
                }
            }
        }
        $add['se_id'] = input('se_id');
        $add['cid'] = input('cid', 0);
        return $this->add_data($add,$row_no);
    }

    protected function set_return_list($row_no, $data)
    {
        $this->return_list[$row_no] = !empty($this->return_list[$row_no]) ?
            array_merge($this->return_list[$row_no], $data) : $data;
    }

    protected function convert_student_name($value)
    {
        $cid = input('cid', 0);
        if($cid > 0 && empty($this->class_sids)) {
            $class_sids = (new ClassStudent())->cache(1)->where('cid', $cid)->column('sid');
            $this->class_sids = array_unique($class_sids);
        }

        $m_student = m('Student');
        $list = $m_student->where('student_name', $value)->field('sid,student_name')->select();
        $list = collection($list)->toArray();
        if(count($list) > 1) {
            $sids = array_column($list, 'sid');
            foreach($sids as $sid) {
                if(in_array($sid, $this->class_sids)) {
                    return $sid;
                }
            }
            exception('存在同名的学生');
        }
        if(count($list) < 1) exception('学员不存在');

        $sid = $list[0]['sid'];

        if(!empty($this->class_sids)) {
            if(!in_array($sid, $this->class_sids)) exception('学员不在此班级');
        }

        return $sid;
    }

    /**
     * 添加数据到数据库
     * @param [type] $data   [description]
     * @param [type] $row_no [description]
     * @return  0 成功
     * @return  2 失败
     * @return  1 重复
     */
    protected function add_data($data,$row_no){

        $data['sid'] = $data['student_name'];
        $data['se_id'] = input('se_id');

        /** @var StudentExamScore $m_ses */
        $m_ses = m('StudentExamScore');
        $rs = $m_ses->addOneScore($data);
        if (!$rs) {
            $msg = '第' . $row_no . '行的学员数据写入数据库失败:' . $m_ses->getError();
            $this->set_return_list($row_no, ['msg' => $msg]);
            $this->import_log[] = $msg;
            return 2;
        }

        $this->set_return_list($row_no, ['is_success' => 1]);
        return 0;
    }
}