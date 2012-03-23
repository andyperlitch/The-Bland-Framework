<?php

// define main method
if( ! defined('PHPUnit_MAIN_METHOD')) {
	define('PHPUnit_MAIN_METHOD','AppTests::main');
}

// define phpunit path
if ( ! defined('PHPUNIT_PATH')) {
	define('PHPUNIT_PATH','/Applications/MAMP/bin/php5.3/share/pear/PHPUnit/');	
}

// include bootstrap file
include '/Users/andyperlitch/Sites/BlandFramework/The-Bland-Framework/app/bootstrap.php';
// check that it was included properly
if( ! defined('SYSPATH') ) die('TEST FAILED TO INIT: bootstrap.php file not properly included! Check the path in '.__FILE__.'
');


require_once(PHPUNIT_PATH.'Framework/TestSuite.php');
require_once(PHPUNIT_PATH.'TextUI/TestRunner.php');
require_once('system/classes/registrytest.php');
// <----- **** INCLUDE OTHER TEST SUITES HERE ****

class AppTests{
	
	public static function main() {
		PHPUnit_TextUI_TestRunner::run( self::suite() );
	}
	
	public static function suite() {
		$ts = new PHPUnit_Framework_TestSuite( 'Request Classes' );
		$ts->addTestSuite('ApplicationRegistryTest');
		// <----- **** ADD OTHER TEST SUITE NAMES HERE ****
		return $ts;
		
	}
	
}

if (PHPUnit_MAIN_METHOD == 'AppTests::main') { 
	AppTests::main();
}

?>