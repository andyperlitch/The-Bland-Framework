<?php defined('SYSPATH') or die('No direct script access.');

abstract class Factory {
	
	protected function _getClassName($subject,$prefix = 'Controller_')
	{
		return $prefix.preg_replace_callback(
		    '/(_([a-z]{1}))/',
		    function ($matches) {
		        return strtoupper($matches[0]);
		    } ,
		    $subject
		);
	}
	
}