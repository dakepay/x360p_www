<?php

namespace app\admapi\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

/**
 * shell useage
 * php think x360 branch 10884 kcenglish|tee ./runtime/log/x360_sync_branch_10884.txt
 * Class X360Sync
 * @package app\admapi\command
 */
class X360Sync extends Command
{
    /*
    protected $x360_db_cfg = [
        'type'            => 'mysql',
        // 服务器地址
        'hostname'        => '120.24.174.15',
        // 数据库名
        'database'        => 'x360',
        // 用户名
        'username'        => 'x360',
        // 密码
        'password'        => 'vx360Pwd@016',
        // 端口
        'hostport'        => '3306',
        // 连接dsn
        'dsn'             => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 数据库表前缀
        'prefix'          => 'x360_'
    ];*/
    protected $x360_db_cfg = [
        'type'            => 'mysql',
        // 服务器地址
        'hostname'        => '192.168.33.1',
        // 数据库名
        'database'        => 'x360',
        // 用户名
        'username'        => 'root',
        // 密码
        'password'        => 'root8848',
        // 端口
        'hostport'        => '3306',
        // 连接dsn
        'dsn'             => '',
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 数据库表前缀
        'prefix'          => 'x360_'
    ];

    protected $db_cfg = [];
    public $error_code;
    protected $data;

    protected $start_page = 1;
    protected $page_size = 100;


    protected function configure()
    {
        $this->setName('x360')->setDescription('sync x360 database');
        $this->addArgument('table', Argument::REQUIRED, "The name of the sync table");
        $this->addArgument('og_id',Argument::REQUIRED,"The og_id of the sync client");
        $this->addArgument('host',Argument::REQUIRED,"The host of import");
        $this->addOption('page',null,Option::VALUE_OPTIONAL,'start page',1);
    }

    protected function execute(Input $input, Output $output)
    {
        $table = $input->getArgument('table');
        $host  = $input->getArgument('host');
        $og_id = $input->getArgument('og_id');

        $start_page = $input->getOption('page');
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
            $this->$method($og_id);
        } else {
            printf('Error:parameter invalid,method not exists!-----%s, available parameter:branch,student,role,hour', $method);
        }
    }

    /**
     * 同步校区
     * @param $og_id
     */
    public function syncBranch($og_id){
        echo("start sync branch!\n");
        $db_x360 = db('org_branch',$this->x360_db_cfg);
        $w['og_id'] = $og_id;
        $w['is_main'] = 1;

        $branch = $db_x360->where($w)->find();

        if(!$branch){
            printf("没有找到要同步的主校区数据!\n");
            return false;
        }

        //先同步主校区数据
        $total = 0;
        $success = 0;
        $failure = 0;

        $result = $this->do_sync_branch($branch,true);

        $total++;
        if(!$result){
            $failure++;
        }else{
            $success++;
        }

        $w['is_main'] = 0;

        $branch_list = $db_x360->where($w)->select();

        foreach($branch_list as $branch){
            $result = $this->do_sync_branch($branch,false);
            $total++;
            if($result){
                $success++;
            }else{
                $failure++;
            }
        }

        printf("sync branch done!total:%s,success:%s,failure:%s\n",$total,$success,$failure);
    }

    /**

     * @param $branch
     * @param bool $is_main
     * @return bool
     */
    protected function do_sync_branch($branch,$is_main = false){
        $mBranch = new \app\api\model\Branch();
        $mDepartment = new \app\api\model\Department();
        if($is_main){
            $first_branch = $mBranch->order('bid ASC')->find();
            $first_branch['short_name']  = $branch['short_name'];
            $first_branch['branch_name'] = $branch['branch_name'];
            $first_branch['address'] = $branch['branch_address'];
            $first_branch['ext_id']  = $branch['ob_id'];

            $result = $first_branch->save();

            if(false === $result){
                printf("failure:branch_name:%s,error:%s\n",$branch['short_name'],"写入主校区数据库失败!");
                return false;
            }
        }else{
            $w['short_name|branch_name'] = $branch['short_name'];
            $ex_branch = $mBranch->where($w)->find();
            if($ex_branch){
                $ex_branch = $mBranch->order('bid ASC')->find();
                $ex_branch['short_name']  = $branch['short_name'];
                $ex_branch['branch_name'] = $branch['branch_name'];
                $ex_branch['address'] = $branch['branch_address'];
                $ex_branch['ext_id']  = $branch['ob_id'];

                $result = $ex_branch->save();

                if(false === $result){
                    printf("failure:branch_name:%s,error:%s\n",$branch['short_name'],"写入校区数据库失败!");
                    return false;
                }
            }else{
                //创建新校区
                $dpt['dpt_type'] = 1;
                $dpt['dpt_name'] = $branch['short_name'];
                $dpt['pid'] = 0;

                $result = $mDepartment->save($dpt);

                if(!$result){
                    printf("failure:branch_name:%s,error:%s\n",$branch['short_name'],"写入部门数据库失败!");
                    return false;
                }

                $nb['short_name']  = $branch['short_name'];
                $nb['branch_name'] = $branch['branch_name'];
                $nb['address'] = $branch['branch_address'];
                $nb['ext_id']  = $branch['ob_id'];

                $result = $mBranch->save($nb);

                if(!$result){
                    printf("failure:branch_name:%s,error:%s\n",$branch['short_name'],"写入校区数据库失败!");
                    return false;
                }

                $mDepartment['bid'] = $mBranch->bid;

                $result = $mDepartment->save();

                if(!$result){
                    printf("failure:branch_name:%s,error:%s\n",$branch['short_name'],"更新部门数据库校区ID失败!");
                    return false;
                }

            }
        }
        printf("success:branch_name:%s\n",$branch['short_name']);
        return true;
    }


    public function syncStudent($og_id){
        echo("start sync student!\n");
        $total = 0;
        $success = 0;
        $failure = 0;

        $w['isdelete'] = 0;
        $w['status'] = ['GT',0];
        $w['og_id'] = $og_id;
        request()->module('api');
        foreach(get_all_rows('org_student',$w,100,'os_id ASC',false,$this->x360_db_cfg,false) as $student){
            $result = $this->do_sync_student($student);
            $total++;
            if($result){
                $success++;
            }else{
                $failure++;
            }
        }
        printf("sync student done!total:%s,success:%s,failure:%s\n",$total,$success,$failure);
    }

    protected function do_sync_student($student){

        $data['student_name']   = $student['student_name'];
        $data['sex']            = $student['sex'];

        $data['birth_year']     = $student['birth_year'];
        $data['birth_month']    = $student['birth_month'];
        $data['birth_day']      = $student['birth_day'];
        $data['birth_time']     = mktime(0,0,0,$student['birth_month'],$student['birth_day'],$student['birth_year']);


        $data['first_tel']          =  $student['first_tel'];
        $data['first_family_rel']   = $this->convert_student_family_rel($student['first_rel_rel']);
        $data['first_family_name']  = $student['first_rel_name'];
        $data['ext_id']             = $student['os_id'];


        $data['card_no'] = $student['card_no'];
        $data['sno'] = $student['os_no'];


        $data['school_grade'] = $student['grade'];
        $data['school_class'] = $student['class'];

        $data['bid'] = $this->get_bid_by_obid($student['ob_id']);


        $data['ext_id'] = $student['os_id'];

        //查看次要联系人
        $w['os_id'] = $student['os_id'];
        $w['is_main'] = 0;
        $second_relation = db('org_student_relation',$this->x360_db_cfg)->where($w)->find();

        if($second_relation){
            $data['second_tel'] = $second_relation['tel'];
            $data['second_family_rel'] = $this->convert_student_family_rel($second_relation['relation']);
            $data['second_family_name'] = $second_relation['name'];
        }

        $mStudent = new \app\api\model\Student();

        try {
            $ex_student = $mStudent->where('ext_id', $student['os_id'])->find();
            if (!$ex_student) {
                $result = $mStudent->createOneStudent($data,false,false);
                if(!$result){
                    printf("failure:student_name:%s,error:%s\n",$student['student_name'],$mStudent->getError());
                    return false;
                }
                printf("success:student_name:%s\n",$student['student_name']);
            }else{
                printf("repeat:student_name:%s\n",$student['student_name']);
            }
        } catch (\Exception $e) {
           printf("failure:student_name:%s,e:%s,sql:%s\n",$student['student_name'],$e->getMessage(),$mStudent->getLastSql());
           return false;
        }
        return true;
    }

    protected function convert_student_family_rel($rel){
        return get_family_rel_id($rel);
    }

    protected function get_bid_by_obid($ob_id){
        static $bid_region_map = [];
        if(isset($bid_region_map[$ob_id])){
            return $bid_region_map[$ob_id];
        }
        $branch = db('branch')->where('ext_id', $ob_id)->whereNull('delete_time')->find();
        if ($branch) {
            $bid_region_map[$ob_id] = $branch['bid'];
        }else{
            $bid_region_map[$ob_id] = 0;
        }
        return $bid_region_map[$ob_id];
    }

}