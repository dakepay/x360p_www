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
 * php think dbtransfer neza --to=docker_mysql |tee ./runtime/log/dbtransfer.log
 * php think dbtransfer all --from=192.168.33.1 --to=docker_mysql |tee ./runtime/log/dbtransfer_all.log
 * Class ExImport
 * @package app\admapi\command
 */

class DatabaseTransfer extends Command
{
    protected $db_cfg = [];
    public $error_code;

    protected $data;

    protected $start_row_index = 2;
    protected $start_page = 1;
    protected $page_size = 100;
    private $dc = null;
    private $to_server = null;

    public function __construct($name = null)
    {
        parent::__construct($name);

    }

    private function get_root_path(){
        return str_replace('application/admapi/command','',__DIR__);
    }

    protected function configure()
    {
        $this->setName('dbtransfer')->setDescription('transfer database to another server');
        $this->addArgument('host',Argument::OPTIONAL,"the host");
        $this->addOption('to',null,Option::VALUE_REQUIRED,'to serer','');
        $this->addOption('from',null,Option::VALUE_OPTIONAL,'from server','');
        $this->addOption('skipdata',null,Option::VALUE_OPTIONAL,'skip data',null);
    }

    protected function execute(Input $input, Output $output)
    {
        $host   = $input->getArgument('host');
        $to     = $input->getOption('to');
        $from   = $input->getOption('from');
        $skipdata = $input->getOption('skipdata');

        if(is_null($skipdata)){
            $skipdata = false;
        }else{
            $skipdata = true;
        }

        if($host != 'all'){
            $this->doTransfer($host,$to,$skipdata);
            $this->do_transfer_updatedbserver($this->dc,$this->to_server);
            return;
        }

        if($from == ''){
            echo("缺少参数 from!\n");
            return;
        }

        if(strpos($from,':') !== false){
            $arr_from = explode(':',$from);
            $from = $arr_from[0];
            $from_port = $arr_from[1];
        }else{
            $from_port = '';
        }

        echo("开始批量转换".$from."服务器上的数据库到".$to."服务器上\n");
        //get_all_rows($table,$w,$pagesize = 500,$order = '',$skip_deleted = true,$config = [])
        $w['hostname'] = $from;
        if($from_port != ''){
            $w['hostport'] = $from_port;
        }
        $total = 0;
        $success = 0;
        $failure = 0;
        foreach(get_all_rows('database_config',$w,100,'host ASC',true,'db_center') as $row){
            $result = $this->doTransfer($row['host'],$to,$skipdata);
            if($result){
                echo("success:host=>".$row['host'].",from=>".$row['hostname'].",to=>".$to."\n");
                $success++;
            }else{
                echo("failure:host=>".$row['host'].",from=>".$row['hostname'].",to=>".$to."\n");
                $failure++;
            }
            echo("+++++++++++++++++++++++++++++++++++++++++\n");
            $total++;
        }
        $this->do_transfer_updatedbserver($this->dc,$this->to_server);
        echo(sprintf("批量转换结束!总数:%s,成功:%s,失败:%s\n",$total,$success,$failure));
    }


    protected function doTransfer($host,$to,$skipdata = false){

        if(strpos($to,':') !== false){
            $arr_to = explode(':',$to);
            $to = $arr_to[0];
            $to_port = $arr_to[1];
        }else{
            $to_port = '';
        }
        echo("==========================================\n");
        echo("host:".$host." start do transfer!\n");
        $w_dc['host'] = $host;

        $dc = db('database_config','db_center')->where($w_dc)->find();
        if(!$dc){
            echo("host does not exists!");
            return false;
        }

        $w_ds_to['ip'] = $to;
        if($to_port != ''){
            $w_ds_to['port'] = $to_port;
        }
        $to_server = db('dbserver','db_center')->where($w_ds_to)->find();

        if(!$to_server){
            echo("error:to server does not exists!\n");
            return false;
        }

        if($dc['hostname'] == $to_server['ip'] && $dc['hostport'] == $to_server['port']){
            echo("待转移的数据库与现有数据库服务器相同,跳过执行!\n");
            return true;
        }

        $w_ds_from['ip']    = $dc['hostname'];
        $w_ds_from['port']  = $dc['hostport'];

        $from_server = db('dbserver','db_center')->where($w_ds_from)->find();

        if(!$from_server){
            echo("error:from server does not exists!\n");
            return false;
        }
        $now_time = time();
        $dbserver_dir = str_replace('.','_',$dc['hostname']).'_'.$dc['hostport'];
        $sql_bak_file = $this->get_root_path().'data/sql/bak/'.$dbserver_dir.'/x360p_'.$host.'_'.date('YmdH',$now_time).'.sql';

        if(!$skipdata) {
            $result = $this->do_transfer_bakdata($host,$dc,$sql_bak_file);
            if(!$result){
                return false;
            }

            $result = $this->do_transfer_createdb($host,$dc,$to_server);
            if(!$result){
                return false;
            }

            $result = $this->do_transfer_createuser($host,$dc,$to_server);
            if(!$result){
                return false;
            }

            $result = $this->do_transfer_importdata($host,$to_server,$sql_bak_file);
            if(!$result){
                return false;
            }
        }

        $result = $this->do_transfer_updatedc($host,$to_server);
        if(!$result){
            return false;
        }

        echo("[".$host."]end transfer!=====================================\n");
        $this->dc = $dc;
        $this->to_server = $to_server;
        return true;
    }

    /**
     * 备份数据库
     * @param $host
     * @param $dc
     * @param $sql_bak_file
     * @return bool
     */
    private function do_transfer_bakdata($host,$dc,$sql_bak_file){
        echo("开始备份数据库!\n");
        mkdirss(dirname($sql_bak_file));
        $cmd_bak = sprintf("mysqldump -h%s -u%s -p%s -P%s --databases x360p_%s > %s",
            $dc['hostname'],
            $dc['username'],
            $dc['password'],
            $dc['hostport'],
            $host,
            $sql_bak_file
        );

        //echo($cmd_bak);
        list($code, $output, $error) = Cmd::run($cmd_bak);
        if ($code !== 0) {
            echo("transfer:error!备份数据库出错:" . $this->strip_warning($error) . "\n");
            return false;
        }
        echo("backup success:[" . $host . "]!" . $output . "\n------------------------------\n");
        return true;
    }

    /**
     * 创建新数据库
     * @param $host
     * @param $dc
     * @param $to_server
     * @return bool
     */
    private function do_transfer_createdb($host,$dc,$to_server){
        echo("开始创建新数据库!\n");
        // mysql -hdocker_mysql -uroot -proot8848 -P3308 -e "CREATE DATABASE IF NOT EXISTS `x360p_neza` DEFAULT CHARACTER SET utf8mb4;"
        $cmd_create_db = sprintf("mysql -h%s -uroot -p%s -P%s -e \"CREATE DATABASE IF NOT EXISTS %s DEFAULT CHARACTER SET %s;\"
        ",
            $to_server['ip'],
            $to_server['root_pwd'],
            $to_server['port'],
            'x360p_'.$host,
            $dc['charset']
        );

        //echo("\n".$cmd_create_db);
        list($code, $output, $error) = Cmd::run($cmd_create_db);
        if ($code !== 0) {
            echo("创建新数据库出错:" . $this->strip_warning($error) . "\n");
            return false;
        }
        echo("创建新数据库成功:[" . $host . "]!" . $output . "\n------------------------------\n");
        return true;
    }

    private function do_transfer_createuser($host,$dc,$to_server){
        echo("开始创建新用户!\n");
        $cmd_create_user = sprintf("mysql -h%s -uroot -p%s -P%s -e \"GRANT ALL PRIVILEGES ON %s.* to '%s'@'' IDENTIFIED BY '%s';\"
        ",
            $to_server['ip'],
            $to_server['root_pwd'],
            $to_server['port'],
            'x360p_'.$host,
            $dc['username'],
            $dc['password']
        );
        $cmd_create_user = str_replace('@\'\'','@\'%\'',$cmd_create_user);
        //echo("\n".$cmd_create_user);
        if ($dc['username'] == 'root') {
            echo("用户是root,不需要创建!\n");
        } else {
            list($code, $output, $error) = Cmd::run($cmd_create_user);
            if ($code !== 0) {
                echo("创建新用户出错:" . $this->strip_warning($error) . "\n");
                return false;
            }
            echo("创建新用户成功:[" . $host . "]!" . $output . "\n------------------------------\n");
        }
        return true;
    }

    /**
     * 在新的数据库导入数据
     * @param $host
     * @param $dc
     * @param $to_server
     * @param $sql_bak_file
     * @return bool
     */
    private function do_transfer_importdata($host,$to_server,$sql_bak_file){
        echo("开始导入新数据!\n");
        $cmd_restore = sprintf("mysql -h%s -u%s -p%s -P%s < %s",
            $to_server['ip'],
            'root',
            $to_server['root_pwd'],
            $to_server['port'],
            $sql_bak_file
        );
        //echo("\n".$cmd_restore);

        list($code, $output, $error) = Cmd::run($cmd_restore);
        if ($code !== 0) {
            echo("导入新数据出错:" . $this->strip_warning($error) . "\n");
            return false;
        }
        echo("导入新数据成功:[" . $host . "]!" . $output . "\n------------------------------\n");
        return true;
    }

    /**
     * 更新数据库连接记录
     * @param $host
     * @param $to_server
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function do_transfer_updatedc($host,$to_server){
        echo("更新新的数据库连接IP\n");
        $w_update['host'] = $host;
        $update['hostname'] = $to_server['ip'];
        $update['hostport'] = $to_server['port'];
        $result = db('database_config','db_center')->where($w_update)->update($update);

        if(false === $result){
            echo("更新数据库连接IP失败:sql save error!");
            return false;
        }
        echo("更新数据库连接成功[".$host."]!\n------------------------------\n");
        return true;
    }

    private function do_transfer_updatedbserver($dc,$to_server){
        if(is_null($this->dc) || is_null($this->to_server)){
            return false;
        }
        echo("更新服务器数量统计\n");
        $w_from['hostname'] = $dc['hostname'];
        $w_from['hostport'] = $dc['hostport'];
        $from_server_count = db('database_config','db_center')->where($w_from)->count();

        $update_from['db_nums'] = $from_server_count;

        $w_update_from['ip'] = $dc['hostname'];
        $w_update_from['port'] = $dc['hostport'];

        $result = db('dbserver','db_center')->where($w_update_from)->update($update_from);

        if(false === $result){
            echo("更新数据库数量统计失败:".$dc['hostname']."\n");
            return false;
        }
        $w_to['hostname'] = $to_server['ip'];
        $w_to['hostport'] = $to_server['port'];

        $to_server_count = db('database_config','db_center')->where($w_to)->count();

        $update_to['db_nums'] = $to_server_count;
        $w_update_to['ip'] = $to_server['ip'];
        $w_update_to['port'] = $to_server['port'];

        $result = db('dbserver','db_center')->where($w_update_to)->update($update_to);

        if(false === $result){
            echo("更新数据库数量统计失败:".$to_server['ip']."\n");
            return false;
        }
        echo(sprintf("更新服务器统计成功,%s=>%s,%s=>%s\n",$dc['hostname'],$from_server_count,$to_server['ip'],$to_server_count));
    }

    private function strip_warning($warn){
        return str_replace("[Warning] Using a password on the command line interface can be insecure.\n",'',$warn);
    }

}