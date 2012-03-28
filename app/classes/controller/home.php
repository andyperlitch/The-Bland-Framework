<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Default controller for requests
 *
 * @package Controller
 * @author Andrew Perlitch
 */
class Controller_Home extends Controller {

	public function action_index()
	{
		echo 'Hello World!';
	}
	
	public function action_test()
	{
		echo 'testing another action';
	}
	
}