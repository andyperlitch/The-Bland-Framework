<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/commandresolver.php';
class CommandResolverTest extends PHPUnit_Framework_TestCase {
	
	private $cmd_r;
	private $controller;
	
	public function setUp()
	{
		$this->cmd_r = new CommandResolver(
			
			// Routes
			array(
				array(
					'pattern' => '/^(?:\/custom\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
					'keys'    => array('controller','action','id'),
					'defaults' => array('controller' => 'controller_home', 'action' => 'index'),
				),
				array(
					'pattern' => '/^(?:\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
					'keys'    => array('controller','action','id'),
					'defaults' => array('controller' => 'test_test', 'action' => 'test_action'),
				)
			),
			// uri
			'/test_test/test_action/id'
		);
	}
	
	public function tearDown()
	{
		$this->cmd_r = null;
		$this->controller = null;
	}
	public function testConstruct_NoURI()
	{
		try {
			$cmd_r = new CommandResolver(

				// Routes
				array(
					array(
						'pattern' => '/ ^(?:\/)   ([-\w]*)   (?: (?:\/) ([-\w]+) )?   (?: (?:\/) ([-\w]+) )?  /x',
						'keys'    => array('controller','action','id'),
						'defaults' => array('controller' => 'test_test', 'action' => 'test_action'),
					)
				),
				// uri
				'/'
			);
		} catch (CommandResolverException $e) {
			$this->fail('Empty URI should have passed. Message:'.$e->getMessage());
		}
		return;
	}
	public function testConstruct_NoAction()
	{
		try {
			$cmd_r = new CommandResolver(

				// Routes
				array(
					array(
						'pattern' => '/^(?:\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
						'keys'    => array('controller','action','id'),
						'defaults' => array('controller' => 'test_test', 'action' => 'test_action'),
					)
				),
				// uri
				'/test_test/'
			);
		} catch (CommandResolverException $e) {
			$this->fail('Empty action should have passed. Message:'.$e->getMessage());
		}
		return;
	}
	public function testConstruct_BadRouteController()
	{
		try {
			$bad = new CommandResolver(

				// Routes
				array(
					array(
						'pattern' => '/^(?:\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
						'keys'    => array('blah','action','id'),
						'defaults' => array('blah' => 'controller_home', 'action' => 'index'),
					)
				),
				// uri
				'/controller/action/id'
			);
		} catch (CommandResolverException $e) {
			return;
		}
		$this->fail("Expecting CommandResolverException from no controller present in defaults or keys.");
		
	}
	
	public function testConstruct_BadRouteAction()
	{
		try {
			$bad = new CommandResolver(

				// Routes
				array(
					array(
						'pattern' => '/^(?:\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
						'keys'    => array('controller','accion','id'),
						'defaults' => array('controller' => 'controller_home', 'accion' => 'index'),
					)
				),
				// uri
				'/controller/action/id'
			);
		} catch (CommandResolverException $e) {
			return;
		}
		$this->fail("Expecting CommandResolverException from no action present in defaults or keys.");
		
	}
	
	public function testConstruct_BadUri()
	{
		try {
			$bad = new CommandResolver(

				// Routes
				array(
					array(
						'pattern' => '/^(?:\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
						'keys'    => array('controller','action','id'),
						'defaults' => array('controller' => 'controller_home', 'action' => 'index'),
					)
				),
				// uri
				array('/controller/action/id')
			);
		} catch (CommandResolverException $e) {
			return;
		}
		$this->fail("Expecting CommandResolverException from incorrect var type for \$uri.");
		
	}
	
	public function testConstruct_CustomRoute()
	{
		$cmd_r = new CommandResolver(

			// Routes
			array(
				array(
					'pattern' => '/^(?:\/custom\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
					'keys'    => array('controller','action','id'),
					'defaults' => array('controller' => 'controller_home', 'action' => 'index'),
				),
				array(
					'pattern' => '/^(?:\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
					'keys'    => array('controller','action','id'),
					'defaults' => array('controller' => 'controller_home', 'action' => 'index'),
				)
			),
			// uri
			'/custom/test_test/test_action'
		);
		$params = $cmd_r->getParams();
		$controller = new $params['controller'](
			$this->getMockBuilder("ApplicationRegistry")->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder("RequestRegistry")->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder("SessionRegistry")->disableOriginalConstructor()->getMock()
		);
		$this->assertInstanceOf('Controller_Test_Test',$controller);
	}
	
	public function testGetParams()
	{
		$params = $this->cmd_r->getParams();
		$controller = new $params['controller'](
			$this->getMockBuilder("ApplicationRegistry")->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder("RequestRegistry")->disableOriginalConstructor()->getMock(),
			$this->getMockBuilder("SessionRegistry")->disableOriginalConstructor()->getMock()
		);
		$this->assertInstanceOf('Controller', $controller);
	}
	
}