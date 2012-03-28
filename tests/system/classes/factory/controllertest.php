<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/factory/controller.php';
class Factory_ControllerTest extends PHPUnit_Framework_TestCase {
	
	private $factory;
	
	public function setUp()
	{
		$this->factory = new Factory_Controller();
	}
	
	public function tearDown()
	{
		$this->factory = null;
	}
	
	public function testBuild()
	{
		$controller = $this->factory->build(
				array(),
				array(),
				array()
			);
		$this->assertInstanceOf('Controller',$controller);
	}
	
}