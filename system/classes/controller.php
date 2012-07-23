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
	 * @var Response
	 */
	protected $response;
	
	/**
	 * Factory_Model object.
	 * Used for making model objects
	 *
	 * @var Factory_Model
	 */
	protected $fm;
	
	/**
	 * Factory_View object.
	 * Used for processing view files into string.
	 * Also for adding variables to views.
	 *
	 * @var Factory_View
	 */
	protected $fv;
	
	/**
	 * Action to execute 
	 *
	 * @var String
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
	function __construct(Config $c, Request $r, Session $s, Response $res, Factory_Model $fm, Factory_View $fv, $action )
	{
		$this->config = $c;
		$this->request = $r;
		$this->session = $s;
		$this->response = $res;
		$this->fm = $fm;
		$this->fv = $fv;
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
	
	public function redirectUser()
	{
		// vars
		$location;
		
		// get roles
		$roles = $this->session["user"]["roles"];
		
		// wrap in try-catch for session error when accessing "user" key
		if ($roles) {

			// determine best home page
			if ( in_array("admin",$roles) ) {
				$location = '/admin';
			} else {
				$location = '/userhome';
			}
			
		} else {
			// go to login page
			$location = '/login';
		}
		
		// redirect to new location
		$this->request->redirect( $location );
	}
}