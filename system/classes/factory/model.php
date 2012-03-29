<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Factory for creating model objects
 *
 * @package Factory
 * @author Andrew Perlitch
 */
class Factory_Model{
	
	public function build($className)
	{
		return new $className();
	}
	
}