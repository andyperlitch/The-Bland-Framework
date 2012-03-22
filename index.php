<?php

/**
 * The directory in which application specific resources are located.
 * The application directory must contain the bootstrap.php file.
 */
$application = 'app';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/controller.php and classes/model.php files.
 */
$system = 'system';

/**
 * Set the full path to the docroot
 */
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

/**
 * Make the application relative to the docroot, for symlink'd index.php
 */
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

/**	
 * Make the system relative to the docroot, for symlink'd index.php
 */
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;
	
/**
 * Define the absolute path for app directory
 */
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);
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

// Bootstrap the application
require APPPATH.'bootstrap.php';

