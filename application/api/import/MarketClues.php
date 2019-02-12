<?php
namespace app\api\import;

use app\api\model\Dictionary;
use app\api\model\MarketChannel;
use app\api\model\MarketClue;
use app\api\model\Customer;
use app\api\model\Student;
use app\api\model\MarketClueLog;
use app\common\Import;

class MarketClues extends Import{
	protected $res = 'market_clue';
    protected $start_row_index = 3;

    protected $did_map = [];

    protected $fields = [
        ['field'=>'name','name'=>'姓名','must'=>true],
        ['field'=>'tel','name'=>'电话','must'=>true],
        ['field'=>'email','name'=>'邮箱'],
        ['field'=>'sex','name'=>'性别'],
        ['field'=>'birth_time','name'=>'出生日期'],
        ['field'=>'remark','name'=>'备注'],
        ['field'=>'school_grade','name'=>'年级'],

        ['field'=>'from_did','name'=>'招生来源'],
        ['field'=>'get_time','name'=>'获取时间'],
    ];

    protected $pagesize = 20;

    public function __init(){

	}

    protected function init_did_map()
    {
        if(!empty($this->did_map)){
            return;
        }
        $w['pid']  =  5;
        $w['og_id'] = gvar('og_id');
        $from_dids = m('dictionary')->field('did,name')->where($w)->select();
        foreach ($from_dids as $k) {
            $key = md5($k['name']);
            $this->did_map[$key] = $k['did'];
        }
    }

    protected function get_fields(){
        return $this->fields;
    }

    protected function import_row(&$row,$row_no){

        $fields = $this->get_fields();

        $add = [];

        foreach($fields as $index=>$f){
            $field = $f['field'];
            $name = $f['name'];
            $cell = $row[$index];
            if(is_object($cell)){
                $value = $cell->getPlainText();
            } else {
                $value = $cell;
            }

            $func = 'convert_'.$field;

            if(empty($value)){
                if(isset($f['must']) && $f['must'] === true){
                    $this->import_log[] = '第'.$row_no.'行的【'.$name.'】没有填写';
                    return 2;
                }
            }else{
                $add[$field] = trim($value);
                if($field == 'tel'){
                    if(!is_mobile($value)){
                        $this->import_log[] = '第'.$row_no.'行的【'.$name.'】格式不正确';
                        return 2; 
                    }
                    // 检测重复
                    try{
                        $add[$field] = $this->check_tel_repeat($value);
                    }catch(\Exception $e){
                        $this->import_log[] = '第'.$row_no.'行的【'.$name.'】'.$e->getMessage();
                        return 1; 
                    }
                }  
                
                if(method_exists($this,$func)){
                    try{
                        $add[$field] = $this->$func($value);
                    }catch(\Exception $e){
                        $this->import_log[] = '第'.$row_no.'行的【'.$name.'】'.$e->getMessage();
                        return 2; 
                    }
                }
            }
        }

        if(!empty($add['birth_time'])) {
            list($year,$month,$day) = explode('-',$add['birth_time']);

            $add['birth_year'] = intval($year);
            $add['birth_month'] = intval($month);
            $add['birth_day']  = intval($day);

            if($month == 0){
                $month = date('n',time());
            }
            if($day == 0){
                $day = date('j',time());
            }
            $add['birth_time'] = mktime(0,0,0,$month,$day,$year);
        }

        return $this->add_data($add,$row_no);
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

        $data['mc_id'] = input('mc_id', 0);
        if(!isset($data['from_did']) || $data['from_did'] == 0){
            $data['from_did'] = $this->get_from_did($data['mc_id']);
        }
        if(!isset($data['get_time'])){
            $data['get_time'] = time();
        }
        $m_mc = new MarketClue;
        // $rs = $m_mc->addClue($data);
        $rs = $m_mc->data([])->allowField(true)->isUpdate(false)->save($data);
        if(!$rs){
            $this->import_log[] = '第'.$row_no.'行的数据写入数据库失败:'.$m_mc->getError();
            return 2;
        }else{
            if(isset($data['mc_id'])) {
                MarketClue::UpdateNumOfChannel($data['mc_id']);
            }
            // 添加一条客户导入日志
            MarketClueLog::addMarketClueImportLog($m_mc->mcl_id);
        }

        return 0;

    }

    protected function convert_email($value)
    {
        if(!is_email($value)) exception('格式不正确');
        return $value;
    }

    protected function check_tel_repeat($value)
    {
        $mMarketClue = new MarketClue;
        $market = $mMarketClue->where('tel',$value)->find();
        if(!empty($market)) exception($value.'已存在市场名单中，疑似重复名单');

        $mCustomer = new Customer;
        $customer = $mCustomer->where('first_tel',$value)->find();
        if(!empty($customer))  exception($value.'已存在客户名单中，疑似重复名单');

        $mStudent = new Student;
        $student = $mStudent->where('first_tel',$value)->find();
        if(!empty($student)) exception($value.'已存在学员名单中，疑似重复名单');
 
        return $value;
    }

    protected function convert_from_did($value)
    {
        $this->init_did_map();
        $value = trim($value);
        if(empty($value)){
            return 0;
        }
        $key = md5($value);
        if(key_exists($key,$this->did_map)){
            return $this->did_map[$key];
        }

        $pid = 5;
        $did = Dictionary::createDictItem($value,$pid);
        $this->did_map[$key] = $did;
        return $did;
    }

    protected function convert_get_time($value)
    {
        $value = intval(($value - 25569) * 3600 * 24); //转换成1970年以来的秒数
        $date =  date('Y-m-d',$value); 
        if(!is_date_format($date)) exception('格式不正确');
        return $value;
    }


    protected function convert_sex($value)
    {
        $map = ['男' => 1, '女' => 2];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return 0;
    }

    protected function convert_birth_time($value)
    {
       return dage_to_date($value);
    }

    protected function convert_remark($value)
    {
        $len = mb_strlen($value,'utf-8');
        return $len >= 255 ? mb_substr($value,0,255,'utf-8') : mb_substr($value,0,$len,'utf-8');
    }

    protected function get_from_did($mc_id)
    {
        static $cache = [];
        if(isset($cache[$mc_id])){
            return $cache[$mc_id];
        }
        $did = 0;
        $mc_info = get_mc_info($mc_id);
        if($mc_info){
            $did = $mc_info['from_did'];    
        }
        $cache[$mc_id] = $did;
        return $did;
    }

    protected function convert_school_grade($value)
    {
        if(empty($value)) return '';
        $grade = (new Dictionary())->where('pid', 11)->where('title', $value)->find();
        if(empty($grade)) return '';

        return $grade['name'];
    }


}