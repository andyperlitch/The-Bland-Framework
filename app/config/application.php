<?php defined('APPPATH') or die('No direct script access.');
/**
 * Application config file.
 * Used by Registry
 *
 * @package Config
 * @author Andrew Perlitch
 */

return array(

	'local' => array(
		
		'pdo' => array(
			'dsn'      => 'mysql:dbname=local_HSS;host=localhost',
			'username' => 'root',
			'password' => 'root',
			'options'  => array(
				
			),
		),
		
	),
	
	'server_testing' => array(
		
		'pdo' => array(
			'dsn'      => 'mysql:dbname=local_HSS;host=localhost',
			'username' => 'root',
			'password' => 'root',
			'options'  => array(
				
			),
		),
		
	),
	
	'production' => array(
		
		'pdo' => array(
			'dsn'      => 'mysql:dbname=local_HSS;host=localhost',
			'username' => 'root',
			'password' => 'root',
			'options'  => array(
				
			),
		),
		
	),
	
//	'example' => array(
//		
//		'pdo' => array(
//			'dsn'      => 'mysql:dbname=[DATABASE_NAME];host=localhost',
//			'username' => '[DATABASE_USERNAME]',
//			'password' => '[DATABASE_PASSWORD]'
//		)
//		
//	),
	
);