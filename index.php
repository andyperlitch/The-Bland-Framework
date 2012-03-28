<?php

// TESTING NEW BRANCH

/**
 * Index file for The Bland Framework.
 * 
 */

// Bootstrap the application
require 'app/bootstrap.php';

/**
 * Set spl_autoload_register
*/
spl_autoload_register(array('AutoLoader', 'autoload'));

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
	// App object
	$appRegistry = new ApplicationRegistry( $environment , APPPATH.'config/routes.php');
	// Request object
	$reqRegistry = new RequestRegistry($_SERVER, $_GET, $_POST);
	// Session object
	$sesRegistry = new SessionRegistry();
	
} catch (RegistryException $e) {
	
	// TODO: create nice-looking server error page for this
	Error::log("\$environment variable [$environment] not a valid key in /app/config/application.php file.",'index.php',__LINE__,'3');
	die("Sorry, an error occurred");

}

// Clean up global
unset($environment);

try {
	
	// Create new command resolver
	$cmd_r = new CommandResolver($appRegistry->routes(), $reqRegistry->uri());
	
	// Add uri params to appregistry
	$reqRegistry->acceptParams($cmd_r->getParams());
	
	// Create controller
	$cName = $reqRegistry->param('controller');
	$controller = new $cName($appRegistry, $reqRegistry, $sesRegistry);
	
	// execute controller
	$controller->execute();
	
} catch (Exception $e) {
	// 404 Error
	// TODO: Make nice 404 Error page
	// TODO: Log bad request
	echo '404 Error from index';
}