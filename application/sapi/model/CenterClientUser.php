<?php

namespace app\sapi\model;

class CenterClientUser extends Center
{
	protected $table = 'pro_client_user';

	public static function LoadClientByUser($account){
		$client = [
	        'defined'       => false,       //是否明确
	        'defined_way'   => 'domain',    //明确方式:domain 是通过域名,header 是通过http头 , debug 是调试 , login 登录方式
	        'domain'        => '',          //客户的四级域名
	        'subdomain'     => '',          //客户的五级域名
	        'database'      => [],          //数据库连接配置
	        'info'          => [],          //基本信息 
	        'cid'           => 0,           //客户ID
            'og_id'         => 0,           //机构ID
	    ];
		$w['account'] = $account;
        $client_account = Self::get($w);

        if($client_account){
        	$w_dc['cid'] = $client_account['cid'];
            $m_client= CenterClient::get($client_account['cid']);
            $m_client_db_config = CenterDatabaseConfig::get($w_dc);
           
            if($m_client && $m_client_db_config){
            	$client_info = $m_client->toArray();
            	$client_db_config   = $m_client_db_config->toArray();
            	config('database',$client_db_config);
            	$cid = $client_account['cid'];
            	$client_info['params'] = isset($client_info['params']) && !empty($client_info['params']) ?
                    json_decode($client_info['params'], true) : [];
	            $client_info['is_expire'] = isset($client_info['expire_day']) && strtotime($client_info['expire_day']) - time() > 0 ? false : true;
                $client['defined']     = true;
                $client['defined_way'] = 'account';   //通过账号
                $client['domain']       = $client_info['host'];
                $client['subdomain']    = 'student';
                $client['database']     = $client_db_config;
                $client['info']         = $client_info; 
                $client['cid']          = $cid;
                $client['og_id']        = $client_info['og_id'];
            }
        }
        return $client;
	}
}