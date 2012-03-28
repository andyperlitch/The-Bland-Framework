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
		echo '404 Error: Page not found';
		// TODO: make nice 404 error page
	}
	
}