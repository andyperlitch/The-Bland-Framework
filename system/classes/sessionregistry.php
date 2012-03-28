<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Registry for session information.
 *
 * @package default
 * @author Andrew Perlitch
 */
class SessionRegistry extends Registry {
	
	function __construct()
	{
		// Check for command line interface (unit testing)
		if(PHP_SAPI === 'cli') {
			global $_SESSION;
			$_SESSION = array();
		} else {
			
			// Start the session
			session_start();

			// Set cache-control to private
		    header("Cache-control: private");
		}

		// Set the user ID
	    if ( isset($_COOKIE['SESSION']) ) {
	        $_SESSION[__CLASS__]['uid'] = $_COOKIE['SESSION'];
	    } elseif ( ! isset($_SESSION[__CLASS__]['uid']) ) {
	        $_SESSION[__CLASS__]['uid'] = md5(uniqid('biped',true));
	    }
	    if(PHP_SAPI !== 'cli') setcookie('SESSION',$_SESSION[__CLASS__]['uid'],time()+(60*60*24*30));
	}
	
	/**
	 * Returns uid generated in __construct()
	 *
	 * @return string
	 * @author Andrew Perlitch
	 */
	public function uid()
	{
		return $_SESSION[__CLASS__]['uid'];
	}
	
}