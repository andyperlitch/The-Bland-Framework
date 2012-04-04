<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Application config file.
 * Used by Registry
 *
 * @package Config
 * @author Andrew Perlitch
 */

return array(

	'local' => array(
		'domain'   => 'http://localhost:8888/',
		'pdo_dsn'  => 'mysql:dbname=local_HSS;host=localhost',
		'pdo_user' => 'root',
		'pdo_pass' => 'root',
		'pdo_options'  => array(
			
		),
		'js_dir'   => 'media/js/',
		'css_dir'  => 'media/css/',
		'main_css' => 'main.css',
		'main_js'  => 'main.js',
		'jquery'   => 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js',
	),
	
	'server_testing' => array(
		'domain'   => 'http://test.example.com/',		
		'pdo_dsn'      => 'mysql:dbname=local_HSS;host=localhost',
		'pdo_user' => 'root',
		'pdo_pass' => 'root',
		'pdo_options'  => array(
			
		),
		'js_dir'   => 'media/js/',
		'css_dir'  => 'media/css/',
		'main_css' => 'main.css',
		'main_js'  => 'main.js',
		'jquery'   => 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js',
	),
	
	'production' => array(
		'domain'   => 'http://www.example.com/',		
		'pdo_dsn'      => 'mysql:dbname=local_HSS;host=localhost',
		'pdo_user' => 'root',
		'pdo_pass' => 'root',
		'pdo_options'  => array(
			
		),
		'js_dir'   => 'media/js/',
		'css_dir'  => 'media/css/',
		'main_css' => 'main.css',
		'main_js'  => 'main.js',
		'jquery'   => 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
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