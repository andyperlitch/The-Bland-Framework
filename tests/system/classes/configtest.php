<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/config.php';
class ConfigTest extends PHPUnit_Framework_TestCase {
	
	private $configs = array();
	
	public function setUp()
	{
		$this->configs[] = new Config('local');
		$this->configs[] = new Config('server_testing');
		$this->configs[] = new Config('production');
	}
	
	public function tearDown()
	{
		
	}
	
	public function testGet()
	{
		foreach ($this->configs as $config) {
			$this->assertTrue( !!preg_match('/mysql:dbname=[\w]+;host=[\w]+/',$config->get('pdo_dsn')) , "array key 'pdo_dsn' not found" );
		}
	}
	
	public function testGet_badKey()
	{
		foreach ($this->configs as $config) {
			try {
				$config->get('Spaghettiosssss');
			} catch (ConfigException $e) {
				return;
			}
			$this->fail("Expecting a ConfigException to be thrown");
		}
	}
	
}