<?php
/**
 * Author: luo
 * Time: 2017-12-04 18:31
**/

namespace app\admapi\model;

use think\Db;
use app\common\Cmd;

class DatabaseConfig extends Base
{

    protected $ds = null;           //数据库服务器实例
    protected $db = null;           //数据库连接实例
    protected $dc = null;           //数据库连接配置
    protected $use_db = null;       //当前数据库名
    /**
     * 安装数据库 
     * @param  [type] $cid  [description]
     * @param  [type] $host [description]
     * @return [type]       [description]
     */
    public function installDb(Client $client, $input) {
        $ver_file = ROOT_PATH . 'sql'. DS . 'init' . DS . 'version';
        $current_ver = file_get_contents($ver_file);

        $host = $client->host;
        $cid  = $client->cid;

        $m_dbserver = Dbserver::get($input['ds_id']);
        if(!$m_dbserver){
            return $this->user_error('数据库服务器不存在!');
        }

        $db_config = $m_dbserver->getDbConfig();
        try{
           $db        = Db::connect($db_config); 
        }catch(Exception $e){
           return $this->user_error($e->getMessage());
        }

        $dc['cid']      = $cid;
        $dc['host']     = $host;
        $dc['hostname'] = $m_dbserver->ip;
        $dc['type']     = 'mysql';
        $dc['hostport'] = 3306;
        $dc['charset']  = 'utf8mb4';
        $dc['prefix']   = 'x360p_';
        $dc['database'] = !empty($input['database'])?$input['database']:'x360p_'.$host;
        $dc['username'] = !empty($input['username'])?$input['username']:$host.'_root';
        $dc['password'] = !empty($input['password'])?$input['password']:random_str(10);
        $this->ds = $m_dbserver;
        $this->dc = $dc;
        $this->db = $db;
        //创建数据库
        if(!$this->create_database($dc['database'])){
            return false;
        }
        //创建数据库用户
        if(!$this->create_db_user($dc['username'],$dc['password'],$dc['database'])){
            return false;
        }
        //创建数据库结构
        if(!$this->create_db_instruct($dc['database'])){
            return false;
        }
        //导入初始数据
        if(!$this->init_db_data($dc['database'])){
            return false;
        }

        //创建初始信息
        if(!$this->create_product_init_data($client)){
            return false;
        }


        //更新客户数据信息
        $client->is_db_install = 1;
        $client->current_version = $current_ver;
        $client->save();
        //写入数据库
        $result = $this->allowField(true)->save($dc);
        if(!$result) {
            return $this->user_error('写入database_config记录失败!'.$this->getLastSql());
        }

        //创建VIP User
        $result = $this->create_vip_user($client);
        if(!$result){
          return $this->user_error('创建VIP用户表信息失败!');
        }
        //更新dbserver db_nums
        $m_dbserver->db_nums++;
        $m_dbserver->save();

        $dc['id'] = $result;

        return $dc;
    }

    /**
     * 恢复出厂设置
     * @param  integer $og_id [description]
     * @return [type]         [description]
     */
    public function resetDb(Client $client,$og_id = 0){
      if($og_id != 0){
        return $this->clearDbData($og_id);
      }

      $ver_file = ROOT_PATH . 'sql'. DS . 'init' . DS . 'version';
      $current_ver = file_get_contents($ver_file);

      $hostname = $this->hostname;

      $m_dbserver = Dbserver::get(['ip'=>$hostname]);
      if(!$m_dbserver){
          return $this->user_error('数据库服务器不存在!');
      }

      $db_config = $m_dbserver->getDbConfig();

      $dc = $this->getData();

      try{
         $db        = Db::connect($db_config); 
      }catch(Exception $e){
         return $this->user_error($e->getMessage());
      }

      $this->ds = $m_dbserver;
      $this->db = $db;
      $this->dc = $dc;


      if(!$this->bakup_db_sql($dc['database'])){
        return false;
      }
      //创建数据库结构
      if(!$this->create_db_instruct($dc['database'])){
          return false;
      }
      //导入初始数据
      if(!$this->init_db_data($dc['database'])){
          return false;
      }

      //创建初始信息
      if(!$this->create_product_init_data($client)){
          return false;
      }

      return true;
    }


    public function clearDbData($og_id){
      //todo:清空数据库数据
      return true;
    }

    /**
     * 卸载数据库
     * @return [type] [description]
     */
    public function uninstallDb(){
        if(is_null($this->dc)){
            $this->dc = $this->data;
        }
        //保险起见先做个备份
        $bak_sql_dir = DATA_PATH.'delete_sql_bak'.DS.$this->dc['host'];
        mkdirss($bak_sql_dir);

        $bak_sql_file = $bak_sql_dir.DS.$this->dc['database'].'.sql';
        $root     = $this->dc['username'];
        $password = $this->dc['password'];
        $database = $this->dc['database'];
        $hostname = $this->dc['hostname'];
        $shell    = "mysqldump -h{$hostname} -u{$root} -p{$password} {$database} > {$bak_sql_file}";

        list($code, $output, $error) = Cmd::run($shell);
        if ($code !== 0) {
            return $this->user_error('删除前备份数据库出错:'.Cmd::StripWarning($error));
        }

        $m_dbserver = Dbserver::get(['ip'=>$this->dc['hostname']]);
        if(!$m_dbserver){
            return $this->user_error('数据库服务器不存在!');
        }

        $db_config = $m_dbserver->getDbConfig();
        try{
           $db        = Db::connect($db_config); 
        }catch(PDOException $e){
           return $this->user_error($e->getMessage());
        }

        $sql = "DROP DATABASE {$database}";

        try{
          $db->execute($sql);
        }catch(\PDOException $e){
          return $this->user_error($e->getMessage());
        }

        $m_dbserver->db_nums = $m_dbserver->db_nums -1;
        $m_dbserver->save();

        //删除vipuser
        $m_vip_user = new VipUser();

        $w_del['cid'] = $this->dc['cid'];

        $result = $m_vip_user->where($w_del)->delete(true);

        if(false === $result){
          return $this->sql_delete_error('vip_user');
        }

        return true;

    }

    /**
     * 创建数据库
     * @param  [type] $db_name [description]
     * @return [type]          [description]
     */
    protected function create_database($db_name){
        $sql  = sprintf("CREATE DATABASE IF NOT EXISTS `%s` DEFAULT CHARACTER SET %s",
                        $db_name,
                        $this->dc['charset']
                        );
        if(!$this->db->execute($sql)) {
           return $this->user_error('创建数据库失败,可能已经创建过');
        }

        return true;
    }


    protected function drop_database($db_name){
      $sql = sprintf("DROP DATABASE IF EXISTS `%s`",
              $db_name
              );
      $this->db->execute($sql);
      return true;
    }

    /**
     * 创建数据库用户
     * @param  [type] $username [description]
     * @param  [type] $password [description]
     * @return [type]           [description]
     */
    protected function create_db_user($username,$password,$db_name){
        $sql = "GRANT ALL PRIVILEGES ON {$db_name}.* to '{$username}'@'%' IDENTIFIED BY '{$password}'";
        try{
            $this->db->execute($sql);
            $this->db->execute('flush privileges');
        }catch(Exception $e){
            return $this->user_error('创建数据库用户失败!'.$e->getMessage());
        }
        $this->db->close();
        return true;
    }

    /**
     * 创建VIP用户信息表
     * @return [type] [description]
     */
    protected function create_vip_user(Client $client){
        $user['account']  = 'admin@'.$this->dc['host'];
        $user['name']     = $client->contact;
        $user['salt']     = random_str(6);
        $user['password'] = passwd_hash($this->dc['password'],$user['salt']);
        $user['avatar']   = 'http://s1.xiao360.com/common_img/avatar.jpg';
        if(is_mobile($client->tel)){
          $user['mobile']   = $client->tel;
        }
        $user['cid'] = $client->cid;

        try{
           (new VipUser)->save($user);
        }catch(\Exception $e){
          return $this->user_error($e->getMessage());
        }
        return true;
    }

    /**
     * 创建数据库结构
     * @return [type] [description]
     */
    protected function create_db_instruct($db_name){
        $config = $this->ds->getDbConfig();
        $sql_file = ROOT_PATH .  'sql'. DS . 'init' . DS . 'init_structure.sql';
        $root_pwd = $config['password'];
        $hostname = $config['hostname'];
        $shell  = "mysql -h{$hostname} -uroot -p{$root_pwd} {$db_name} < {$sql_file}";
        list($code, $output, $error) = Cmd::run($shell);
        if ($code !== 0) {
            return $this->user_error('创建数据库结构出错:'.Cmd::StripWarning($error));
        }

        return true;
    }


    /**
     * 备份数据库文件
     * @param  [type] $db_name [description]
     * @return [type]          [description]
     */
    protected function bakup_db_sql($db_name){
      $sql_file = ROOT_PATH.'sql'.DS.'bak'.DS.$db_name.'_'.date('YmdHi',time()).'.sql';
      $config = $this->ds->getDbConfig();
      $root_pwd = $config['password'];
      $hostname = $config['hostname'];
      $shell = "mysqldump -h{$hostname} -uroot -p{$root_pwd} {$db_name} > {$sql_file}";
      list($code, $output, $error) = Cmd::run($shell);
      if ($code !== 0) {
          return $this->user_error('备份数据库文件出错:'.Cmd::StripWarning($error));
      }
      return true;
    }

    /**
     * 初始化数据
     * @param  [type] $db_name [description]
     * @return [type]          [description]
     */
    protected function init_db_data($db_name){
        $sql_file = ROOT_PATH . 'sql' . DS . 'init' . DS . 'init_data.sql';
        return $this->execute_sql_file($sql_file,$db_name);

    }

    /**
     * 初始化数据
     * @param  [type] $db_name [description]
     * @return [type]          [description]
     */
    protected function init_db_data3($db_name){
        $config = $this->ds->getDbConfig();
        $sql_file = ROOT_PATH . DS . 'sql' . DS . 'init' . DS . 'init_data.sql';
        $root_pwd = $config['password'];
        $hostname = $config['hostname'];
        $shell  = "mysql -h{$hostname} -uroot -p{$root_pwd} --default-character-set=utf8 {$db_name} < {$sql_file}";
        list($code, $output, $error) = Cmd::run($shell);
        if ($code !== 0) {
            return $this->user_error('初始化数据库出错:'.Cmd::StripWarning($error));
        }
        return true;
    }

    /**
     * 创建客户产品初始化数据
     * @return [type] [description]
     */
    protected function create_product_init_data(Client $client){
        $gvar_og_id = gvar('og_id');
        gvar('og_id',0);
        //初始化数据库连接为客户的数据库连接
        $admin_database_config = config('database');
        $client_database_config = $this->dc;
        config('database',$client_database_config);
        Db::connect([],true);
        //创建默认参数机构名称
        $params = config('org_default_config.params');
        $params['org_name'] = $client->client_name;
        $c['og_id']    = 0;
        $c['cfg_name'] = 'params';
        $c['cfg_value'] = json_encode($params,JSON_UNESCAPED_UNICODE);
        $c['format']    = 'json';

        $m_config = new \app\api\model\Config();
        $result = $m_config->save($c);
        //创建一个默认部门(校区)
        $dept['og_id'] = 0;
        $dept['pid']   = 0;
        $dept['dpt_type'] = 1;
        $dept['bid']  = 1;
        $dept['dpt_name'] = $client->client_name;

        $m_dept = new \app\api\model\Department();
        $result = $m_dept->createDepartment($dept);

        //创建一个默认员工
        $e['open_account'] = 1;
        $e['user'] = [
            'og_id'     => 0,
            'account'   => 'admin',
            'avatar'    => 'http://s1.xiao360.com/common_img/avatar.jpg',
            'password'  => $this->dc['password'],
            'status'    => 1,
            'is_admin'  => 1
        ];

        $e['employee'] = [
            'og_id' => 0,
            'eid'   => 0,
            'bids'   => [1],
            'ename' => $client->contact,
            'is_on_job' => 1,
            'is_part_job'   => 0,
            'nick_name' => '管理员',
            'rids'  => [10],
            'sex'   => 1
        ];


        $e['employee']['mobile'] = $client->tel;

        if(is_email($client->email)){
            $e['employee']['email'] = $client->email;
        }
        $m_employee = new \app\api\model\Employee();

        $result = $m_employee->createEmployee($e,true);

        if(false === $result){
            return $this->user_error('创建员工失败:'.$m_employee->getError());
        }

        config('database',$admin_database_config);
        Db::connect([],true);
        gvar('og_id',$gvar_og_id);
        return true;
    }

    /**
     * 执行一个SQL文件
     * @param  [type] $sql_file [description]
     * @param  [type] $db_name  [description]
     * @return [type]           [description]
     */
    public function execute_sql_file($sql_file,$db_name = ''){
        if(is_null($this->dc)){
            $this->dc = $this->data;
        }

        if($db_name == ''){
            $db_name = $this->dc['database'];
        }

        $tablePre = $this->dc['prefix'];
        $charset  = $this->dc['charset'];

        $sqls = $this->split_sql($sql_file,$tablePre,$charset);

        if(!empty($sqls)){
            foreach($sqls as $sql){
                if(false === $this->execute_sql($sql,$db_name)){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 切分SQL文件成多个可以单独执行的sql语句
     * @param $file sql文件路径
     * @param $tablePre 表前缀
     * @param string $charset 字符集
     * @param string $defaultTablePre 默认表前缀
     * @param string $defaultCharset 默认字符集
     * @return array
     */
    protected function split_sql($file, $tablePre, $charset = 'utf8mb4', $defaultTablePre = 'x360p_', $defaultCharset = 'utf8mb4')
    {
        if (file_exists($file)) {
            //读取SQL文件
            $sql = file_get_contents($file);
            $sql = $this->format_sql($sql);
            $sqls = explode(";\n", $sql);
            return $sqls;
        }

        return [];
    }

    /**
     * sql格式化
     * @param  [type] $sql             [description]
     * @param  [type] $tablePre        [description]
     * @param  [type] $charset         [description]
     * @param  [type] $defaultTablePre [description]
     * @param  [type] $defaultCharset  [description]
     * @return [type]                  [description]
     */
    protected function format_sql($sql,$tablePre='x360p_',$charset='utf8mb4',$defaultTablePre='x360p_',$defaultCharset='utf8mb4'){
        $sql = str_replace("\r", "\n", $sql);
        $sql = str_replace("BEGIN;\n", '', $sql);//兼容 navicat 导出的 insert 语句
        $sql = str_replace("COMMIT;\n", '', $sql);//兼容 navicat 导出的 insert 语句
        $sql = str_replace($defaultCharset, $charset, $sql);
        $sql = preg_replace('/\/\*.*\*\/[;]/', '', $sql);  //替换注释
        $sql = preg_replace('/--\s.*\n/','',$sql);  //替换注释
        $sql = trim($sql);
        //替换表前缀
        $sql  = str_replace(" `{$defaultTablePre}", " `{$tablePre}", $sql);
        return $sql;
    }

    /**
     * 执行sql语句
     * @param  [type] $sql     [description]
     * @param  [type] $db_name [description]
     * @return [type]          [description]
     */
    protected function execute_sql($sql,$db_name = ''){
        if(is_null($this->dc)){
            $this->dc = $this->data;
        }

        if($db_name == ''){
            $db_name = $this->dc['database'];
        }
        if(is_null($this->use_db) || $db_name != $this->use_db){
            $this->db = Db::connect($this->dc);
            $this->use_db = $db_name;
        }
        $sql = trim($sql);

        try{
           //if(strpos(strtolower($sql),'select ') !== false){
            $result = $this->db->query($sql);
           //}else{
            //$result = $this->db->execute($sql);
           //} 
        }catch(\PDOException $e){
            return $this->user_error($e->getMessage());

        }catch(\Exception $e){
            return $this->user_error($e->getMessage());
        }
        return $result;
    }


    public function executeSql($sql,$db_name = ''){
        $sql = $this->format_sql($sql);
        $sqls = explode(";\n", $sql);
        $results = [];
        if(!empty($sqls)){
            try{
              foreach($sqls as $sql){
                $result = $this->execute_sql($sql,$db_name);
                if(false === $result){
                    return false;
                }
                array_push($results,$result);
              }  
            }catch(Exception $e){
               return $this->user_error($e->getMessage());
            }
            
        }

        return $results;
    }

}