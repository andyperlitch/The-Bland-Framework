<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Factory for creating model objects
 *
 * @package Factory
 * @author Andrew Perlitch
 */
class Factory_Model extends Factory{
	
	protected $config;
	
	function __construct(Config $c)
	{
		$this->config = $c;
	}
	
	public function build( $className, $args = array(), $database = true, $config = true )
	{
		// compute class name
		$className = $this->_getClassName($className, 'Model_');
		
		// set db object (or lack thereof)
		if($database) $args[] = new DB($this->config);
		
		// set config 
		if($config) $args[] = $this->config;
		
		// Multiple parameters
		$object = new ReflectionClass($className);
		return $object->newInstanceArgs($args);
	}
}