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

// define test path
if( ! defined('UNITTEST_PATH')) {
	define('UNITPATH',preg_replace( '/([-\w]+\/)$/' , '' , SYSPATH ).'tests/');
}

require_once(PHPUNIT_PATH.'Framework/TestSuite.php');
require_once(PHPUNIT_PATH.'TextUI/TestRunner.php');

// <----- **** INCLUDE OTHER TEST SUITES HERE ****
require_once('system/classes/applicationtest.php');
require_once('system/classes/requesttest.php');
require_once('system/classes/sessiontest.php');
require_once('system/classes/autoloadertest.php');
require_once('system/classes/commandresolvertest.php');


class AppTests{
	
	public static function main() {
		PHPUnit_TextUI_TestRunner::run( self::suite() );
	}
	
	public static function suite() {
		$ts = new PHPUnit_Framework_TestSuite( 'Request Classes' );
		
		// <----- **** ADD OTHER TEST SUITE NAMES HERE ****
		$ts->addTestSuite('ApplicationRegistryTest');
		$ts->addTestSuite('RequestRegistryTest');
		$ts->addTestSuite('SessionRegistryTest');
		$ts->addTestSuite('AutoLoaderTest');
		$ts->addTestSuite('CommandResolverTest');
		
		return $ts;
		
	}
	
}

if (PHPUnit_MAIN_METHOD == 'AppTests::main') { 
	AppTests::main();
}

?>