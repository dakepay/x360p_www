<?php
namespace app\admapi\command;

use app\api\model\Student;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Log;
use util\excel;

/**
 * shell useage
 * php think eximport changeBranch neza|tee ./transfer_branch3.log
 * php think eximport SmtLessonHour smtjy|tee ./smt_lesson_hour.log.txt
 * php think eximport updateEmployeeTel neza --file=data/import/neza/employee_tel_20181128.xls|tee ./runtime/log/update_employee_tel_20181128.txt
 * Class ExImport
 * @package app\admapi\command
 */

class ExImport extends Command
{
    protected $db_cfg = [];
    public $error_code;

    protected $data;

    protected $start_row_index = 3;
    protected $start_page = 1;
    protected $page_size = 100;
    private $_total = 0;
    private $_success = 0;
    private $_failure = 0;

    public function __construct($name = null)
    {
        parent::__construct($name);

    }

    protected function configure()
    {
        $this->setName('eximport')->setDescription('import data from excel file');
        $this->addArgument('func', Argument::REQUIRED, "The function name of the application");
        $this->addArgument('host',Argument::REQUIRED,"The name of the database");
        $this->addOption('file',null,Option::VALUE_OPTIONAL,'excel file','');
    }

    protected function execute(Input $input, Output $output)
    {
        $func = $input->getArgument('func');
        $host  = $input->getArgument('host');
        $file  = $input->getOption('file');

        if($file == ''){
            $file = 'data/import/student_branch_transfer3.xlsx';
        }
        $real_file = ROOT_PATH.$file;

        if(!file_exists($real_file)){
            printf('file:%s does not exists!',$file);
            return;
        }

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
            $this->$method($real_file);
        } else {
            printf('Error:parameter invalid,method not exists!-----%s, available parameter:ChangeBranch', $method);
        }
    }

    /**
     * 批量更新员工电话
     * @param string $file
     */
    public function UpdateEmployeeTel($real_file = ''){
        $excel = new excel();
        $sheet_names = $excel->getSheets($real_file);
        $sheet_nums = count($sheet_names);
        printf("共%s个sheet需要处理\n",$sheet_nums);
        foreach($sheet_names as $index=>$name){
            printf("开始处理sheet:%s\n======================================\n",$name);
            $this->do_update_employee_tel_sheet($index,$real_file,$name);

        }
        printf("本Excel共处理%s条记录，成功%s条,失败%s条\n",$this->_total,$this->_success,$this->_failure);


    }

    /**
     * 处理一个sheet
     * @param $sheet_index
     * @param $real_file
     */
    protected function do_update_employee_tel_sheet($sheet_index,$real_file,$sheet_name){
        $this->start_row_index = 1;
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
            $rows = $excel->readAsRow($xls_file,$start_row,$pagesize,$sheet_index,true);
            if(!$rows || empty($rows)){
                break;
            }
            $row_no = $start_row;
            foreach($rows as $row){
                $result = $this->update_employee_tel($row,$row_no,$sheet_name);
                if($result){
                    $success++;
                }else{
                    $failure++;
                }
                $total++;
                $row_no++;
            }
        }
        $this->_total += $total;
        $this->_success += $success;
        $this->_failure += $failure;

        printf("本sheet共处理%s条记录，成功%s条,失败%s条\n",$total,$success,$failure);
        echo("============================================\n");

    }

    /**
     * 更新一行数据
     * @param $row
     * @param $row_no
     */
    private function update_employee_tel($row,$row_no,$sheet_name){
        $branch_name = $row[1];
        $employee_name = $row[2];
        $tel  = $row[3];

        $branch = $this->get_branch_by_branch_name($branch_name);

        if(!$branch){
            printf("error:sheet[%s]第%s行数据错误,校区不存在!校区:%s,姓名:%s,电话:%s\n",$sheet_name,$row_no,$branch_name,$employee_name,$tel);
            return false;
        }

        $w['bid'] = $branch['bid'];
        $w['ename'] = $employee_name;

        $employee_info = get_employee_info($w);

        if(!$employee_info){
            printf("error:sheet[%s]第%s行数据错误,找不到员工!校区:%s,姓名:%s,电话:%s\n",$sheet_name,$row_no,$branch_name,$employee_name,$tel);
            return false;
        }

        if($employee_info['mobile'] == $tel){
            printf("skip:sheet[%s]第%s行数据不需要处理,电话号码正确,校区:%s,姓名:%s,电话:%s\n",$sheet_name,$row_no,$branch_name,$employee_name,$tel);
            return true;
        }

        $update['mobile'] = $tel;

        $w_update['eid'] = $employee_info['eid'];

        $result = db('employee')->where($w_update)->update($update);

        if(false === $result){
            printf("error:sheet[%s]第%s行数据更新错误，更新employee表sql错误,校区:%s,姓名:%s,电话:%s\n",$sheet_name,$row_no,$branch_name,$employee_name,$tel);
            return false;
        }

        if($employee_info['uid'] > 0){
            $w_update_user['uid'] = $employee_info['uid'];
            $result = db('user')->where($w_update_user)->update($update);
            if(false === $result) {
                printf("error:sheet[%s]第%s行数据更新错误，更新user表sql错误,校区:%s,姓名:%s,电话:%s\n",$sheet_name, $row_no, $branch_name, $employee_name, $tel);
                return false;
            }
        }

        printf("success:sheet[%s]第%s行数据处理成功,电话号码已更新,校区:%s,姓名:%s,电话:%s|原来电话:%s\n",$sheet_name,$row_no,$branch_name,$employee_name,$tel,$employee_info['mobile']);
        return true;
    }

    private function get_branch_by_branch_name($branch_name){
        $w['short_name'] = $branch_name;
        return get_branch_info($w);
    }

    /**
     * 批量转换校区
     * @param string $file
     */
    public function ChangeBranch($real_file){

        $sheet_index = 0;
        $xls_file = $real_file;
        $excel = new excel();
        $xcount = $excel->getExcelCount($xls_file,$this->start_row_index,$sheet_index);

        $data_count = $xcount['data_count'];
        $page = 1;
        $pagesize = 100;
        $total_page = ceil($data_count / $pagesize);

        $success = 0;
        $failure = 0;
        $total   = 0;
        for($page = 1;$page <= $total_page;$page++){
            $start_row = ($page - 1)*$pagesize + $this->start_row_index;
            $end_row = $start_row + $pagesize;
            $rows = $excel->readAsRow($xls_file,$start_row,$pagesize);
            if(!$rows || empty($rows)){
                break;
            }
            $row_no = $start_row;
            foreach($rows as $row){
                $result = $this->change_branch_one($row,$row_no);
                if($result){
                    $success++;
                }else{
                    $failure++;
                }
                $total++;
                $row_no++;
            }
        }
        printf("共处理%s条记录，成功%s条,失败%s条\n",$total,$success,$failure);
        echo('done!');
    }

    /**
     * 修复斯玛特的导入数据课时数问题
     */
    public function SmtLessonHour($file){
        if($file == ''){
            $file = 'data/fixdata/smtjy/2018_8.xlsx';
        }
        $real_file = ROOT_PATH.$file;

        if(!file_exists($real_file)){
            printf('file:%s does not exists!',$file);
        }
        $this->start_row_index = 2;
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
                $result = $this->fix_smtjy_lesson_hour($row,$row_no);
                if($result){
                    $success++;
                }else{
                    $failure++;
                }
                $total++;
                $row_no++;
            }
        }

        $sql = <<<EOF
UPDATE `x360p_employee_lesson_hour` elh
LEFT JOIN (
  select catt_id,sum(lesson_amount) as lesson_amount
  from `x360p_student_lesson_hour`
  where is_delete = 0 and catt_id > 0 and lesson_amount > 0
  group by catt_id
) c
ON elh.catt_id = c.catt_id
LEFT JOIN (
  select catt_id,sum(lesson_amount) as lesson_amount
  from `x360p_student_lesson_hour`
  where is_delete = 0 and catt_id > 0 and lesson_amount > 0 and is_pay=1 and sl_id > 0
  group by catt_id
) d
ON elh.catt_id = d.catt_id
set elh.total_lesson_amount = c.lesson_amount,
    elh.payed_lesson_amount = d.lesson_amount
WHERE elh.og_id = 0 AND c.catt_id IS NOT NULL and elh.total_lesson_amount <> c.lesson_amount
EOF;

        //更新总剩余课时
        db()->execute($sql);

        printf("共处理%s条记录，成功%s条,失败%s条\n",$total,$success,$failure);
        echo('done!');
    }

    /**
     * 执行修复
     * @param $row
     * @param $row_no
     */
    protected function fix_smtjy_lesson_hour($row,$row_no){
        $student_name = $row[0];
        $branch_name  = $row[12];
        $import_lesson_hour  = floatval($row[9]) * 2;
        $import_unit_price = round(floatval($row[6]) / 2,2);


        $bid = $this->get_bid($branch_name);
        if(!$bid){
            printf("failure:第%s行的校区名不对,%s,%s\n",$row_no,$student_name,$branch_name);
            return false;
        }

        $w['bid'] = $bid;
        $w['student_name'] = $student_name;

        $mStudent = new \app\api\model\Student();
        $student_list = $mStudent->where($w)->select();

        if(!$student_list){
            printf("failure:第%s行的学员名不对,%s,%s\n",$row_no,$student_name,$branch_name);
            return false;
        }

        if(count($student_list) > 1){
            printf("failure:第%s行的学员名存在多个记录无法确定哪一个,%s,%s\n",$row_no,$student_name,$branch_name);
            return false;
        }

        $student = $student_list[0];

        $sid = $student['sid'];

        $w_slil['sid'] = $sid;

        $mStudentLessonImportLog = new \app\api\model\StudentLessonImportLog();

        $slil_list = $mStudentLessonImportLog->where($w_slil)->order('create_time ASC')->select();

        $slil_list_nums = 0;

        if($slil_list){
            $slil_list_nums = count($slil_list);
        }

        if($slil_list_nums == 0){
            printf("failure:empty:第%s行的学员没有导入记录，需要另外单独导入,%s,%s\n",$row_no,$student_name,$branch_name);
            return false;
        }

        $mStudentLesson = new \app\api\model\StudentLesson();

        $w_sl['sid'] = $sid;

        $m_sl = $mStudentLesson->where($w_sl)->find();

        if(!$m_sl){
            printf("failure:empty:第%s行的学员没有课时记录，需要另外单独导入,%s,%s\n",$row_no,$student_name,$branch_name);
            return false;
        }

        $has_order_nums = 0;
        $old_lesson_hour = $m_sl->lesson_hours;
        $old_remain_lesson_hour = $m_sl->remain_lesson_hours;

        $w_o['sid'] = $sid;
        $mOrder = new \app\api\model\Order();
        $order_list = $mOrder->where($w_o)->select();

        if($order_list){
            $has_order_nums = count($order_list);
            foreach($order_list as $order){
                $result = $mOrder->deleteOrder($order,true);
                if(false === $result){
                    printf("failure:empty:第%s行的学员删除订单失败，需要另外单独处理,%s,%s,%s,%s\n",$row_no,$mOrder->getError(),$student_name,$branch_name,$order->oid);
                    exit;
                    return false;
                }
            }
        }

        $del_import_record_nums = 0;
        if($slil_list_nums > 1){        //删除多余的导入记录
            for($i=1;$i<$slil_list_nums;$i++){
                $slil_list[$i]->delete();
                $del_import_record_nums++;
            }
        }

        $need_fix_slil = $slil_list[0];

        $old_unit_price = $need_fix_slil->unit_lesson_hour_amount;

        $need_fix_slil->lesson_hours = $import_lesson_hour;
        $need_fix_slil->unit_lesson_hour_amount = $import_unit_price;

        $result = $need_fix_slil->save();
        if(false === $result){
            printf("failure:sql_save_error:第%s行的学员更新导入记录失败，%s,%s\n",$row_no,$student_name,$branch_name);
            return false;
        }

        //删除登记课耗
        $w_slh['sl_id'] = $m_sl->sl_id;
        $w_slh['change_type'] = 2;

        $mStudentLessonHour = new \app\api\model\StudentLessonHour();
        $need_del_hours = $mStudentLessonHour->where($w_slh)->sum('lesson_hours');

        if($need_del_hours){
            $mStudentLessonHour->where($w_slh)->delete();
        }

        //计算实际使用课耗记录数
        $w_slh['change_type'] = 1;

        $total_used_hours = $mStudentLessonHour->where($w_slh)->sum('lesson_hours');

        $m_sl->import_lesson_hours = $import_lesson_hour;
        $m_sl->lesson_hours = $import_lesson_hour;
        $m_sl->use_lesson_hours = $total_used_hours;
        $m_sl->remain_lesson_hours = $import_lesson_hour - $total_used_hours;

        $result = $m_sl->save();
        if(false === $result){
            printf("failure:sql_save_error:第%s行的学员更新课时记录失败，%s,%s\n",$row_no,$student_name,$branch_name);
            return false;
        }

        //更新学员表剩余课时
        $student->student_lesson_hours = $m_sl->lesson_hours;
        $student->student_lesson_remain_hours  = $m_sl->remain_lesson_hours;

        $new_lesson_hour = $m_sl->lesson_hours;
        $new_remain_lesson_hour = $m_sl->remain_lesson_hours;

        $result = $student->save();
        if(false === $result){
            printf("failure:sql_save_error:第%s行的学员更新学员记录失败，%s,%s\n",$row_no,$student_name,$branch_name);
            return false;
        }

        //批量更新学员课耗单价
        $slh_list = $mStudentLessonHour->where($w_slh)->select();

        $old_used_amount = 0;
        $new_used_amount = 0;
        foreach($slh_list as $slh){
            $old_used_amount += $slh->lesson_amount;
            $slh->lesson_amount = $import_unit_price * $slh->lesson_hours;
            $new_used_amount += $slh->lesson_amount;
            $slh->save();
        }

        printf("success:第%s行的学员课时处理成功,%s,%s,
        删除登记课时:%s,
        删除多余订单:%s,
        删除多余导入记录:%s,
        原来总课时:%s,新总课时:%s,
        原来剩余课时:%s,新剩余课时:%s,
        原来课消金额:%s,新课消金额:%s,
        原来课时单价:%s,新课时单价:%s\n",
            $row_no,$student_name,$branch_name,
            $need_del_hours,
            $has_order_nums,
            $del_import_record_nums,
            $old_lesson_hour,$new_lesson_hour,
            $old_remain_lesson_hour,$new_remain_lesson_hour,
            $old_used_amount,$new_used_amount,
            $old_unit_price,$import_unit_price
        );

        return true;
    }

    /**
     * @param $row
     * @param $row_no
     */
    protected function change_branch_one($row,$row_no){
        $student_name = $row[0];
        $mobile = trim($row[1]);
        $mobiles = [];
        $old_branch_name = $row[2];
        $new_branch_name = $row[3];


        $w['short_name'] = $old_branch_name;

        $old_branch_info = get_branch_info($w);

        if(!$old_branch_info){
            printf("failure:第%s行的原校区名不对,%s,%s,%s,%s\n",$row_no,$student_name,$mobile,$old_branch_name,$new_branch_name);
            return false;
        }

        $old_bid = $old_branch_info['bid'];

        $w['short_name'] = $new_branch_name;
        $new_branch_info = get_branch_info($w);

        if(!$new_branch_info){
            printf("failure:第%s行的原校区名不对,%s,%s,%s,%s\n",$row_no,$student_name,$mobile,$old_branch_name,$new_branch_name);
            return false;
        }
        $new_bid = $new_branch_info['bid'];

        if(strlen($mobile) > 11 && strpos($mobile,'/') !== false){
            $mobiles = explode('/',$mobile);
            $mobile  = $mobiles[0];
        }else{
            $mobiles[] = $mobile;
        }

        $w_student['student_name'] = $student_name;
        $w_student['bid'] = $old_bid;

        $m_student = new Student();

        $student_list = $m_student->where($w_student)->select();

        if(!$student_list){
            printf("failure:第%s行的学员姓名在原校区找不到,%s,%s,%s,%s\n",$row_no,$student_name,$mobile,$old_branch_name,$new_branch_name);
            return false;
        }

        $student = null;
        if(count($student_list) == 1){
            $student = $student_list[0];
        }else{
            foreach($student_list as $s){
                if(in_array($s['first_tel'],$mobiles) || in_array($s['second_tel'],$mobiles)){
                    $student = $s;
                    break;
                }
            }
            if(!$student){
                printf("failure:第%s行的学员姓名在原校区找到，但是手机号不对,%s,%s,%s,%s\n",$row_no,$student_name,$mobile,$old_branch_name,$new_branch_name);
                return false;
            }
        }

        $result = $student->transferBranch($new_bid);

        if(!$result){
            printf("failure:第%s行的学员在执行转校操作时出错:%s,%s,%s,%s,%s\n",$row_no,$student->getError(),$student_name,$mobile,$old_branch_name,$new_branch_name);
            return false;
        }

        printf("success:第%s行的学员处理成功,%s,%s,%s,%s\n",$row_no,$student_name,$mobile,$old_branch_name,$new_branch_name);

        return true;

    }

    /**
     * @param $branch_name
     */
    protected function get_bid($branch_name){
        static $map = [];
        if(isset($map[$branch_name])){
            return $map[$branch_name];
        }

        $mBranch = new \app\api\model\Branch();

        $branch_list = $mBranch->select();

        foreach($branch_list as $branch){
            $map[$branch['branch_name']] = $branch['bid'];
        }

        if(isset($map[$branch_name])){
            return $map[$branch_name];
        }

        return 0;
    }


}