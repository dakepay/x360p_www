<?php

namespace app\admapi\command;

use app\api\model\Branch;
use app\api\model\Employee;
use app\api\model\Role;
use Curl\Curl;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Log;

class DssSync extends Command
{
    protected $token = '';

    protected $db_cfg = [];

    protected $end_points = [];

    public $error_code;

    protected $curl;

    protected $data;

    protected $start_page = 1;
    protected $page_size = 100;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $config = $this->dss_api_config();
        $this->token = $config['token'];
        $this->end_points = $config['end_points'];
        foreach($this->end_points as $k=>$v){
            $this->end_points[$k] = $config['domain'].$v;
        }
        $this->setCurl();
    }

    protected function setCurl()
    {
        $this->curl = new Curl();
    }

    protected function configure()
    {
        $this->setName('dss')->setDescription('sync dss database');
        $this->addArgument('table', Argument::REQUIRED, "The name of the sync table");
        $this->addArgument('host',Argument::REQUIRED,"The name of the sync client");
        $this->addArgument('page',Argument::OPTIONAL,"The page of import");
    }

    protected function execute(Input $input, Output $output)
    {
        $table = $input->getArgument('table');
        $host  = $input->getArgument('host');

        $start_page = $input->getArgument('page');

        if($start_page){
            $this->start_page = $start_page;
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
        $method = 'sync' . ucfirst($table);
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            printf('Error:parameter invalid,method not exists!-----%s, available parameter:school,student,employee,role,hour', $method);
        }
    }

    protected function syncSchool()
    {
        $data = $this->curlSchools();

        if (empty($data)) {
            return;
        }
        foreach ($data as $school) {
            $model = m('Branch');
            $branch = $model->where('short_name', $school['schoolname'])->whereOr('branch_name',$school['schoolname'])->find();
            if (empty($branch)) {
                $info['short_name']  = $school['schoolname'];
                $info['branch_name'] = $school['schoolname'];
                $info['ext_id']      = $school['schoolid'];
                db('branch')->insert($info);
            } elseif (empty($branch['ext_id'])) {
                $branch->save(['ext_id' => $school['schoolid']]);
            }
        }
    }

    protected function curlSchools()
    {

        $end_point = $this->end_points['schools'];
        $option = [
            'token' => $this->token,
        ];
        $curl = $this->curl;
        $curl->get($end_point, $option);
        if ($curl->error) {
            $this->error_code = $curl->error_code;
            return false;
        }
        $response = json_decode($curl->response, true);
        if ($response['ispass']) {
            return $response['list'];
        } else {
            throw new \Exception('Dss接口ispass返回false,期望返回true，errmsg:' . $response->errormsg);
        }
    }

    protected function syncStudent()
    {

        request()->module('api');
        $data = $this->curlStudents();
        foreach ($data as $list) {
            if (empty($list)) {
                return ;
            }
            foreach ($list as $student) {
                try {
                    $this->processStudent($student);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    continue;
                }
            }
        }
    }

    protected function curlStudents()
    {
        $curl = $this->curl;
        $end_point = $this->end_points['students'];
        $option['token'] = $this->token;
        //$option['hours'] = 1;
        $total = 50000;
        for ($page = $this->start_page, $pagesize = $this->page_size; $page * $pagesize <= $total; $page ++) {
            echo('当前正在处理第'.$page.'页,共:'.$total.'条,每页'.$pagesize.'条');
            echo("\n");
            $option['page'] = $page;
            $option['pagesize'] = $pagesize;
            $curl->get($end_point, $option);
            $response = json_decode($curl->response, true);
            if ($response['ispass'] && $response['list']) {
                $total = $response['total'] + $pagesize;
                yield $response['list'];
            } else {
                throw new \Exception('Dss接口ispass返回false或list为空，errmsg:' . $response['errormsg']);
            }
        }

    }

    protected function processStudent($student)
    {
        static $process_nums = 0;

        $data['student_name']   = $student['studentname'];
        $data['sex']            = $student['sex'] == '男' ? 1 : 2;
        preg_match('/\d{10}/', $student['birthday'], $matches);
        $data['birth_time']     = $matches[0];
        $data['first_tel']      = $this->convert_student_keeperphone($student['keeperphone']);
        $data['first_family_rel']   = $this->convert_student_keepertype($student['keepertype']);
        $data['first_family_name']  = $student['keepername'];
        $data['ext_id']             = $student['studentid'];
        if(isset($student['cardno']) && !empty($student['cardno'])){
            $data['card_no'] = $student['cardno'];
        }
        if(isset($student['gradename']) && !empty($student['gradename'])){
            $data['school_grade'] = $this->trip_grade($student['gradename']);
        }
        if(isset($student['studentid']) && !empty($student['studentid'])){
            $data['sno'] = $student['studentid'];
        }
        if (!empty($student['region'])) {

            $data['bid'] = $this->get_bid_by_region($student['region']);
        }
        $process_nums++;

        $show_nums = ($this->start_page - 1)*$this->page_size + $process_nums;

        echo('处理第'.$process_nums.'条数据!');
        $model = new \app\api\model\Student();
        try {
            $student = $model->where('ext_id', $student['studentid'])->find();
            if (!$student) {
                $model->createOneStudent($data,false,false);
                echo('===>成功!'.$data['student_name']);
                //$model->isUpdate(false)->save($data);
            }else{
                echo('====重复数据不处理!');
            }
        } catch (\Exception $e) {
            echo 'XXXX失败!:'.$student['studentname'].$e->getTraceAsString().$e->getMessage();
            echo $model->getLastSql();
        }
        echo("\n");
        $model->data([]);
    }

    protected function get_bid_by_region($region){
        static $bid_region_map = [];
        if(isset($bid_region_map[$region])){
            return $bid_region_map[$region];
        }
        $branch = db('branch')->where('ext_id', $region)->whereNull('delete_time')->find();
        if ($branch) {
            $bid_region_map[$region] = $branch['bid'];
        }else{
            $bid_region_map[$region] = 0;
        }
        return $bid_region_map[$region];
    }

    protected function trip_grade($grade){
        return intval(preg_replace('/[^\d]+/','',$grade));
    }

    protected function syncEmployee()
    {
        $this->data['roles']  = m('Role')->select();
        $this->data['branches'] = m('Branch')->select();
        $data = $this->curlEmployee();
        foreach ($data as $list) {
            if (empty($list)) {
                return ;
            }
            foreach ($list as $item) {
                try {
                    $this->processEmployee($item);
                } catch (\Exception $e) {
                    echo $e->getMessage();

                    continue;
                }
            }
        }
    }

    protected function curlEmployee()
    {
        $curl = $this->curl;
        $end_point = $this->end_points['employee'];
        $option['token'] = $this->token;
        $total = 200;
        for ($page = 1, $pagesize = 100; $page * $pagesize <= $total; $page ++) {
//            echo $page . '---';
            $option['page'] = $page;
            $option['pagesize'] = $pagesize;
            $curl->get($end_point, $option);
            $response = json_decode($curl->response, true);
            if ($response['ispass'] && $response['list']) {
                $total = $response['total'] + $pagesize ;
                yield $response['list'];
            } else {
                throw new \Exception('Dss api response filed ispass return false or list field is empty，errmsg:' . $response['errormsg']);
            }
        }

    }

    protected function curlRoles()
    {
        $end_point = $this->end_points['roles'];
        $option = [
            'token' => $this->token,
        ];
        $curl = $this->curl;
        $curl->get($end_point, $option);
        if ($curl->error) {
            $this->error_code = $curl->error_code;
            return false;
        }
        $response = json_decode($curl->response, true);
        if ($response['ispass']) {
            return $response['list'];
        } else {
            throw new \Exception('Dss接口ispass返回false,期望返回true，errmsg:' . $response->errormsg);
        }
    }

    protected function syncRole()
    {
        $list = $this->curlRoles();
        foreach ($list as $item) {
            $role = new Role();
            $where['ext_id'] = $item['roleid'];
            $data = $role->where($where)->find();
            if (empty($data)) {
                $insert_data['ext_id']    = $item['roleid'];
                $insert_data['role_name'] = $item['rolename'];
                $insert_data['role_desc'] = $item['rolename'];
                $insert_data['sort']      = $item['showindex'];
                $role->isUpdate(false)->save($insert_data);
            } else {
                $data->save(['sort' => $item['showindex']]);
            }
            unset($role, $data);
        }
    }

    protected function processEmployee($dss_employee)
    {
        static $process_nums = 0;
        $process_nums++;
        echo('处理第'.$process_nums.'条数据!');
        $dss_roles = array_filter(explode('|', $dss_employee['roles']));

        $dss_controlableschools = array_filter(explode(',', $dss_employee['controlableschools']));
        $dss_regions = array_filter(array($dss_employee['regions']));
        $dss_regions = array_unique($dss_regions + $dss_controlableschools);
        $rids = (new Role())->whereIn('ext_id', $dss_roles)->cache(1000)->column('rid');
        $bids = (new Branch())->whereIn('ext_id', $dss_regions)->cache(1000)->column('bid');

        $model = new Employee();

        $qms_employee = $model->where(['ext_id' => $dss_employee['employeeid']])->find();
        if (!$qms_employee) {
            $employee['ename']  = $dss_employee['empname'];
            $employee['ext_id'] = $dss_employee['employeeid'];
            $employee['email']  = $dss_employee['email'];
            $employee['mobile'] = $dss_employee['phone'];
            $employee['bids']   = $bids;
            $employee['rids']   = $rids;
            $employee['sex']    = 0;/*不确定,dss么有性别字段*/

            $user['account']      = $dss_employee['nickname'];
            $user['ext_password'] = $dss_employee['password'];

            $data['from_dss']   = true;
            $data['employee']   = $employee;
            $data['user']       = $user;
            $data['IsActive']   = $dss_employee['isactive'];
            $data['AllowedLogin'] = $dss_employee['allowedlogin'];
            $model = new Employee();
            $result = $model->createEmployee($data, true);
            if (!$result) {
                echo $model->getError();
                return false;
            }
        } else {
            //todo
        }

    }

    protected function syncHour()
    {

        request()->module('api');
        $data = $this->curlHours();

        foreach ($data as $list) {
            if (empty($list)) {
                return ;
            }

            foreach ($list as $student) {
                try {
                    $this->processHour($student);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                    continue;
                }
            }
        }
    }

    protected function curlHours()
    {
        $curl = $this->curl;
        $end_point = $this->end_points['hours'];
        $option['token'] = $this->token;
        $option['hours'] = 1;
        $total = 50000;
        for ($page = $this->start_page, $pagesize = $this->page_size; $page * $pagesize <= $total; $page ++) {
            echo('当前正在处理第'.$page.'页,共:'.$total.'条,每页'.$pagesize.'条');
            echo("\n");
            $option['page'] = $page;
            $option['pagesize'] = $pagesize;
            $curl->get($end_point, $option);
            $response = json_decode($curl->response, true);
            if ($response['ispass'] && $response['list']) {
                $total = $response['total'] + $pagesize;
                yield $response['list'];
            } else {
                throw new \Exception('Dss接口ispass返回false或list为空，errmsg:' . $response['errormsg']);
            }
        }

    }

    protected function processHour($import)
    {
        static $process_nums = 0;
        $process_nums++;
        $show_nums = ($this->start_page - 1)*$this->page_size + $process_nums;

        echo('处理第'.$process_nums.'条数据!');
        echo("\n");
        $model = new \app\api\model\Student();


        try {
            $student = $model->where('ext_id', $import['studentid'])->find();
            if (!$student) {
                echo('学员ID:'.$import['studentid'].'不存在!');
            }else{
                $data['initial_money'] = $import['remainmoney'];
                $data['sid']  = $student->sid;
                $data['remark'] = '从DSS导入';
                $result = $model->importMoney($data,true);
                if(!$result){
                    echo('====>失败!'.$model->getError());
                }else {
                    echo('===>成功!' . $import['studentname']);
                }
            }
        } catch (\Exception $e) {
            echo 'XXXX失败!:'.$import['studentname'].$e->getTraceAsString().$e->getMessage();
            echo $model->getLastSql();
        }
        echo("\n");
        $model->data([]);
    }

    protected function convert_student_keeperphone($value)
    {
        if (strstr($value, '/') == false) {
            return $value;
        }
        return explode('/', $value)[0];
    }

    protected function convert_student_keepertype($value)
    {
        if ($value == '母亲') {
            return 3;
        }
        if ($value == '父亲') {
            return 2;
        }
        return 4;
    }

    public function dss_api_config()
    {
        return include CONF_PATH . 'dss_api.php';
    }
}