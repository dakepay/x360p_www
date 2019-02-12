<?php
namespace app\api\import;

use app\api\model\Customer;
use app\common\Import;
use app\api\model\PublicSchool;
use app\api\model\Dictionary;
use app\api\model\Employee;
use app\api\model\MarketClue;
use app\api\model\Student;
use app\api\model\CustomerLog;

class Customers extends Import{
	protected $res = 'customer';
	protected $start_row_index = 3;
    protected $pagesize = 20;

    protected $did_map = [];

    protected $fields = [
        ['field'=>'bid','name'=>'校区','must'=>true],
        ['field'=>'name','name'=>'学员姓名','must'=>true],
        ['field'=>'nick_name','name'=>'昵称'],
        ['field'=>'sex','name'=>'性别'],
        ['field'=>'birth_time','name'=>'出生日期'],
        ['field'=>'school_grade','name'=>'学校年级'],
        ['field'=>'school_class','name'=>'学校班级'],
        ['field'=>'school_id','name'=>'学校'],
        ['field'=>'first_tel','name'=>'联系电话', 'must'=>true],
        ['field'=>'first_family_name','name'=>'第一亲属姓名'],
        ['field'=>'first_family_rel','name'=>'第一亲属关系'],
        ['field'=>'second_family_name','name'=>'第二亲属姓名'],
        ['field'=>'second_family_rel','name'=>'第二亲属关系'],
        ['field'=>'second_tel','name'=>'第二联系电话'],
        ['field'=>'content','name'=>'最后联系内容'],

        ['field'=>'mc_id','name'=>'市场渠道'],
        ['field'=>'from_did','name'=>'招生来源'],
        ['field'=>'follow_eid','name'=>'主责任人'],
        ['field'=>'home_address','name'=>'家庭地址'],
        ['field'=>'intention_level','name'=>'意向级别'],
        ['field'=>'get_time','name'=>'获取时间'],
    ];

    public function __init(){

	}

    protected function init_did_map(){
        if(!empty($this->did_map)){
            return;
        }
        $w['pid'] = 5;
        $w['og_id'] = gvar('og_id');
        $from_dids = m('dictionary')->field('did,name')->where($w)->select();
        foreach($from_dids as $k){
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
            if(!isset($row[$index])){
                continue;
            }
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

                if($field == 'first_tel'){
                    if(!is_mobile($value)){
                        $this->import_log[] = '第'.$row_no.'行的【'.$name.'】格式不正确';
                        return 2;
                    }
                    //排除重复
                    try{
                        $add['first_tel'] = $this->check_first_tel_repeat($value);
                    }catch(\Exception $e){
                        $this->import_log[] = '第'.$row_no.'行的【'.$name.'】'.$e->getMessage();
                        return 1; 
                    }
                }

                $add[$field] = trim($value);
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

        if(isset($add['birth_time'])) {
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

        $config = user_config('params.customer');
        if($config['must_from_did'] == 1){
            if(empty($data['from_did'])){
                $this->import_log[] = '第'.$row_no.'行的【招生来源】没有填写';
                return 2;
            }
        }
        if($config['must_intention_level'] == 1){
            if(empty($data['intention_level'])){
                $this->import_log[] = '第'.$row_no.'行的【意向级别】没有填写';
                return 2;
            }
        }
	    
        $m_customer = m('Customer');
		if(!empty($data['follow_eid'])) {
		    $data['assign_time'] = time();
        }

        if(isset($data['mc_id']) && !isset($data['from_did'])){
            $data['from_did'] = $this->get_from_did($data['mc_id']);
        }

        if(!isset($data['get_time'])){
            $data['get_time'] = date('Y-m-d',time());
        }

		$rs = $m_customer->data([])->allowField(true)->isUpdate(false)->save($data);
		if(!$rs){
			$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败:'.m('customer')->getError();
			return 2;
		}else{
            // 添加一条客户导入日志
            CustomerLog::addCustomerImportLog($m_customer->cu_id);
        }

        if(!empty($data['content'])) {
            $cu_id = $m_customer->getAttr('cu_id');
            $m_cfu = m('CustomerFollowUp');
            $rs = $m_cfu->addOneFollowUp(['cu_id' => $cu_id, 'content' => $data['content']]);
            if(!$rs){
                $this->import_log[] = '第'.$row_no.'行的数据跟进记录失败:'.$m_cfu->getError();
                return 2;
            }
        }

		return 0;

	}

    protected function check_first_tel_repeat($value)
    {
        $exist = (new Customer)->where('first_tel',$value)->find();
        if(!empty($exist)) exception('存在客户名单中');

        $exist = (new Student)->where('first_tel',$value)->find();
        if(!empty($exist)) exception('存在学员名单中');

        $exist = (new MarketClue)->where('tel',$value)->find();
        if(!empty($exist)) exception('存在市场名单中');
        
        return $value;
    }

    protected function convert_second_tel($value)
    {
        if(!is_mobile($value)) exception('格式不正确');
        return $value;
    }

    /**
     * 查找或 创建招生来源
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
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
        // 招生来源 在字典表中不存在
        $pid = 5;
        $did = Dictionary::createDictItem($value,$pid);
        // 将新增的来源写入 $did_map数组中，便于下一次查询
        $this->did_map[$key] = $did;
        return $did;
    }

    protected function convert_bid($value)
    {
        $value = explode('|', $value);
        $bids = model('Branch')->where('short_name|branch_name', 'in', $value)->limit(1)->column('bid');
        if (!$bids) exception('所属校区信息错误,可能不存在此校区');
        return $bids[0];
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

    protected function convert_school_id($value)
    {
        return PublicSchool::findOrCreate($value);
    }

    protected function convert_first_family_rel($value)
    {
        
	return get_family_rel_id($value);
    }

    protected function convert_school_grade($value)
    {
        if(empty($value)) return '';
        $grade = (new Dictionary())->where('pid', 11)->where('title', $value)->find();
        if(empty($grade)) return '';

        return $grade['name'];
    }

    protected function convert_second_family_rel($value)
    {
    	return get_family_rel_id($value);
    }

    public function convert_follow_eid($ename)
    {
        $mEmployee = new Employee;
        $input['ename'] = $ename;
        $employee = $mEmployee->getSearchResult($input);
        if(empty($employee['list'])) exception($ename.'不存在');

        return $employee['list'][0]['eid'];
    }

   /* public function convert_follow_eid($ename)
    {
        $mEmployee = new Employee;
        $w['ename'] = $ename;
        $employee = $mEmployee->where($w)->find();
        print_r($employee);exit;
        if(empty($employee['list'])) exception($ename.'不存在');

        return $employee['eid'];
    }*/

    public function convert_home_address($value)
    {
        return trim($value);
    }


    public function convert_intention_level($value)
    {
        $map = ['0','1','2','3','4','5'];
        if(!in_array($value,$map)) exception('请填写0,1,2,3,4,5');
        return $value;
    }

    protected function convert_mc_id($value)
    {
        $value = trim($value);
        if(empty($value)){
            return 0;
        }
        $w['channel_name'] = $value;
        $w['og_id']        = gvar('og_id');
        $w['bid']          = request()->bid;
        $mc_id = model('MarketChannel')->where($w)->find();
        if(empty($mc_id)) exception($value.'不存在');
        return $mc_id->mc_id;
    }

    protected function convert_get_time($value)
    {
        $value = intval(($value - 25569) * 3600 * 24); //转换成1970年以来的秒数
        $date =  date('Y-m-d',$value); 
        if(!is_date_format($date)) exception('格式不正确');
        return $value;
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


}