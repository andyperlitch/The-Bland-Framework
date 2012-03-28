<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/request.php';
class RequestTest extends PHPUnit_Framework_TestCase {
	
	private $ajaxPostRequest;
	private $ajaxGetRequest;
	private $postRequest;
	private $getRequest;
	private $allRequests = array();
	
	public function setUp()
	{
		
		
		$this->allRequests[] = $this->ajaxPostRequest = new Request(
			// server
			array(
				// user agent
				'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:11.0) Gecko/20100101 Firefox/11.0',
				// cookie
				'HTTP_COOKIE' => 'SESSION=34d132dee44d5f036087aabaf82c65f6; __utma=111872281.39029251.1320881510.1328393513.1328731845.57; __utmz=111872281.1320881510.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); re_ret=2; lang=cd3422f8124cf4e1fe8430abbf24e94b218352cc%7Een; SQLiteManager_currentLangue=2; PHPSESSID=34d132dee44d5f036087aabaf82c65f6; session=bdc9af34caf213bb06792ca5fef16e1e34500024%7E4f67e13101bcc1-50459547',
				// ajax request
				'HTTP_X_REQUESTED_WITH' => 'xmlhttprequest',
				// IP address
				'REMOTE_ADDR' => '127.0.0.1',
				// method
				'REQUEST_METHOD' => 'POST',
				// query string
				'QUERY_STRING' => '',
				// uri
				'REQUEST_URI' => '/controller/action/id',
			),
			// get
			array(),
			// post
			array(
				'post_var_1' => 'post_var_1_value',
				'post_var_2' => 'post_var_2_value',
				'post_var_3' => 'post_var_3_value',
				'post_var_4' => 'post_var_4_value',
			)
		);
		$this->allRequests[] = $this->ajaxGetRequest = new Request(
			// server
			array(
				// user agent
				'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:11.0) Gecko/20100101 Firefox/11.0',
				// cookie
				'HTTP_COOKIE' => 'SESSION=34d132dee44d5f036087aabaf82c65f6; __utma=111872281.39029251.1320881510.1328393513.1328731845.57; __utmz=111872281.1320881510.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); re_ret=2; lang=cd3422f8124cf4e1fe8430abbf24e94b218352cc%7Een; SQLiteManager_currentLangue=2; PHPSESSID=34d132dee44d5f036087aabaf82c65f6; session=bdc9af34caf213bb06792ca5fef16e1e34500024%7E4f67e13101bcc1-50459547',
				// ajax request
				'HTTP_X_REQUESTED_WITH' => 'xmlhttprequest',
				// IP address
				'REMOTE_ADDR' => '127.0.0.1',
				// method
				'REQUEST_METHOD' => 'GET',
				// query string
				'QUERY_STRING' => '',
				// uri
				'REQUEST_URI' => '/controller/action/id',
			),
			// get
			array(
				'get_var_1' => 'get_var_1_value',
				'get_var_2' => 'get_var_2_value',
			),
			// post
			array(
				
			)
		);
		$this->allRequests[] = $this->postRequest = new Request(
			// server
			array(
				// user agent
				'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:11.0) Gecko/20100101 Firefox/11.0',
				// cookie
				'HTTP_COOKIE' => 'SESSION=34d132dee44d5f036087aabaf82c65f6; __utma=111872281.39029251.1320881510.1328393513.1328731845.57; __utmz=111872281.1320881510.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); re_ret=2; lang=cd3422f8124cf4e1fe8430abbf24e94b218352cc%7Een; SQLiteManager_currentLangue=2; PHPSESSID=34d132dee44d5f036087aabaf82c65f6; session=bdc9af34caf213bb06792ca5fef16e1e34500024%7E4f67e13101bcc1-50459547',
				// IP address
				'REMOTE_ADDR' => '127.0.0.1',
				// method
				'REQUEST_METHOD' => 'POST',
				// query string
				'QUERY_STRING' => '',
				// uri
				'REQUEST_URI' => '/controller/action/id',
			),
			// get
			array(
				
			),
			// post
			array(
				'post_var_1' => 'post_var_1_value',
				'post_var_2' => 'post_var_2_value',
				'post_var_3' => 'post_var_3_value',
				'post_var_4' => 'post_var_4_value',
			)
		);
		$this->getRequest = new Request(
			// server
			array(
				// user agent
				'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:11.0) Gecko/20100101 Firefox/11.0',
				// cookie
				'HTTP_COOKIE' => 'SESSION=34d132dee44d5f036087aabaf82c65f6; __utma=111872281.39029251.1320881510.1328393513.1328731845.57; __utmz=111872281.1320881510.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); re_ret=2; lang=cd3422f8124cf4e1fe8430abbf24e94b218352cc%7Een; SQLiteManager_currentLangue=2; PHPSESSID=34d132dee44d5f036087aabaf82c65f6; session=bdc9af34caf213bb06792ca5fef16e1e34500024%7E4f67e13101bcc1-50459547',
				// IP address
				'REMOTE_ADDR' => '127.0.0.1',
				// method
				'REQUEST_METHOD' => 'GET',
				// query string
				'QUERY_STRING' => '',
				// uri
				'REQUEST_URI' => '/controller/action/id',
			),
			// get
			array(
				'get_var_1' => 'get_var_1_value',
				'get_var_2' => 'get_var_2_value',
			),
			// post
			array(
				
			)
		);
	}
	
	public function tearDown()
	{
		$this->ajaxPostRequest = null;
		$this->ajaxGetRequest = null;
		$this->postRequest = null;
		$this->getRequest = null;
		$this->allRequests = array();
	}
	
	public function testIsAjax()
	{
		$this->assertTrue( $this->ajaxPostRequest->isAjax(), "Should be an ajax request.");
		$this->assertTrue( $this->ajaxGetRequest->isAjax(), "Should be an ajax request.");
		$this->assertFalse( $this->postRequest->isAjax(), "Should not be an ajax request.");
		$this->assertFalse( $this->getRequest->isAjax(), "Should not be an ajax request.");
	}
	
	public function testMethod()
	{
		$this->assertEquals('POST', $this->ajaxPostRequest->method());
		$this->assertEquals('GET', $this->ajaxGetRequest->method());
		$this->assertEquals('POST', $this->postRequest->method());
		$this->assertEquals('GET', $this->getRequest->method());
	}
	
	public function testGet()
	{
		$this->assertTrue(is_array($this->ajaxPostRequest->get()));
		$this->assertTrue(is_array($this->ajaxGetRequest->get()));
		$this->assertTrue(is_array($this->postRequest->get()));
		$this->assertTrue(is_array($this->getRequest->get()));
		$this->assertEquals('get_var_1_value',$this->getRequest->get('get_var_1'));
		$this->assertEquals('get_var_2_value',$this->ajaxGetRequest->get('get_var_2'));
	}
	
	public function testGet_badKey()
	{
		try {
			$bad = $this->getRequest->get('spaghetti');
		} catch (RequestException $e) {
			return;
		}
		$this->fail("RequestException expected.");
	}
	
	public function testPost()
	{
		$this->assertTrue(is_array($this->ajaxPostRequest->post()));
		$this->assertTrue(is_array($this->ajaxGetRequest->post()));
		$this->assertTrue(is_array($this->postRequest->post()));
		$this->assertTrue(is_array($this->getRequest->post()));
		$this->assertEquals('post_var_1_value',$this->postRequest->post('post_var_1'));
		$this->assertEquals('post_var_2_value',$this->ajaxPostRequest->post('post_var_2'));
	}
	
	public function testPost_badkey()
	{
		try {
			$bad = $this->postRequest->post('spaghetti');
		} catch (RequestException $e) {
			return;
		}
		$this->fail("RequestException expected. [$bad] returned");
	}
	
	public function testAgent()
	{
		foreach ($this->allRequests as $request) {
			$this->assertEquals('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:11.0) Gecko/20100101 Firefox/11.0', $request->agent());
		}
	}
	
	public function testAddr()
	{
		foreach ($this->allRequests as $request) {
			$this->assertEquals('127.0.0.1', $request->addr());
		}
	}
	
	public function testUri()
	{
		foreach ($this->allRequests as $request) {
			$this->assertEquals('/controller/action/id', $request->uri());
		}
	}
}