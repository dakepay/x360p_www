<?php
namespace app\api\import;

use app\common\Import;
use think\Exception;

class LessonStandardFiles extends Import{
	protected $res = 'lesson_standard_file';
	protected $start_row_index = 3;
    protected $pagesize = 20;

    protected $fields = [
        ['field'=>'lid','name'=>'课程名称','must'=>true],
        ['field'=>'title','name'=>'标题','must'=>true],
        ['field'=>'csft_did','name'=>'课标类型','must'=>true],
        ['field'=>'sort','name'=>'排序'],
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

        $lid = m('LessonStandardFile')->addLessonStandardFile($data);

        if(!$lid){
			$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败:'.m('lesson')->getError();
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
                    } catch(Exception $e) {
                        $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']有问题：'.$e->getMessage().'!';
                        return 2;
                    }
                }
            }
        }
        return $this->add_data($add,$row_no);
    }

    public function convert_lid($lesson_name, &$add, &$row)
    {
        $m_lesson = m('lesson');
        $lesson = $m_lesson->where('lesson_name', trim($lesson_name))->cache(2)->field('lid')->find();
        if(empty($lesson)) exception($lesson_name.'不存在');
        return $lesson['lid'];
    }

    public function convert_csft_did($type_name, &$add, &$row)
    {
        $type_name = trim($type_name);
        if(empty($type_name)) return 0;
        $m_dictionary = m('dictionary');
        $dictionary = $m_dictionary->where('title', $type_name)->cache(20)->where('pid = 18')->field('did')->find();
        if(empty($dictionary)) exception($type_name.'不存在');

        return $dictionary['did'];
    }



}