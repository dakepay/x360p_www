<?php

namespace app\admapi\model;

class Version extends Base
{

	protected $_logs = [];
	/**
	 * 添加一个版本
	 * @param [type] &$input [description]
	 */
	public function addVersion(&$input)
	{
		$need_fields = ['ver','content','publish_date'];

		if(!$this->checkInputParam($input,$need_fields)){
			return false;
		}

		$input['ver'] = trim($input['ver']);
		$input['publish_date'] = format_int_day($input['publish_date']);

		//判断版本的相关文件是否上传
		if(!$this->checkVersionFilesUploaded($input['ver'])){
			return $this->user_error('请检查版本文件是否上传!版本号:'.$input['ver']);
		}

		$vid = $this->save($input);

		if(!$vid){
			return $this->sql_add_error('version');
		}

		//执行UI目录文件转移
		$this->copyVersionUiFiles($input['ver']);

		//执行最新版本的init sql文件转移
		$this->updateVersionSqlFiles($input['ver']);

		return true;
	}

	/**
	 * 根据版本ID删除版本号
	 * @param  [type] $vid [description]
	 * @return [type]      [description]
	 */
	public function delVersion($vid){
		$version = $this->find($vid);
		if(!$version){
			return $this->user_error('版本ID不存在!');
		}

		$result = $version->delete();

		return $result;
	}



	/**
	 * 批量升级版本
	 * @param  [type]  $version  [description]
	 * @param  integer $page     [description]
	 * @param  integer $pagesize [description]
	 * @return [type]            [description]
	 */
	public function batUpgradeVersion($version,$page = 1,$pagesize = 10){
		if(!preg_match('/^\d+\.\d+\.\d+$/',$version)){
			return $this->user_error('版本号不正确!');
		}

		$w['ver'] = $version;

		$ex_ver = $this->where($w)->find();
		if(!$ex_ver){
			return $this->user_error('版本号不存在!');
		}

		$versions  = $this->order('vid ASC')->select();
		$ver_list  = [];
		foreach($versions as $v){
			array_push($ver_list,$v['ver']);
		}
		$m_client = new Client();
		$client_list = $m_client->where(['is_db_install'=>1,'og_id'=>0,'cid'=>['GT',2]])->order('cid ASC')->page($page,$pagesize)->select();
		$do_nums = 0;

		if($client_list){
			foreach($client_list as $client){
				$this->loopUpgradeVersion($client,$ver_list,$version);
				$do_nums++;
			}
		}
		return $do_nums;
	}

	/**
	 * 循环升级
	 * @param  [type] $client    [description]
	 * @param  [type] &$ver_list [description]
	 * @param  [type] $version   [description]
	 * @return [type]            [description]
	 */
	public function loopUpgradeVersion(&$client,&$ver_list,$version){
		$start_ver = $client->current_version;
		foreach($ver_list as $ver){
			$client_version = $client->current_version;
			$int_client_version = int_version($client_version);
			$int_ver            = int_version($ver);
            /*
			echo('client_version:'.$client_version);
			echo('int_client_version:'.$int_client_version);
			echo(',ver:'.$ver.',int_ver:'.$int_ver);
			echo('----------');
            */
			if($int_client_version >= $int_ver){
				continue;
			}
			$result = $this->upgradeVersion($client,$ver);
			if($result){
				$client->current_version = $ver;
			}
		}
		return true;
	}
	/**
	 * 给指定的客户升级版本号
	 * @param  [type] $cid     [description]
	 * @param  [type] $version [description]
	 * @return [type]          [description]
	 */
	public function upgradeVersion($cid,$version){
		if($cid instanceof Client){
			$client = $cid;
			$cid    = $client->cid;
		}else{
			$client   = Client::get($cid,true);
		}
		if(!$client){
			$log = '客户ID:'.$cid.'不存在!';
			$this->addUpgradeFailureLog('ID:'.$cid,'-',$version,$log);
			return $this->user_error($log);
		}

		$dc = DatabaseConfig::get(['cid'=>$cid],true);

		if(!$dc){
			$log = '客户ID:'.$cid.'的数据库连接配置文件不存在!';
			$this->addUpgradeFailureLog('ID:'.$cid,'-',$version,$log);
			return $this->user_error($log);
		}

		$from_ver = $client->current_version;
		$to_ver   = $version;

		$int_from_ver = int_version($from_ver);
		$int_to_ver   = int_version($to_ver);

		if($int_from_ver >= $int_to_ver){
			$log = '不能从高版本升级到低版本!';
			$this->addUpgradeInfoLog($client->client_name,$from_ver,$to_ver,$log);
			return $this->user_error($log);
		}

		if($from_ver == $to_ver){
			$log = '版本号相同';
			$this->addUpgradeInfoLog($client->client_name,$from_ver,$to_ver,$log);
			return true;
		}

		$patch_sql_file = RELEASE_PATH.$to_ver.DS.'sql'.DS.'patch.sql';

		if(is_file($patch_sql_file)){
			$result = $dc->execute_sql_file($patch_sql_file);
			/*
			if(false === $result){
				$this->addUpgradeFailureLog($client->client_name,$from_ver,$to_ver,$dc->getError());
				return $this->user_error($dc->getError());
			}*/
		}

		$this->startTrans();
		try{
			$vuh['cid']      = $cid;
			$vuh['from_ver'] = $from_ver;
			$vuh['to_ver']   = $to_ver;
			$m_vuh = new VersionUpgradeHistory();
			$m_vuh->save($vuh);
			$client->current_version = $to_ver;
			$result = $client->save();
			$this->commit();
			
			$this->addUpgradeSuccessLog($client->client_name,$from_ver,$to_ver);
		}catch(Exception $e){
			$this->rollback();
			$this->addUpgradeFailureLog($client->client_name,$from_ver,$to_ver,$e->getMessage());
			return $this->user_error($e->getMessage());
		}
		return true;
	}

	protected function addUpgradeSuccessLog($client_name,$from_ver,$to_ver){
		$log = sprintf("【√】%s 从 版本 %s 升级到 %s,升级成功",$client_name,$from_ver,$to_ver);
		array_push($this->_logs,$log);
	}

	protected function addUpgradeFailureLog($client_name,$from_ver,$to_ver,$reason){
		$log = sprintf("【×】%s 从 版本 %s 升级到 %s,升级失败:%s",$client_name,$from_ver,$to_ver,$reason);
		array_push($this->_logs,$log);
	}

	protected function addUpgradeInfoLog($client_name,$from_ver,$to_ver,$info){
		$log = sprintf("【-】%s 从 版本 %s 升级到 %s,跳过:%s",$client_name,$from_ver,$to_ver,$info);
		array_push($this->_logs,$log);
	}

	/**
	 * 获得省级日志
	 * @return [type] [description]
	 */
	public function getUpgradeLogs(){
		return $this->_logs;
	}


	/**
	 * 判断文件是否上传
	 * @param  [type] $ver [description]
	 * @return [type]      [description]
	 */
	protected function checkVersionFilesUploaded($ver)
	{
		$ver_folder = RELEASE_PATH.$ver;
		$ver_ui_folder = $ver_folder . DS .'ui';

		if(is_dir($ver_folder) && is_dir($ver_ui_folder) && !is_dir_empty($ver_ui_folder)){
			return true;
		}

		return false;
	}
	/**
	 * 拷贝UI文件
	 * @param  [type] $ver [description]
	 * @return [type]      [description]
	 */
	protected function copyVersionUiFiles($ver){
		$ver_folder = RELEASE_PATH.$ver;
		$ver_ui_folder = $ver_folder . DS .'ui';
		$public_ui_ver_folder = PUBLIC_PATH.'ui'.DS.$ver.DS;
		copy_dir($ver_ui_folder,$public_ui_ver_folder);
	}

	/**
	 * 更新版本SQL文件
	 * @param  [type] $ver [description]
	 * @return [type]      [description]
	 */
	protected function updateVersionSqlFiles($ver){
		$ver_sql_folder = RELEASE_PATH.$ver.DS.'sql';
		$ver_init_struct_sql_file = $ver_sql_folder.DS.'init_structure.sql';
		$ver_init_data_sql_file   = $ver_sql_folder.DS.'init_data.sql';
		$dst_init_struct_sql_file = ROOT_PATH .  'sql'. DS . 'init' . DS . 'init_structure.sql';
		$dst_init_data_sql_file   = ROOT_PATH .  'sql'. DS . 'init' . DS . 'init_data.sql';
		$ver_file = ROOT_PATH.'sql'.DS.'init'.DS.'version';

		try{
			if(is_file($ver_init_struct_sql_file)){
				copy($ver_init_struct_sql_file,$dst_init_struct_sql_file);
			}
			if(is_file($ver_init_data_sql_file)){
				copy($ver_init_data_sql_file,$dst_init_data_sql_file);
			}
			file_put_contents($ver_file,$ver);
		}catch(Exception $e){
			log_write($e->getMessage());
			return false;
		}
		return true;
	}
}