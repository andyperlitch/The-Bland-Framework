<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/sessionregistry.php';
class SessionRegistryTest extends PHPUnit_Framework_TestCase {
	
	private $session;
	
	public function setUp()
	{
		$this->session = new SessionRegistry();
	}
	
	public function tearDown()
	{
		$this->session = null;
	}
	
	public function testUid()
	{
		$uid = $this->session->uid();
		$this->assertRegExp('/[\da-zA-Z]{32}/',$uid);
	}
	
}