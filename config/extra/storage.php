<?php
//上传相关配置
return [
	'engine'		=>  'qiniu',
	'file'			=> [
		'prefix'	=> '/data/uploads/',
	],
	'qiniu'			=> [
		'access_key'	=> 'p9mUPzEN5ihLHctwvBIk5w9MBckHvFSrXadVRlPY',//ak
		'secret_key'	=> 'UJRv2IaSnsFUmZyXmYWyhpcrPW7WIYnslnT749Fh',
		'bucket'		=> 'ygwqms',//
		'prefix'		=> 'qms/',		//KEY前缀
		'domain'		=> 'http://s10.xiao360.com/',//domain
	],
	'alioss'		=> [
	    'access_id'     =>  'LTAI42SuyAV091Dm',
	    'access_key'    =>  'oTiPQhKLIze57grFZ7aTiCGzZa6Xiv',
	    'host'          =>  'http://mine-bucket.oss-cn-beijing.aliyuncs.com',
	    'prefix'        =>  'qms',
	    'bucket'		=> 'ygwqms',
	],
];

