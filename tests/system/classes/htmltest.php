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
		$script2 = $this->html->script('test.html','text/html');
		$this->assertEquals('<script src="test.js"></script>',$script);
		$this->assertEquals('<script type="text/html" src="test.html"></script>',$script2);
	}
	
	public function testStyle()
	{
		$style = $this->html->style('test.css','screen','main styles');
		$style2 = $this->html->style('test.css','print');
		$this->assertEquals('<link rel="stylesheet" href="test.css" media="screen" title="main styles">',$style);
		$this->assertEquals('<link rel="stylesheet" href="test.css" media="print">',$style2);
	}
	
}