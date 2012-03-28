<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Config file for routes
 *
 * @author Andrew Perlitch
 */

return array(
	/**
	 * Request URI matched to these via regexp. 
	 * First match is used.
	 *
	 * @author Andrew Perlitch
	 */
	
	// default
	array(
		'pattern' => '/^(?:\/)([-\w]*)(?:(?:\/)([-\w]+))?(?:(?:\/)([-\w]+))?/',
		'keys'    => array('controller','action','id'),
		'defaults' => array('controller' => 'controller_home', 'action' => 'index'),
	)
);