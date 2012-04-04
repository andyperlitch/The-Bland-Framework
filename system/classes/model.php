<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Parent class for all model classes.
 *
 * @package Model
 * @author Andrew Perlitch
 */
abstract class Model {
	
	protected $db;
	
	function __construct($db = NULL)
	{
		$this->db = $db;
	}
	
}