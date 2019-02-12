<?php
namespace app\admapi\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Model;
use \app\api\model\Base;


/**
 * shell useage
 * php think datapatch studentLessonAmount --host=base |tee ./runtime/log/datapatch_20181130.txt
 * Class DataPatch
 * @package app\admapi\command
 */

class DataPatch extends Command
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
        $this->setName('datapatch')->setDescription('data patch for some host');
        $this->addArgument('func',Argument::OPTIONAL,"the function");
        $this->addOption('host',null,Option::VALUE_OPTIONAL,"host",'');
        $this->addOption('pagesize',null,Option::VALUE_OPTIONAL,"pagesize",100);

    }

    protected function execute(Input $input, Output $output)
    {
        $func = $input->getArgument('func');
        $host  = $input->getOption('host');
        $pagesize = intval($input->getOption('pagesize'));


        $method = ucfirst($func);

        if(!method_exists($this,$method)){
            printf('Error:parameter invalid,func not exists!-----%s, available parameter:studentLessonAmount', $method);
            return;
        }

        $w['is_delete'] = 0;
        if(empty($host) || $host == 'all'){
            if(!$pagesize){
                printf("parameter error ,pagesize ");
                return;
            }
        }else{
            $w['host'] = $host;
        }

        $total = 0;
        echo("开始执行数据修复!\n");
        foreach(get_all_rows('client',$w,$pagesize,'host ASC',true,'db_center',false) as $client){
            $this->doExec($method,$client);

            $total++;
        }
        echo(sprintf("执行完毕!总计:%s\n",$total));
    }

    /**
     * @param $client
     */
    protected function doExec($method,$client){
        if($client['parent_cid'] > 0){
            $w_dc['cid'] = $client['parent_cid'];
        }else{
            $w_dc['cid'] = $client['cid'];
        }
        $dc = db('database_config','db_center')->where($w_dc)->find();
        if(!$dc){
            printf("error:database config not exists!host:%s,客户:%s\n",$client['host'],$client['client_name']);
            return false;
        }
        $client['database'] = $dc;
        config('database',$dc);
        gvar('client',$client);
        gvar('og_id',$client['og_id']);
        printf("开始处理客户:%s的数据,host:%s,db:%s,og_id:%s\n",$client['client_name'],$client['host'],$dc['database'],$client['og_id']);
        $this->$method();
        printf("end===================================\n");




    }

    /**
     * 处理剩余课时金额
     */
    protected function StudentLessonAmount(){
        \app\api\model\Base::ResetLinks();
        Db::clear();
        $client = gvar('client');
        $dbname = $client['database']['database'];
        $w['og_id'] = gvar('og_id');
        $total = 0;
        $success = 0;
        $failure = 0;
        foreach(get_all_rows('student_lesson',$w,30,'sl_id ASC',true,$client['database'],false) as $sl_info){

            $total++;
            $mSl = new \app\api\model\StudentLesson($sl_info);
            $result = $mSl->updateLessonAmount();
            if(!$result){
                printf("error!:%s,sl_id:%s,db:%s\n",$mSl->getError(),$sl_info['sl_id'],$dbname);
                $failure++;
                continue;
            }

            $success++;
            printf("success!db:%s,sl_id:%s,old(lesson_amount:%s,remain_lesson_amount:%s),new(lesson_amount:%s,remain_lesson_amount:%s)\n",
                    $dbname,
                    $sl_info['sl_id'],
                    $sl_info['lesson_amount'],
                    $sl_info['remain_lesson_amount'],
                    $mSl['lesson_amount'],
                    $mSl['remain_lesson_amount']
            );


        }
        printf("total:%s,success:%s,failure:%s\n",$total,$success,$failure);
    }







}