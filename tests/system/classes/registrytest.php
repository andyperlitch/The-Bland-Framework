<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include '/Users/andyperlitch/Sites/BlandFramework/The-Bland-Framework/system/classes/registry.php';
class ApplicationRegistryTest extends PHPUnit_Framework_TestCase {
	
	private $registries = array();
	
	public function setUp()
	{
		$this->registries['local'] = new ApplicationRegistry('local');
		$this->registries['server_testing'] = new ApplicationRegistry('server_testing');
		$this->registries['production'] = new ApplicationRegistry('production');
	}
	
	public function tearDown()
	{
		
	}
	
	public function testGetEnv()
	{
		foreach ($this->registries as $key => $registry) {
			$this->assertEquals($registry->getEnv(), $key);
		}
	}
	
	public function testGetEnv_invalidEnv()
	{
		try {
			$this->registries['spaghettimonster'] = new ApplicationRegistry('spaghettimonster');
		} catch (RegistryException $e) {
			return;
		}
		$this->fail("invalid environment exception expected");
	}
} 