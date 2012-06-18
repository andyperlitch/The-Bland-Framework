<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Parent class for all model classes.
 *
 * @package Model
 * @author Andrew Perlitch
 */
abstract class Model {
	
	protected $db;
	protected $config;
	
	function __construct(DB $db = NULL, Config $config = NULL)
	{
		$this->db = $db;
		$this->config = $config;
	}
	
}