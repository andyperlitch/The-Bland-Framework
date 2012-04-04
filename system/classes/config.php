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
class Config implements arrayaccess{
	
	/**
	 * Array that holds config info.
	 *
	 * @var array
	 */
	private $container = array();
	
	/**
	 * Reads app config file(s), sets appropriate config array to $this->properties
	 *
	 * @author Andrew Perlitch
	 */
	function __construct($environment)
	{
		$configs = include(APPPATH.'config/application.php');
		$this->container = $configs[$environment];
	}
	
	public function offsetSet($offset, $value) {
        throw new ConfigException("Setting config data not allowed.");
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        if ( isset($this->container[$offset]) ) {
			return $this->container[$offset];
		}
		throw new ConfigException("Config key:'$offset' not found in config data.");
    }

}