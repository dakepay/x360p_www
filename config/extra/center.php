<?php
return [
	'wxpay'	=> 		[
		'app_id'	=> 'wx577f93940693c8e9',
		'app_secret'=> '89eb097b1aa398a996052827a6f2539d',
		'mch_id'	=> '1267213801',
		'key'		=> '4f64df7be3bebf50f909a67087ea5cb3'
	],
	'oldalipay'	=> [
		// 证书路径
    	'cacert' 	=> CONF_PATH.'alipay_key/cacert.pem',
	    // 支付宝商家 ID
	    'partner'   => '2088021987368101',
	    // 支付宝商家 KEY
	    'key'       => '2ws0ru1dt7sp3jktq7k39lomge3nf09n',
	    // 支付宝商家注册邮箱
	    'seller_email' => 'pay@t910.com'
	],
	'alipay'	=>	[
	    // 支付宝商家 ID
	    //'app_id'      => '2088311256457556',
	    'app_id'		=> '	2016080300159653',//sandbox appid
	    'ali_public_key'	=> 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB',
	    'private_key'		=> 'MIICWwIBAAKBgQDSZcRFpMu4sQqpB6urqljvYYAphQDFrptodyBrDAgaIX1foFNrjUOYuo/ZMfmhoYkl7+rs+Tlq6K091jWiXZx6+FVIOHM7VF34bZ1LhL5Sh+ni5UDYiRtQLjBJjojtcSfHbmmh/G+GOKzQVVkDiovWnkY2hqq/JCp9R/QBBuxrPwIDAQABAoGANrUxiO5l7ptSa0tMTzHXOD+BBMrJvZ0+WbaIHm2debX1lLTqnA+6YO850j8VavrG3693xbC0RmFEEs0tWw++TR41NneSQIWHkN2LMRSi2d5OAmdhCBaG4dTu8DyC7P1sKrhZPxhSpRUfC8fgziHz3Xu6JlLUKZQGor5xrl0+mwECQQD9hMu7JRn9WhDQTCRbdOGtJP219zta64TeOJ3pOheOmZoYzqi6BmibmcgO4mJBP9DRc9hGEO1uyM8QrqckvYW/AkEA1HTtsB2mo35z/76CEMk6/wkWUVotSKRVheMSbG4TL9v6GYAfI0mm+9HlVXZQe8IEwS9ME0kRDlO+NrlEsBF6gQJAP4Ghi2ra3NVP+u3n+aUI11e52nhpPPhcm1IxdHgh6I3fxTEXoSnz0G6wZ9Ib7N5wj9dmKP8aizLwc+xcYF69fwJAcxVTm060cWOSGjpr4gPe/T9C45ZhaTP7T6cM18dYhVg3RZDtTQQce9Pa0kxglGkogizInLm7j3M1WhdJacZOAQJANSsBnA+Iuhf2sGNG4wDHTRtV1BgGpXJwPofRjjedvB5oAF79weGcNJxoek4qLBuHcRRlhZPzIGGqMeo5bbqkRA=='
	],
	'client_expire_warn_days'	=> 30	//客户到期提醒天数
];