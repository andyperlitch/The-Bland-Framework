<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
class AutoLoaderTest extends PHPUnit_Framework_TestCase {
	
	public function setUp()
	{
		spl_autoload_register(array('AutoLoader', 'autoload'));
	}
	
	public function tearDown()
	{
		
	}
	
	public function testAutoload_System()
	{
		AutoLoader::autoload('Email');
		$this->assertTrue(class_exists('Email'));
	}
	
	public function testAutoload_App()
	{
		AutoLoader::autoload('Test_Autoload');
		$this->assertTrue(class_exists('Test_Autoload'));
	}
	
	public function testAutoload_Fail()
	{
		try {
			$bad = new Spaghettttiiiooooooossssss;
		} catch (AutoloadException $e) {
			return;
		}
		$this->fail("Expecting AutoloadException.");
		
	}
	
}