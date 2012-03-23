<?php
/**
 * Index file for The Bland Framework.
 * 
 */

// Bootstrap the application
include 'app/bootstrap.php';


// Include init file that includes all necessary system files
require SYSPATH.'init.php';

/**
 * Set environment variable.
 * This must be a key in app config.
 * 
 * @see application.php
 */
$environment = 'local';

/**
 * Set up registry objects
 *
 */
try {
	$app_registry = new ApplicationRegistry( $environment );
} catch (RegistryException $e) {
	
	// TODO: create nice-looking server error page for this
	die("Sorry, an error occurred");
	Error::log("\$environment variable [$environment] not a valid key in /app/config/application.php file.",'index.php',__LINE__,'3');
}



/**
 * Set spl_autoload_register
*/
spl_autoload_register(array('AutoLoader', 'autoload'));



unset($environment);

// Request object
$request = new Request($_SERVER);

// 
echo APPPATH;