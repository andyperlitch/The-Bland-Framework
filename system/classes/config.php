<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Exception class for Config.
 *
 * @package Exception
 * @author Andrew Perlitch
 */
class ConfigException extends Exception{}

/**
 * Class that holds config info.
 * Besides PDO connection info, contains flags for testing mode, live, eCommerce-enabled, etc.
 * no setters, only getters... basically this is a glorified associative array
 *
 * @package Config
 * @author Andrew Perlitch
 */
class Config {
	
	/**
	 * Array that holds config info.
	 *
	 * @var array
	 */
	protected $properties = array();
	
	/**
	 * Reads app config file(s), sets appropriate config array to $this->properties
	 *
	 * @author Andrew Perlitch
	 */
	function __construct($environment)
	{
		$configs = include(APPPATH.'config/application.php');
		$this->properties = $configs[$environment];
	}
	
	/**
	 * Retrieves 
	 *
	 * @param string $key 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function get($key)
	{
		if( ! array_key_exists($key, $this->properties) ) throw new ConfigException("Config key:'$key' not found in config data.");
		return $this->properties[$key];
	}
}