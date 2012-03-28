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
	
	/**
	 * Gets URI.
	 *
	 * @return string
	 * @author Andrew Perlitch
	 */
	public function uri()
	{
		return $this->server['REQUEST_URI'];
	}
	
	/**
	 * Returns true if ajax call, false if not.
	 *
	 * @return bool
	 * @author Andrew Perlitch
	 */
	public function isAjax()
	{
		return array_key_exists('HTTP_X_REQUESTED_WITH', $this->server) && $this->server['HTTP_X_REQUESTED_WITH'] == 'xmlhttprequest';
	}
	
	/**
	 * Returns request method.
	 *
	 * @return string
	 * @author Andrew Perlitch
	 */
	public function method()
	{
		return $this->server['REQUEST_METHOD'];
	}
	
	/**
	 * Returns post array or key in array.
	 *
	 * @param  string $key   
	 * @return mixed
	 * @author Andrew Perlitch
	 */
	public function post($key = NULL)
	{
		if($key === NULL) return $this->post;
		if(array_key_exists($key, $this->post)) return $this->post[$key];
		throw new RequestException("No key found in post array. key: '$key'");
	}
	
	/**
	 * Returns get array or key in array.
	 *
	 * @param string $key 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function get($key = NULL)
	{
		if($key === NULL) return $this->get;
		if(array_key_exists($key, $this->get)) return $this->get[$key];
		throw new RequestException("No key found in get array. key: '$key'");
	}
	
	/**
	 * Returns user agent (browser, compy, etc) string.
	 *
	 * @return string
	 * @author Andrew Perlitch
	 */
	public function agent()
	{
		return $this->server['HTTP_USER_AGENT'];
	}
	
	/**
	 * Returns IP address of user who made request.
	 *
	 * @return string
	 * @author Andrew Perlitch
	 */
	public function addr()
	{
		return $this->server['REMOTE_ADDR'];
	}
}