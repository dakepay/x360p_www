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
	]
];