<?php
/**
 * The directory in which application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 */
$application = 'app';

/**
 * The directory in which the system resources are located. 
 * The system directory must contain the classes/controller.php 
 * and classes/model.php files.
 */
$system = 'system';

/**
 * The directory in which the module resources are located. 
 * Each module directory must have folder structure similar to app.
 * and classes/model.php files.
 */
$modules = 'modules';

/**
 * Set the full path to the docroot
 */
define('DOCROOT', rtrim(realpath(dirname(__FILE__)), $application));

/**
 * Make the application relative to the docroot, for symlink'd index.php
 */
if ( is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

/**	
 * Make the system relative to the docroot, for symlink'd index.php
 */
if ( is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;
	
/**	
 * Make the system relative to the docroot, for symlink'd index.php
 */
if ( is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;
	
/**
 * Define the absolute path for app directory
 */
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);

/**
 * Define absolute url for links etc.
 */
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
$servername = ( isset($_SERVER['HTTP_HOST']) ) ? $_SERVER['HTTP_HOST'] : 'localhost:8888';
define('ABSPATH', $protocol . $servername);
unset($protocol);
unset($servername);

/**
 * Clean up the configuration vars
 */
unset($application);
unset($system);

/**
 * Define the start time of the application, used for profiling.
 */
if ( ! defined('APP_START_TIME'))
{
	define('APP_START_TIME', microtime(TRUE));
}

/**
 * Define the memory usage at the start of the application, used for profiling.
 */
if ( ! defined('APP_START_MEMORY'))
{
	define('APP_START_MEMORY', memory_get_usage());
}
/**
 * Set the default locale.
 *
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Include autoloader
*/
require SYSPATH.'classes/autoloader.php';

?>