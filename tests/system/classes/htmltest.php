<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/html.php';
class HTMLTest extends PHPUnit_Framework_TestCase {
	
	private $html;
	
	public function setUp()
	{
		$this->html = new HTML();
	}
	
	public function tearDown()
	{
		$this->html = null;
	}
	
	public function testScript()
	{
		$script = $this->html->script('test.js');
		$this->assertEquals('<script type="text/javascript" charset="utf-8" src="test.js"></script>',$script);
	}
	
	public function testStyle()
	{
		$style = $this->html->style('test.css','screen','main styles');
		$style2 = $this->html->style('test.css','print');
		$this->assertEquals('<link rel="stylesheet" href="test.css" charset="utf-8" media="screen" title="main styles">',$style);
		$this->assertEquals('<link rel="stylesheet" href="test.css" charset="utf-8" media="print">',$style2);
	}
	
}