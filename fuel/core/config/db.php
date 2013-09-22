<?php
return array(
	'active' => 'default',
	'default' => 
	array(
		'type' => 'pdo',
		'connection' => 
		array(
			'persistent' => false,
			'compress' => false,
			'dsn' => 'pgsql:host=localhost;port=3306;dbname=aaas_dev',
			'username' => 'root',
			'password' => 'test',
		),
		'identifier' => '`',
		'table_prefix' => '',
		'charset' => 'utf8',
		'enable_cache' => true,
		'profiling' => true,
	),
	'redis' => 
	array(
		'default' => 
		array(
			'hostname' => '127.0.0.1',
			'port' => 6379,
			'timeout' => NULL,
		),
	),
);
