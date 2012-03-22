<?php defined('APPPATH') or die('No direct script access.');

/**
 * Config for PDO access
 * 
 * 
*/

return array(
	/**
	 * The following fields are required in each of the options below
	 *
	 * string   dsn          dsn string for PDO connection (contains dbname, host)
	 * string   username     database username
	 * string   password     database password
	 *
	 */
	'local' => array(
		
		'dsn'      => 'mysql:dbname=local_HSS;host=localhost',
		'username' => 'root',
		'password' => 'root'
		
	),
	
	'server_testing' => array(
		
		'dsn'      => 'mysql:dbname=testing_HSS;host=localhost',
		'username' => 'root',
		'password' => 'root'
		
	),
	
	'server_live' => array(
		
		'dsn'      => 'mysql:dbname=live_HSS;host=localhost',
		'username' => 'root',
		'password' => 'root'
		
	)
	
);