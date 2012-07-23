<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/session.php';
class SessionTest extends PHPUnit_Framework_TestCase {
	
	private $session;
	
	public function setUp()
	{
		$this->session = new Session();
		$this->session['spaghetti'] = 'monster';
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
	
	public function testSetAndExists()
	{
		$this->session['foo'] = 'bar';
		$this->assertTrue(isset($this->session['foo']) , 'key "foo" should be set, offsetSet or offsetExists failed');
	}
	
	public function testGet()
	{
		$equal = $this->session['spaghetti'] === 'monster';
		$this->assertTrue( $equal , 'offsetGet failed for session arrayaccess class. ');
	}
	
	public function testUnset()
	{
		unset($this->session['foo']);
		$equal = $this->session['foo'] === 'bar';
		$this->assertFalse( $equal , 'offsetGet failed for session arrayaccess class. ');
	}
	
	public function testBadGet()
	{
		$test = $this->session["not_a_key"] === NULL;
		$this->assertTrue( $test , 'Getting an unset array key should return NULL. Actual value: "'.$test.'"');
	}
	
	public function testUnsetSession()
	{
		$this->session->unsetSession();
		$this->assertTrue( !array_key_exists("spaghetti",$this->session) , 'Array key "spaghetti" should not exist');
	}
}