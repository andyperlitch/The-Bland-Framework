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
	 * Private function for retrieving values in $values property.
	 * Used by public methods in subclasses.
	 *
	 * @param string $key Array key to look up 
	 * @return mixed
	 * @author Andrew Perlitch
	 */
	abstract protected function _get($key);
	
	/**
	 * Private function for setting values in $values property (array)
	 *
	 * @param string $key   Array key to look up
	 * @param mixed $value  Value to set
	 * @return void
	 * @author Andrew Perlitch
	 */
	abstract protected function _set($key, $value);
	
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
 * Contains application-specific information.
 * Includes environment variables, database connection info, factory methods, etc.
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
	 * Constructs application registry object.
	 *
	 * @param string $environment   Used to look up config info
	 * @author Andrew Perlitch
	 */
	function __construct($environment)
	{
		// get config associative array
		$configs = $this->_getConfigFile();
		
		// check that environment key exists in config file
		if( ! array_key_exists($environment, $configs) ) throw new RegistryException("Environment [$environment] not found in application.php config file.");
		
		// set config array
		$this->config = $configs[$environment];
		
		// set environment property (in $values)
		$this->_set('environment',$environment);
	}
	
	protected function _getConfigFile()
	{
		return include(APPPATH.'config/application.php');
	}
	
	protected function _get($key)
	{
		return $this->properties[$key];
	}
	
	protected function _set($key, $value)
	{
		$this->properties[$key] = $value;
	}
	
	/**
	 * Retrieve environment variable.
	 * Expecting 'local','server', or 'production'
	 *
	 * @return string  Value of environment key
	 * @author Andrew Perlitch
	 */
	function getEnv()
	{
		return $this->_get('environment');
	}
	
}