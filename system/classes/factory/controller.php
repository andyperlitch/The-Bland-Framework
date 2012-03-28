<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Builds controller object for given server and request info.
 *
 * @package Controller
 * @author Andrew Perlitch
 */
class Factory_Controller{
	
	/**
	 * Returns controller object.
	 * Request object requires $_SERVER, $_GET, and $_POST arrays.
	 *
	 * @param array $server 
	 * @param array $get 
	 * @param array $post 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function build(array $server, array $get, array $post, $environment)
	{
		$c = new Config($environment);
		$r = new Request($server, $get, $post);
		$s = new Session();
		
		// Reads config file with route info, compares to $r->getUri to find name of controller and action within controller.
		$name = 'Controller_Example';
		$action = 'action_showComments';
		
		// Returns correct child object of Controller.
		return new $name($c, $r, $s, $action);
	}
}