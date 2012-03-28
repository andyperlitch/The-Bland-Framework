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
		
		'pdo_dsn'      => 'mysql:dbname=local_HSS;host=localhost',
		'pdo_user' => 'root',
		'pdo_pass' => 'root',
		'pdo_options'  => array(
			
		),
	),
	
	'server_testing' => array(
		
		'pdo_dsn'      => 'mysql:dbname=local_HSS;host=localhost',
		'pdo_user' => 'root',
		'pdo_pass' => 'root',
		'pdo_options'  => array(
			
		),
	),
	
	'production' => array(
		
		'pdo_dsn'      => 'mysql:dbname=local_HSS;host=localhost',
		'pdo_user' => 'root',
		'pdo_pass' => 'root',
		'pdo_options'  => array(
			
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