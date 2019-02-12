<?php
namespace app\admapi\command;

use app\api\model\Student;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Log;
use util\excel;

/**
 * shell useage
 * php think franchisee services smtjy|tee ./import_service_log.log
 * Class ExImport
 * @package app\admapi\command
 */

class Franchisee extends Command
{
    protected $db_cfg = [];
    public $error_code;

    protected $data;

    protected $start_row_index = 2;
    protected $start_page = 1;
    protected $page_size = 100;

    public function __construct($name = null)
    {
        parent::__construct($name);

    }

    protected function configure()
    {
        $this->setName('franchisee')->setDescription('import franchisee data from excel file');
        $this->addArgument('func', Argument::REQUIRED, "The function name of the application");
        $this->addArgument('host',Argument::REQUIRED,"The name of the database");
        $this->addArgument('file',Argument::OPTIONAL,"The excel file path");
    }

    protected function execute(Input $input, Output $output)
    {
        $func = $input->getArgument('func');
        $host  = $input->getArgument('host');
        $file  = $input->getArgument('file');

        $db_cfg = db('database_config','db_center')->where('host',$host)->find();

        if(!$db_cfg){
            throw new \Exception('host对应的数据库配置不存在:' . $host);
        }

        $this->db_cfg = $db_cfg;

        $client = db('client','db_center')->where('host',$host)->find();

        $client['database'] = $db_cfg;

        config('database',$db_cfg);

        gvar('client',$client);
        gvar('og_id',$client['og_id']);

        /*合法的method:syncSchool|syncStudent|syncEmployee|syncRole*/
        $method = ucfirst($func);
        if (method_exists($this, $method)) {
            $this->$method($file);
        } else {
            printf('Error:parameter invalid,method not exists!-----%s, available parameter:Basic,ServiceRecord', $method);
        }
    }

    /**
     * 导入基本信息
     * @param string $file
     */
    public function Basic($file = ''){
        if($file == ''){
            $file = 'data/import/smart/basic.xls';
        }
        $real_file = ROOT_PATH.$file;

        if(!file_exists($real_file)){
            printf('file:%s does not exists!',$file);
            exit;
        }
        $sheet_index = 0;
        $xls_file = $real_file;
        $excel = new excel();
        $xcount = $excel->getExcelCount($xls_file,$this->start_row_index,$sheet_index);

        $data_count = $xcount['data_count'];
        $pagesize = 100;
        $total_page = ceil($data_count / $pagesize);

        $success = 0;
        $failure = 0;
        $total   = 0;
        for($page = 1;$page <= $total_page;$page++){
            $start_row = ($page - 1)*$pagesize + $this->start_row_index;
            $rows = $excel->readAsRow($xls_file,$start_row,$pagesize);
            if(!$rows || empty($rows)){
                break;
            }
            $row_no = $start_row;
            foreach($rows as $row){
                $result = $this->add_one_franchisee($row,$row_no);
                if($result){
                    $success++;
                }else{
                    $failure++;
                    exit;
                }
                $total++;
                $row_no++;


            }
        }
        printf("共处理%s条记录，成功%s条,失败%s条\n",$total,$success,$failure);
        echo('done!');
    }

    public function Services($folder = ''){
        if($folder == ''){
            $folder = 'data/import/smart/services';
        }
        $real_folder = ROOT_PATH.$folder;
        if(!is_dir($real_folder)){
            printf('folder:%s does not exists!',$folder);
            exit;
        }

        $handle = opendir($real_folder);

        $total = 0;
        $success = 0;
        $failure = 0;
        $files = [];
        while(($file = readdir($handle)) !== false){
            if($file != '.' && $file != '..'){
                if(strpos($file,'.xlsx') !== false){
                    array_push($files,$file);
                }
            }
        }

        $file_count = count($files);
        asort($files);

        printf("共有%s个文件需要处理\n",$file_count);

        foreach($files as $file){
            $result = $this->importServices($file,$real_folder);
            $total      += $result['total'];
            $success    += $result['success'];
            $failure    += $result['failure'];
        }

        printf("共处理%s条记录，成功%s条,失败%s条\n",$total,$success,$failure);
        echo('done!');

    }


    public function importServices($file,$folder){
        printf("开始处理文件:%s\n",$file);
        $this->start_row_index = 3;
        $sheet_index = 0;
        $xls_file = $folder.'/'.$file;
        $excel = new excel();
        $xcount = $excel->getExcelCount($xls_file,$this->start_row_index,$sheet_index);

        $data_count = $xcount['data_count'];
        $pagesize = 100;
        $total_page = ceil($data_count / $pagesize);

        $success = 0;
        $failure = 0;
        $total   = 0;
        for($page = 1;$page <= $total_page;$page++){
            $start_row = ($page - 1)*$pagesize + $this->start_row_index;
            $rows = $excel->readAsRow($xls_file,$start_row,$pagesize);
            if(!$rows || empty($rows)){
                break;
            }
            $row_no = $start_row;
            foreach($rows as $row){
                $result = $this->add_one_service($row,$row_no,$file);
                if($result){
                    $success++;
                }else{
                    $failure++;
                }
                $total++;
                $row_no++;


            }
        }
        printf("文件:%s处理完毕,共处理%s条记录，成功%s条,失败%s条\n",$file,$total,$success,$failure);
        echo("=========================================================================\n");
        return [
          'total'   => $total,
          'success' => $success,
          'failure' => $failure
        ];
    }

    /**
     * 添加一条服务记录
     * @param $row
     * @param $row_no
     */
    protected function add_one_service($row,$row_no,$file){

        $fc_id = $this->get_field_fc_id($row[0]);
        if(!$fc_id){
            printf("failure:第%s行的加盟商名称不对,%s,%s\n",$row_no,$row[0],$file);
            return false;
        }

        $fsr['fc_id'] = $fc_id;
        $fsr['og_id'] = 0;
        $fsr['fc_service_did'] = $this->get_field_fc_service_did($row[4]);
        $fsr['eid'] = $this->get_service_eid($row[3]);
        $fsr['content'] = $row[1];
        $fsr['int_day'] = $this->get_field_int_day($row[2]);
        $fsr['fsa_id']  = 0;


        $mFranchiseeServiceRecord = new \app\api\model\FranchiseeServiceRecord();

        $result = $mFranchiseeServiceRecord->addFranchiseeServiceRecord($fsr);

        if(!$result){
            printf("failure:第%s行的服务记录写入失败,%s,%s\n",$row_no,$row[0],$mFranchiseeServiceRecord->getLastSql());
            return false;
        }

        printf("success:第%s行的服务记录导入成功,%s,%s\n",$row_no,$fsr['int_day'],$row[0]);
        return true;

    }

    protected function get_field_fc_service_did($text){
        if(empty($text)){
            return 0;
        }
        static $map = [];
        if(isset($map[$text])){
            return $map[$text];
        }

        $did = get_dict_id($text,'fc_service');
        if(!$did){
            $did = add_dict_value($text,'fc_service');
        }
        $map[$text] = $did;
        return $did;
    }

    protected function get_field_fc_id($name){
        static $map = [];
        if(isset($map[$name])){
            return $map[$name];
        }

        $mFranchisee = new \app\api\model\Franchisee();

        $w['org_name'] = $name;
        $fc = $mFranchisee->where($w)->find();
        if(!$fc){
            $fc_id = 0;
        }else{
            $fc_id = $fc['fc_id'];
        }
        $map[$name] = $fc_id;
        return $fc_id;
    }


    /**
     * @param $row
     * @param $row_no
     */
    protected function add_one_franchisee($row,$row_no){

        foreach($row as $k=>$v){
            if(is_null($v)){
                $row[$k] = '';
            }
        }
        $fc['org_name'] = $row[5];
        $fc['mobile']   = $row[33];
        $fc['org_address'] = $row[32];
        $fc['province_id'] = $this->get_area_id($row[52]);

        $fc['status'] = $this->get_field_status($row[94]);
        $fc['address_did'] = $this->get_field_address_did($row[55]);
        $fc['decorate_fee'] = intval($row[39]);
        $fc['is_head_decorate'] = $this->get_field_is_head_decorate($row[75]);

        $fc['is_owner_change'] = $this->get_field_is_owner_change($row[54]);
        $fc['business_license'] = '';
        $fc['is_authorize_dispatch'] = 0;
        $fc['org_email'] = not_null($row[30]);
        $fc['open_int_day'] = $this->get_field_int_day($row[37]);
        $fc['service_eid'] = $this->get_service_eid($row[2]);
        $fc['sale_eid'] = $this->get_sign_eid($row[25]);

        $mFranchisee = new \app\api\model\Franchisee();

        $w_fc['org_name'] = $fc['org_name'];

        $ex_fc = $mFranchisee->where($w_fc)->find();
        if($ex_fc){
            $ex_fc->address_did = $fc['address_did'];
            $master_tel = $this->get_field_tel($row[65]);
            if(empty($master_tel)){
                $master_tel = $fc['mobile'];
            }
            $ex_fc->mobile = $master_tel;
            $ex_fc->save();
            printf("success:repeat:第%s行的加盟商重复，跳过！,%s\n",$row_no,$fc['org_name']);
            return true;
        }
        $result = $mFranchisee->addOneFranchisee($fc);

        if(!$result){
            printf("failure:第%s行创建加盟商失败,写入数据库失败,%s,%s\n",$row_no,$row[5],$mFranchisee->getLastSql());
            return false;
        }
        $fc_id = $mFranchisee->fc_id;

        //开始创建合同
        $mFranchiseeContract = new \app\api\model\FranchiseeContract();

        $fcc['fc_id'] = $fc_id;
        $fcc['og_id'] = 0;
        $fcc['contract_no'] = not_null($row[24]);
        $fcc['contract_start_int_day'] = $this->get_field_int_day($row[26]);
        $fcc['contract_end_int_day'] = $this->get_field_int_day($row[27]);

        $fcc['open_int_day'] = $fc['open_int_day'];
        $fcc['region_level'] = $this->get_field_region_level($row[41]);
        $fcc['join_fee1'] = floatval($row[34]);
        $fcc['join_fee2'] = floatval($row[35]);
        $fcc['join_fee3'] = floatval($row[36]);
        $fcc['join_fee4'] = floatval($row[71]);

        $fcc['contract_amount'] = $fcc['join_fee1']+$fcc['join_fee2']+$fcc['join_fee3']+$fcc['join_fee4'];
        $fcc['all_pay_int_day'] = $this->get_field_int_day($row[74]);
        $fcc['content'] = not_null($row[45]);
        $fcc['sign_eid'] = $this->get_sign_eid($row[25]);
        $fcc['service_eid'] = $fc['service_eid'];

        $result = $mFranchiseeContract->addOneFranchiseeContract($fcc);
        if(!$result){
            printf("failure:第%s行创建加盟商合同失败,写入数据库失败,%s,%s\n",$row_no,$row[5],$mFranchiseeContract->getLastSql());
            $mFranchisee->where('fc_id',$fc_id)->delete(true);
            return false;
        }

        $fcc_id = $mFranchiseeContract->fcc_id;

        //开始添加联系人
        $fcp['og_id'] = 0;
        $fcp['fc_id'] = $fc_id;
        $fcp['name'] = $row[23];
        $fcp['sex']  = $this->get_field_sex($row[78]);
        $fcp['mobile'] = $this->get_field_tel($row[28]);
        $fcp['email'] = not_null($row[57]);
        $fcp['id_card_no'] = $this->get_field_id_card_no($row[63]);
        $fcp['is_contract_sign'] = 1;

        $name_result = $this->parse_name($fcp['name']);

        if($name_result[0]){
            $fcp['name'] = $name_result[1];
            $fcp['is_partner'] = 1;
            $fcp['partner_percent'] = floatval($name_result[2]);
        }

        $mFranchiseePerson = new \app\api\model\FranchiseePerson();
        $fcp_id = $mFranchiseePerson->addFranchiseePerson($fcp);
        if(!$fcp_id){
            printf("failure:第%s行创建加盟商联系人,写入数据库失败,%s,%s\n",$row_no,$row[5],$mFranchiseePerson->getLastSql());
            $mFranchisee->where('fc_id',$fc_id)->delete(true);
            $mFranchiseeContract->where('fcc_id',$fcc_id)->delete(true);
            return false;
        }

        //添加其他联系人
        if(!empty($row[59])){       //合伙人
            $fcp1['og_id'] = 0;
            $fcp1['fc_id'] = $fc_id;
            $fcp1['name']  = $row[59];
            $fcp1['sex'] = $this->get_field_sex($row[79]);
            $fcp1['mobile'] = $this->get_field_tel($row[65]);
            $fcp1['email'] = $row[61];
            $fcp1['id_card_no'] = $this->get_field_id_card_no($row[64]);
            $fcp1['is_contract_sign'] = 0;
            $fcp1['is_partner'] = 1;

            $name_result = $this->parse_name($fcp1['name']);

            if($name_result[0]){
                $fcp1['name'] = $name_result[1];
                $fcp1['partner_percent'] = $name_result[2];
            }

            $fcp1_id = $mFranchiseePerson->addFranchiseePerson($fcp1);
            if(!$fcp1_id){
                printf("failure:第%s行创建加盟商合伙人1,写入数据库失败,%s,%s\n",$row_no,$row[5],$mFranchiseePerson->getLastSql());
                $mFranchisee->where('fc_id',$fc_id)->delete(true);
                $mFranchiseeContract->where('fcc_id',$fcc_id)->delete(true);
                $mFranchiseePerson->where('fcp_id',$fcp_id)->delete(true);
                return false;
            }
        }

        if(!empty($row[82])){       //合伙人2
            $fcp2['og_id'] = 0;
            $fcp2['fc_id'] = $fc_id;
            $fcp2['name']  = $row[82];
            $fcp2['sex'] = $this->get_field_sex($row[83]);
            $fcp2['mobile'] = $this->get_field_tel($row[88]);
            $fcp2['email'] = $row[61];
            $fcp2['id_card_no'] = $this->get_field_id_card_no($row[86]);
            $fcp2['is_contract_sign'] = 0;
            $fcp2['is_partner'] = 1;

            $name_result = $this->parse_name($fcp2['name']);

            if($name_result[0]){
                $fcp2['name'] = $name_result[1];
                $fcp2['partner_percent'] = $name_result[2];
            }

            $mFranchiseePerson->addFranchiseePerson($fcp2);
        }

        if(!empty($row[84])){       //合伙人3
            $fcp3['og_id'] = 0;
            $fcp3['fc_id'] = $fc_id;
            $fcp3['name']  = $row[84];
            $fcp3['sex'] = $this->get_field_sex($row[85]);
            $fcp3['mobile'] = $this->get_field_tel($row[89]);
            $fcp3['email'] = $row[61];
            $fcp3['id_card_no'] = $this->get_field_id_card_no($row[87]);
            $fcp3['is_contract_sign'] = 0;
            $fcp3['is_partner'] = 1;

            $name_result = $this->parse_name($fcp3['name']);

            if($name_result[0]){
                $fcp3['name'] = $name_result[1];
                $fcp3['partner_percent'] = $name_result[2];
            }

            $mFranchiseePerson->addFranchiseePerson($fcp3);
        }

        printf("success:第%s行的加盟商导入成功,%s\n",$row_no,$fc['org_name']);

        return true;

    }

    protected function get_sign_eid($ename){
        if(empty($ename) || $ename == 'admin'){
            return 0;
        }
        static $map = [];

        if(isset($map[$ename])){
            return $map[$ename];
        }

        $w['ename'] = $ename;
        $w['og_id'] = 0;

        $einfo = get_employee_info($w);

        if($einfo){
            $map[$ename] = $einfo['eid'];
            return $einfo['eid'];
        }

        $mEmployee = new \app\api\model\Employee();

        $employee['ename'] = $ename;
        $employee['rids']  = [101];
        $employee['bids']  = [10];
        $employee['mobile'] = '';
        $employee['email'] = '';

        $input['employee'] = $employee;

        $result = $mEmployee->createEmployee($input,false,false);

        if(!$result){
            exit($mEmployee->getError());
            $eid = 0;
        }else{
            $eid = $mEmployee->eid;
        }

        $map[$ename] = $eid;

        return $eid;
    }

    /**
     * 获得督导员工ID
     * @param $ename
     */
    protected function get_service_eid($ename){
        if(empty($ename)){
            return 0;
        }
        static $map = [];

        if(isset($map[$ename])){
            return $map[$ename];
        }

        $employees = [
            ['胥静静','女','13261313179','xujingjing@smart-art.com.cn'],
            ['邓惠云',	'女',	'18519662331',	'denghuiyun@smart-art.com.cn'],
            ['高山',	'男',	'13488884535',	'gaoshan@smart-art.com.cn'],
            ['李冬蕊',	'女',	'13552685701',	'lidongrui@smart-art.com.cn'],
            ['刘冰',	'女',	'15910330785',	'liubing@smart-art.com.cn'],
            ['牟雪梅',	'女',	'17347668868',	'mouxuemei@smart-art.com.cn'],
            ['宋亚娟',	'女',	'15110095383',	'songyajuan@smart-art.com.cn'],
            ['吴函坤',	'女',	'18210755323',	'wuhankun@smart-art.com.cn']
        ];


        $w['ename'] = $ename;
        $w['og_id'] = 0;

        $einfo = get_employee_info($w);

        if($einfo){
            $map[$ename] = $einfo['eid'];
            return $einfo['eid'];
        }

        $mEmployee = new \app\api\model\Employee();

        $employee['ename'] = $ename;
        $employee['rids']  = [102];
        $employee['bids']  = [10];

        $ei = [];
        foreach($employees as $e){
            if($e[0] == $ename){
                $ei = $e;
                break;
            }
        }
        if(!empty($ei)){
            $employee['email'] = $ei[3];
            $employee['mobile'] = $ei[2];
            $employee['sex'] = $this->get_field_sex($ei[1]);
        }

        $input['employee'] = $employee;

        $result = $mEmployee->createEmployee($input,false);

        if(!$result){
            $eid = 0;
        }else{
            $eid = $mEmployee->eid;
        }

        $map[$ename] = $eid;

        return $eid;
    }

    protected function parse_name($name){
        $ret = [false,'',0];
        $pattern = '/^([^\d\.%]+)([\d\.]+)%/';
        $matches = [];
        if(preg_match($pattern,$name,$matches)){
            $ret[0] = true;
            $ret[1] = $matches[1];
            $ret[2] = floatval($matches[2]);
        }
        return $ret;
    }

    protected function get_field_tel($tel){
        if(empty($tel)){
            return '';
        }
        return preg_replace('/[^\d\-]+/','',$tel);
    }

    /**
     * @param $area_name
     */
    protected function get_area_id($area_name){
        static $area_map = [];
        if(isset($area_map[$area_name])){
            return $area_map[$area_name];
        }

        $area = db('area','db_center')->where('name',$area_name)->find();
        if($area){
            $area_map[$area_name] = $area['area_id'];
        }else{
            $area_map[$area_name] = 0;
        }

        return $area_map[$area_name];
    }

    protected function get_field_status($text){
        $map = [
          '未选址',
          '筹备期',
          '预售期',
          '正常营业',
          '停业',
          '已解约'
        ];

        $status = array_search($text,$map);

        if(!$status){
            $status = 0;
        }

        return $status;
    }

    protected function get_field_region_level($text){
        $map = [
            '一类',
            '二类',
            '三类',
            '四类',
            '五类'
        ];

        $status = array_search($text,$map);

        if(!$status){
            $status = 0;
        }

        return $status;
    }

    protected function get_field_address_did($text){
        if(empty($text)){
            return 0;
        }
        $did = get_dict_id($text,'address');
        if(!$did){
            $did = add_dict_value($text,'address');
        }
        return $did;
    }


    protected function get_field_is_head_decorate($text){
        if($text == '总部装修'){
            return 1;
        }
        return 0;
    }

    protected function get_field_is_owner_change($text){
        if(empty($text)){
            return 0;
        }
        if($text == '完结'){
            return 1;
        }
        return 0;
    }

    protected function get_field_int_day($text){
        if(empty($text)){
            return 0;
        }
        $text = intval($text);
        if($text < 25569){
            return 0;
        }

        $int_day = format_int_day(excel_datetime($text));
        if($int_day > 99999999){
            return 0;
        }
        return $int_day;
    }

    protected function get_field_sex($sex){
        if($sex == '男'){
            return 1;
        }elseif($sex == '女'){
            return 2;
        }
        return 0;
    }

    protected function get_field_id_card_no($text){
        if(empty($text)){
            return '';
        }
        if(strpos($text,'x') !== false || strpos($text,'X') !== false){
            return $text;
        }
        $pos = strpos($text,'E+');
        if(false === $pos){
            return $text;
        }
        $no = substr($text,0,$pos);
        $no = str_replace('.','',$no);
        $no = str_pad($no,18,'0',STR_PAD_RIGHT);
        return $no;
    }


    

}