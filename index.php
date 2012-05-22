<?php

// Bootstrap the application
require 'app/bootstrap.php';

// Set environment
$environment = 'local';

// Set autoloader
spl_autoload_register(array('AutoLoader', 'autoload'));

// Get factory for creating controller
$factory = new Factory_Controller();

// Build controller
$controller = $factory->build($_SERVER, $_GET, $_POST, $_FILES, $environment);

// Clean up $environment global
unset($environment);

// Execute controller
$controller->execute();