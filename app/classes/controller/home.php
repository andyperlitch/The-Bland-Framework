<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Default controller for requests
 *
 * @package Controller
 * @author Andrew Perlitch
 */
class Controller_Home extends Controller_Templates_Html5 {

	public function action_index()
	{
		$this->template['body'] = 'hello world!';
	}
	
}