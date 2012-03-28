<?php defined('APPPATH') or die('No direct script access.');
abstract class Controller {
	
	protected $app;
	protected $request;
	protected $session;
	
	function __construct(ApplicationRegistry $app, RequestRegistry $req, SessionRegistry $ses)
	{
		$this->app = $app;
		$this->request = $req;
		$this->session = $ses;
	}
	
	public function before()
	{
		
	}
	
	public function after()
	{
		
	}
	
	final public function execute()
	{
		// grab action name
		$action = $this->request->param('action');
		
		// check if chosen action exists
		if ( ! method_exists($this, $action) ) {
			// TODO: Write 404 error page
			echo '404 Error Page';
			exit();
		}
		
		// do stuff
		$this->before();
		$this->$action();
		$this->after();
	}
	
}