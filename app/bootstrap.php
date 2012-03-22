<?php

/**
 * Set the default locale.
 *
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Set environmental variable (to be unset at the end of this bootstrap file)
*/
$SITE_ENV = 'local';

/**
 * Include init file that includes all necessary system files
*/
require SYSPATH.'init.php';


/**
 * Set spl_autoload_register
*/
spl_autoload_register(array('AutoLoader', 'autoload'));

/**
 * Register the request
*/
$request = new 

unset($SITE_ENV);
?>