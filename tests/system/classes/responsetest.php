<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/response.php';
class ResponseTest extends PHPUnit_Framework_TestCase {
	
	private $response;
	
	public function setUp()
	{
		$this->response = new Response();
	}
	
	public function tearDown()
	{
		$this->response = null;
	}
	
	public function testSend()
	{

		$output_test_string = "Testing an output string";
		$this->response->body($output_test_string);
		ob_start();
		$this->response->send();
		$response_text = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($output_test_string, $response_text);
	}
	
}