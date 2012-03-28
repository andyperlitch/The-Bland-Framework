<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/applicationregistry.php';
class ApplicationRegistryTest extends PHPUnit_Framework_TestCase {
	
	private $registries = array();
	private $routes;
	
	public function setUp()
	{
		$this->registries['local'] = new ApplicationRegistry('local', APPPATH.'config/routes.php');
		$this->registries['server_testing'] = new ApplicationRegistry('server_testing', APPPATH.'config/routes.php');
		$this->registries['production'] = new ApplicationRegistry('production', APPPATH.'config/routes.php');
		$this->routes = include(APPPATH.'config/routes.php');
	}
	
	public function tearDown()
	{
		$this->registries = array();
		$this->routes = null;
	}
	
	public function testConstruct_invalidEnv()
	{
		try {
			$this->registries['spaghettimonster'] = new ApplicationRegistry('spaghettimonster', APPPATH.'config/routes.php');
		} catch (RegistryException $e) {
			return;
		}
		$this->fail("invalid environment exception expected");
	}
	
	public function testConstruct_invalidRoutes()
	{
		try {
			$this->registries['bad_route'] = new ApplicationRegistry('local', APPPATH.'config/spaghettiosssss.php');
		} catch (RegistryException $e) {
			return;
		}
		$this->fail("invalid routes exception expected");
	}
	
	public function testEnv()
	{
		foreach ($this->registries as $key => $registry) {
			$this->assertEquals($registry->env(), $key);
		}
	}
	
	public function testPdo()
	{
		$pdo = $this->registries['local']->pdo();
		$this->assertInstanceOf('PDO',$pdo);
	}
	
	public function testRoutes()
	{
		foreach ($this->registries as $registry) {
			$this->assertEquals( $this->routes, $registry->routes() );
		}
	}
	
} 