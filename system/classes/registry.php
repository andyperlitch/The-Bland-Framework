<?php defined('SYSPATH') or die('No direct script access.');

abstract class Registry {
	
	abstract protected function get($key);
	abstract protected function set($key, $value);
	
}