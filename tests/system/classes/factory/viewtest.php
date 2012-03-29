<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/factory/view.php';
class Factory_ViewTest extends PHPUnit_Framework_TestCase {
	
	private $fv;
	
	public function setUp()
	{
		$this->fv = new Factory_View();
	}
	
	public function tearDown()
	{
		$this->fv = null;
	}
	
	public function testBuild()
	{
		$response = $this->fv->build('test', array(
			'variable' => 'foo'
		));
		$this->assertEquals("This is a view with a variable: 'foo'.",$response );
	}
	
}