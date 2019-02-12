<?php
/**
 * Author: luo
 * Time: 2017-12-04 15:00
**/

namespace app\admapi\model;

class Dbserver extends Base
{
	 protected $db_config = [
        'type'     => 'mysql',
        'hostname' => '127.0.0.1',
        'database' => '',
        'username' => 'root',
        'password' => 'SqlRootLantel2017',
        'hostport' => 3306,
        'charset'  => 'utf8mb4',
        'prefix'   => 'x360p_',
    ];

    /**
     * 获取数据库配置
     * @return [type] [description]
     */
	public function getDbConfig(){
		$db_config = $this->db_config;
		$db_config['password'] = $this->getData('root_pwd');
		$db_config['hostname'] = $this->getData('ip');
		$db_config['hostport'] = $this->getData('port');
		return $db_config;
	}
}