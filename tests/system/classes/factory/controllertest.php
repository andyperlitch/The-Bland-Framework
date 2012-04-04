<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include_once SYSPATH.'classes/factory.php';
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
				array(
					'REQUEST_URI' => '/'
				),
				array(),
				array(),
				'local'
			);
		$this->assertInstanceOf('Controller',$controller);
	}
	
	public function testBuild_badRequest()
	{
		$controller = $this->factory->build(
				array(
					'REQUEST_URI' => '/spaghettimonstrerrrrsrsssssasrlkaslkrjalskdjralsdkrjalsdkr'
				),
				array(),
				array(),
				'local'
			);
			
		$controller2 = $this->factory->build(
				array(
					'REQUEST_URI' => '/asdfkajsdf/J*(#)#(?#J#IU#I)J?@J#?###?#/'
				),
				array(),
				array(),
				'local'
			);
			
		$controller3 = $this->factory->build(
				array(
					'REQUEST_URI' => '/home/asdfkajsdf/J*(#)#(?#J#IU#I)J?@J#?###?#/'
				),
				array(),
				array(),
				'local'
			);
		$this->assertInstanceOf('Controller_Error',$controller);
		$this->assertInstanceOf('Controller_Error',$controller2);
		$this->assertInstanceOf('Controller_Error',$controller3);
		
	}
	
}