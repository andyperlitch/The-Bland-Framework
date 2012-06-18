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
		// set correct headers for error page
		$this->response->setCustomHeaders(
			array(
				'HTTP/1.1 404 Not Found' => false,
				'Content-Type'  => 'text/html; charset=utf-8',
				'Cache-Control' => 'private',
			),
			true
		);
		
		$this->response->body("404 Error: Page '{$this->request->uri()}' not found");
		// TODO: make nice 404 error page
	}
	
}