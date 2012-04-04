<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/factory/model.php';
class Factory_ModelTest extends PHPUnit_Framework_TestCase {
	
	private $fm;
	
	public function setUp()
	{
		$this->fm = new Factory_Model(new Config('local'));
	}
	
	public function tearDown()
	{
		$this->fm = null;
	}
	
	public function testBuild()
	{
		$model = $this->fm->build('test');
		$this->assertInstanceOf('Model',$model );
	}
	
}