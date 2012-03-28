<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract class for all registries.
 *
 * @package Registry
 * @author Andrew Perlitch
 */
abstract class Registry {

	/**
	 * Associative array for holding values.
	 *
	 * @var Array
	 */
	protected $properties = array();

	/**
	 * Protected function for retrieving values in $values property.
	 * Used by public methods in subclasses.
	 *
	 * @param string $key Array key to look up 
	 * @return mixed
	 * @author Andrew Perlitch
	 */
	protected function _get($key)
	{
		return $this->properties[$key];
	}
	
	/**
	 * Protected function for setting values in $values property (array)
	 *
	 * @param string $key   Array key to look up
	 * @param mixed $value  Value to set
	 * @return void
	 * @author Andrew Perlitch
	 */
	protected function _set($key, $value)
	{
		$this->properties[$key] = $value;
	}
	
}

/**
 * Exception class for Registry and subclasses.
 *
 * @package Registry
 * @see Registry
 * @author Andrew Perlitch
 */
class RegistryException extends Exception{}
	
/**
 * Contains application-specific config information.
 * Includes environment variables, database connection info, routes, factory methods, etc.
 *
 * @package Registry
 * @author Andrew Perlitch
 */
class ApplicationRegistry extends Registry {
	
	/**
	 * Configuration data for application
	 *
	 * @author Andrew Perlitch
	 */
	protected $config = array();
	
	/**
	 * Holds the routes for app.
	 *
	 * @var Array
	 */
	protected $routes = array();
	
	/**
	 * Constructs application registry object.
	 *
	 * @param string $environment        Used to look up config info
	 * @uses ApplicationRegistry::_getConfigFile
	 * @author Andrew Perlitch
	 */
	function __construct($environment, $path_to_routes)
	{
		// get config associative array
		$configs = $this->_getConfigFile();
		
		// check that environment key exists in config file
		if( ! array_key_exists($environment, $configs) ) throw new RegistryException("Environment [$environment] not found in application.php config file.");
		
		// set config array
		$this->config = $configs[$environment];
		
		// set environment property (in $properties)
		$this->_set('environment',$environment);
		
		// get routes config file
		$this->routes = @include($path_to_routes);
		if( empty($this->routes) ) throw new RegistryException("Routes config file not found");
	}
	
	/**
	 * Retrieve environment variable.
	 * Expecting 'local','server', or 'production'
	 *
	 * @return string  Value of environment key
	 * @author Andrew Perlitch
	 */
	public function env()
	{
		return $this->_get('environment');
	}
	
	
	/**
	 * Factory method for creating new pdo object
	 *
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function pdo()
	{
		return new PDO( $this->config['pdo']['dsn'] , $this->config['pdo']['username'] , $this->config['pdo']['password'], $this->config['pdo']['options']);
	}
	
	public function routes()
	{
		return $this->routes;
	}
	
	/**
	 * Retrieves config file with array of all config sets.
	 *
	 * @return Array
	 * @author Andrew Perlitch
	 */
	protected function _getConfigFile()
	{
		return include(APPPATH.'config/application.php');
	}
	
}