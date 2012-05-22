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
		'live' => false,
		'db_host'     => 'localhost',
		'db_user'     => 'root',
		'db_pass'     => 'root',
		'db_dbname'   => 'bland_framework',
		'options'  => array(
			
		),
	),
	
	'server_testing' => array(
		'live'         => false,
		'db_host'     => 'localhost',
		'db_user'     => 'root',
		'db_pass'     => 'root',
		'db_dbname'       => 'bland_framework',
		'options'  => array(
			
		),
	),
	
	'production' => array(
		'live'         => true,
		'db_host'      => 'localhost',
		'db_user'     => 'root',
		'db_pass'     => 'root',
		'db_dbname'       => 'bland_framework',
		'options'  => array(
			
		),
	),
	'phpunit' => array(
		'live' => false,
		'db_host'     => 'localhost',
		'db_user'     => 'phpunit',
		'db_pass'     => 'phpunit',
		'db_dbname'   => 'phpunit_db',
		'options'  => array(
			
		),
		'js_dir' => ABSPATH.'/media/js/',
		'css_dir' => ABSPATH.'/media/css/',
		'main_css' => 'main.css',
		'requirejs' => 'require.js',
	)
	
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