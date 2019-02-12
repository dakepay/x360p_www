<?php

//host: pc.lantel.pro.x360.com
return [
    'default_wxmp_name' => '学习管家服务号',
    'domain'			=> 'pro.x360.com',
    'base_dir'  		=> 'ui',
    'default'   		=> 'pc',
    'sub_domains'		=> ['m','student'],
    'business_domain'	=> ['admin','vip'],		//业务域名
    'business_base_dir'	=> 'bui',				//业务域名目录名
    'forbidden_domain'		=> ['root','fuck','manage','vip','admin']
];