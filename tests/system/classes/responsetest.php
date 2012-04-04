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
	
	public function testSend_and_Body()
	{
		$output_test_string = "Testing an output string";
		$this->response->body($output_test_string);
		ob_start();
		$this->response->send();
		$response_text = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($output_test_string, $response_text);
		$this->response->bodyClear();
	}
	
	public function testBodyPrepend()
	{
		$output_test_string = "Testing an output string";
		$this->response->body($output_test_string);
		
		// prepend:
		$prepend = 'This is ';
		$this->response->bodyPrepend($prepend);
		ob_start();
		$this->response->send();
		$response_text = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($prepend . $output_test_string, $response_text);
	}
	
	public function testBodyClear()
	{
		$this->response->body('testing');
		$this->response->bodyClear();
		ob_start();
		$this->response->send();
		$response_text = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('',$response_text );
	}
	
}