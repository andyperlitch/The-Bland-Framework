<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Default controller for requests
 *
 * @package Controller
 * @author Andrew Perlitch
 */
class Controller_Error extends Controller {

	public function action_404()
	{
		$this->response->body("404 Error: Page '{$this->request->uri()}' not found");
		// TODO: make nice 404 error page
	}
	
}