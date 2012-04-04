<?php defined('SYSPATH') or die('No direct script access.');

class DB extends PDO{
	
	protected $config;
	
	function __construct(Config $c)
	{
		// store config file
		$this->config = $c;
		
		// do PDO constructor
		parent::__construct(
			$this->config['pdo_dsn'],
			$this->config['pdo_user'],
			$this->config['pdo_pass']
		);
	}
	
	public function ins()
	{
		
	}
	
	public function upd()
	{
		
	}
	
	public function del()
	{
		
	}
}