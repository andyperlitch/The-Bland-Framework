<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Exception class for Request.
 *
 * @package Request
 * @author Andrew Perlitch
 */
class RequestException extends Exception{}

/**
 * Request object that holds info on specifc request.
 *
 * @package Request
 * @author Andrew Perlitch
 */
class Request {
	
	protected $server;
	protected $get;
	protected $post;
	
	/**
	 * Asks for server, get, and post arrays.
	 * Does not use globals to allow for ease
	 * of testing.
	 *
	 * @param array $server 
	 * @param array $get 
	 * @param array $post 
	 * @author Andrew Perlitch
	 */
	function __construct(array $server, array $get, array $post)
	{
		$this->server = $server;
		$this->get = $get;
		$this->post = $post;
	}
	
	public function getUri()
	{
		return $this->server['REQUEST_URI'];
	}
	
	public function isAjax()
	{
		return array_key_exists('')
	}
	
	// other methods in here like getUri, isAjax, getRequestMethod, getPost, getGet, getAgent, getRemoteAddr
}