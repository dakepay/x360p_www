<?php
namespace app\admapi\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\common\Cmd;

/**
 * shell useage
 * php think sqlexec sql/patch/patch_1.4.3_3.sql --skipdb= --db= |tee ./runtime/log/sql_execute/patch1.4.3_3.log
 * php think sqlexec sql/patch/patch_1.4.3_4.sql |tee ./runtime/log/sql_execute/patch1.4.3_4.txt
 * php think sqlexec sql/patch/patch_1.4.4_1.sql |tee ./runtime/log/sql_execute/patch1.4.4_1.txt
 * php think sqlexec sql/patch/patch_1.5.1_2.sql |tee ./runtime/log/sql_execute/patch1.5.1_2.txt
 * php think sqlexec sql/patch/patch_1.5.2_2.sql |tee ./runtime/log/sql_execute/patch1.5.2_2.txt
 * Class ExImport
 * @package app\admapi\command
 */

class SqlExecute extends Command
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

    private function get_root_path(){
        return str_replace('application/admapi/command','',__DIR__);
    }

    protected function configure()
    {
        $this->setName('sqlexec')->setDescription('bat execute sqlfile');
        $this->addArgument('sqlfile',Argument::OPTIONAL,"the sql file path");
        $this->addOption('skipdb',null,Option::VALUE_OPTIONAL,"skip databases",'');
        $this->addOption('db',null,Option::VALUE_OPTIONAL,'db exe','');
    }

    protected function execute(Input $input, Output $output)
    {
        $sqlfile = $input->getArgument('sqlfile');
        $skipdb  = $input->getOption('skipdb');
        $mustdb  = $input->getOption('db');

        if(substr($sqlfile,0,1) != '/'){
            $sqlfile = $this->get_root_path().$sqlfile;
        }


        if(!file_exists($sqlfile)){
            throw new \Exception("sqlfile:$sqlfile does not exists!");
        }

        $skipdbs = explode(',',$skipdb);
        $w = [];
        if(!empty($mustdb)){
            $w['host'] = ['IN',explode(',',$mustdb)];
        }else{
            if(!empty($skipdb)) {
                $w['host'] = ['NOTIN', explode(',', $skipdb)];
            }
        }

        $total = 0;
        $success = 0;
        $failure = 0;
        echo("开始执行SQL导入!\n");
        foreach(get_all_rows('database_config',$w,20,'host ASC',true,'db_center') as $db_cfg){
            if(in_array($db_cfg['host'],$skipdbs)){
                echo("skip:{$db_cfg['host']}\n");
                continue;
            }
            $result = $this->exesql($db_cfg,$sqlfile);
            if($result){
                $success++;
            }else{
                $failure++;
            }
            $total++;
        }
        echo(sprintf("执行完毕!总计:%s,成功:%s,失败:%s\n",$total,$success,$failure));
    }


    protected function exesql($db_cfg,$sqlfile){

        $password = str_replace('$','\\$',$db_cfg['password']);

        $shell = sprintf("mysql -h%s -u%s -p%s x360p_%s < %s",
                $db_cfg['hostname'],
                $db_cfg['username'],
                $password,
                $db_cfg['host'],
                $sqlfile
        );
        list($code,$output,$error) = Cmd::run($shell);
        if($code !== 0){
            echo("import[".$db_cfg['host']."]:error!".$this->strip_warning($error)."\n");
            return false;
        }
        echo("import[".$db_cfg['host']."]:success!".$output."\n===============================\n");
        return true;
    }


    private function strip_warning($warn){
        return str_replace("[Warning] Using a password on the command line interface can be insecure.\n",'',$warn);
    }







}