<?php
return [
	'default' => [
		'db_host'	  => Env('DB_HOST', '127.0.0.1'), 	//地址
		'db_port'	  => Env('DB_PORT', '3306'),        //端口
		'db_database' => Env('DB_DATABASE', 'bayshop'), //数据库名称
		'db_username' => Env('DB_USERNAME', 'root'),    //用户
		'db_password' => Env('DB_PASSWORD', 'root'),  	//密码
		'db_charset'  => Env('DB_CHARSET', 'utf8'),     //字符集
	],
];