<?php defined('APPPATH') or die('No direct script access.');

/**
 * Exception class for controllers.
 *
 * @package Controller
 * @author Andrew Perlitch
 */
class ControllerException extends Exception{}

/**
 * Interface for controller classes.
 *
 * @package Controller
 * @author Andrew Perlitch
 */
abstract class Controller{
	
	/**
	 * Config object
	 *
	 * @var Config
	 */
	protected $config;
	
	/**
	 * Request object
	 *
	 * @var Request
	 */
	protected $request;
	
	/**
	 * Session object
	 *
	 * @var Session
	 */
	protected $session;
	
	/**
	 * Response object
	 *
	 * @var string
	 */
	protected $response;
	
	/**
	 * Action to execute 
	 *
	 * @var Session
	 */
	protected $action;
	
	
	
	/**
	 * Constructor.
	 *
	 * @param Config $c 
	 * @param Request $r 
	 * @param Session $s 
	 * @param string $action 
	 * @author Andrew Perlitch
	 */
	function __construct(Config $c, Request $r, Session $s, Response $res, Factory_Model $fm, $action )
	{
		$this->config = $c;
		$this->request = $r;
		$this->session = $s;
		$this->response = $res;
		$this->action = $action; // string of name of function to execute
	}
	
	/**
	 * function to execute before chosen action of request
	 *
	 * @return void
	 * @author Andrew Perlitch
	 */
	protected function before() 
	{
		
	}
	
	/**
	 * function to execute after chosen action of request
	 *
	 * @return void
	 * @author Andrew Perlitch
	 */
	protected function after()
	{
		
	}
	
	public function execute()
	{
		$this->before();
		$this->{$this->action}();
		$this->after();
		$this->response->send();
	}
}