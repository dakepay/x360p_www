<?php
namespace app\api\import;

use app\common\Import;
use think\Exception;

class Lessons extends Import{
	protected $res = 'lesson';
	protected $start_row_index = 3;
    protected $pagesize = 20;

    protected $all_branchs = [];
    protected $all_subjects = [];

    protected $fields = [
        ['field'=>'lesson_name','name'=>'课程名称','must'=>true],
        ['field'=>'sj_ids','name'=>'适用科目','must'=>true],
        ['field'=>'bids','name'=>'授权校区','must'=>true],
        ['field'=>'price_type','name'=>'计费方式', 'must' => true],
        ['field'=>'lesson_type','name'=>'授课方式'],
        ['field'=>'unit_price','name'=>'单价'],
        ['field'=>'unit_lesson_minutes','name'=>'单次课时长'],
        ['field'=>'unit_lesson_hours','name'=>'单次课扣课时数'],
        ['field'=>'is_term','name'=>'收费方式'],
        ['field'=>'lesson_nums','name'=>'课时数'],
        ['field'=>'is_package','name'=>'是否课时包']
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
		
		$w['lesson_name'] = $data['lesson_name'];

		if(is_string($data['bids'])){
            $bids = explode(',',$data['bids']);
        }

        foreach ($bids as $bid) {
            $w[] = ['exp', "find_in_set({$bid},bids)"];
            $exists_lesson = m('lesson')->where($w)->find();
            $branch_name = get_branch_name($bid);
            if($exists_lesson){
                $this->import_log[] = '第'.$row_no.'行的'. $data['lesson_name'] .'课程在'.$branch_name.'已经存在!';
                return 1;
            }
        }

		$data['public_content'] = '';
        if(!isset($data['is_term']) || $data['is_term'] != 1) {
            $data['lesson_nums'] = 1;
        }
        if(!isset($data['lesson_nums'])){
            $data['lesson_nums'] = 0;
        }
        $data['unit_price'] = isset($data['unit_price']) ? $data['unit_price'] : 0;
        $data['sale_price'] = $data['unit_price'] * $data['lesson_nums'];

        if(!isset($data['sj_id']) && !empty($data['sj_ids'])){
            $arr_sj_ids = explode(',',$data['sj_ids']);
            $data['sj_id'] = $arr_sj_ids[0];
        }

        if(!empty($data['bids'])){
            $arr_bids = explode(',',$data['bids']);
            $c1 = count($arr_bids);
            $c2 = count($this->all_branchs);

            if($c1 < $c2){
                $data['is_public'] = 0;
            }
        }

        $lid = m('lesson')->addLesson($data);

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
                    } catch(\Exception $e) {
                        $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']有问题：'.$e->getMessage().'!';
                        return 2;
                    }
                }
            }
        }

        return $this->add_data($add,$row_no);
    }

    public function convert_sj_id($subject_name, &$add, &$row)
    {
        $m_subject = m('subject');
        $subject = $m_subject->where('subject_name', trim($subject_name))->cache(2)->field('sj_id')->find();
        if(empty($subject)) exception($subject_name.'科目不存在');
        return $subject['sj_id'];
    }

    /**
     * 初始化校区
     */
    protected function init_branch(){
	    if(empty($this->all_branchs)){
	        $w['og_id'] = gvar('og_id');
	        $branch_list = get_table_list('branch',$w);
	        foreach($branch_list as $b){
	            $this->all_branchs[$b['bid']] = $b;
            }
        }
    }

    /**
     * 初始化科目
     */
    protected function init_subject(){
        if(empty($this->all_subjects)){
            $w['og_id'] = gvar('og_id');
            $subject_list = get_table_list('subject',$w);
            foreach($subject_list as $sj){
                $this->all_subjects[$sj['sj_id']] = $sj;
            }
        }
    }

    /**
     * 转化多科目
     * @param $subject_name
     * @param $add
     * @param $row
     * @return string
     */
    public function convert_sj_ids($subject_name, &$add, &$row)
    {
        $this->init_subject();
        $subject_name = str_replace("，",",",$subject_name);
        if($subject_name == '所有科目' || $subject_name == '所有'){
            return implode(',',array_keys($this->all_subjects));
        }
        $sj_ids = [];
        if(strpos($subject_name,',') === false) {
            foreach ($this->all_subjects as $k => $sj) {
                if ($sj['subject_name'] == $subject_name) {
                    array_push($sj_ids,$k);
                    break;
                }
            }
            if(empty($sj_ids)){
                exception($subject_name.'科目不存在');
            }
        }else{
            $subject_names = explode(',',$subject_name);
            foreach($subject_names as $sn){
                foreach ($this->all_subjects as $k => $s) {
                    if ($s['subject_name'] == $sn) {
                        array_push($sj_ids,$k);
                        break;
                    }
                }
            }
        }
        $sj_ids = implode(',',$sj_ids);
        return $sj_ids;
    }

    public function convert_bids($branch_name, &$add, &$row)
    {
        $this->init_branch();
        if($branch_name == '所有校区' || $branch_name == '所有'){
            return implode(',',array_keys($this->all_branchs));
        }
        $bids = [];
        if(strpos($branch_name,',') === false) {
            foreach ($this->all_branchs as $k => $b) {
                if ($b['branch_name'] == $branch_name || $b['branch_name'] == $branch_name) {
                    array_push($bids,$k);
                    break;
                }
            }
        }else{
            $branch_names = explode(',',$branch_name);
            foreach($branch_names as $bn){
                foreach ($this->all_branchs as $k => $b) {
                    if ($b['branch_name'] == $bn || $b['branch_name'] == $bn) {
                        array_push($bids,$k);
                        break;
                    }
                }
            }
        }
        $bids = implode(',',$bids);
        return $bids;
    }

    public function convert_lesson_type($lesson_type, &$add, &$row)
    {
        $lesson_type = str_replace(['一对一','一对多'],['1对1','1对多'],$lesson_type);

        $lesson_type_arr = [
            0 => '班课',
            1 => '1对1',
            2 => '1对多',
            3 => '研学旅行团',
        ];

        $key = array_search($lesson_type, $lesson_type_arr);
        if($key === false){
            return 0;
        }

        return $key;
    }

    public function convert_price_type($price_type, &$add, $row)
    {
        $price_type_arr = [
            2 => '按课时',
            3 => '按时间',
        ];

        $key = array_search($price_type, $price_type_arr);
        if($key === false) exception(sprintf('%s 不在规定的方式中，须在这些方式内:%s', $price_type, implode(',', $price_type_arr)));

        return $key;
    }

    public function convert_is_term($type_name, &$add, $row)
    {
        $is_term_arr = [
            0 => '按课时',
            1 => '按期收费'
        ];

        $key = array_search($type_name, $is_term_arr);
        if($key === false) exception(sprintf('%s 不在规定的方式中，须在这些方式内:%s', $type_name, implode(',', $is_term_arr)));

        return $key;
    }

    public function convert_is_package($is_text,&$add,$row){
	    $is_package = 0;
	    if($is_text == '是'){
	        $is_package = 1;
        }
        return $is_package;
    }

}