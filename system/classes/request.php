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
	protected $files;
	protected $ajax = null;
	protected $requestParams = array();
	
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
	function __construct(array $server, array $get, array $post, array $files = null)
	{
		$this->server = $server;
		$this->get = $get;
		$this->post = $post;
		$this->files = $files;
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
		if ( $this->ajax === null ) {
			$this->ajax = array_key_exists('HTTP_X_REQUESTED_WITH', $this->server) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
		}
		return $this->ajax;
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
	
	public function isPost()
	{
		return $this->method() == 'POST';
	}
	
	/**
	 * Returns post array or key in array.
	 *
	 * @param  string $key   
	 * @return mixed
	 * @author Andrew Perlitch
	 */
	public function post($key = NULL, $emptyReturnsNull = false)
	{
		if($key === NULL) return $this->post;
		if(array_key_exists($key, $this->post)) return $this->post[$key];
		if ($emptyReturnsNull) return null;
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
	 * Returns key from $_FILES or $_FILES itself
	 *
	 * @param string $key 
	 * @return mixed
	 * @author Andrew Perlitch
	 */
	public function files($key = NULL)
	{
		if($key === NULL) return $this->files;
		if(array_key_exists($key, $this->files)) return $this->files[$key];
		throw new RequestException("No key found in files array. key: '$key'");
	}
	
	/**
	 * Returns key from $_SERVER or $_SERVER itself
	 *
	 * @param string $key 
	 * @return mixed
	 * @author Andrew Perlitch
	 */
	public function server($key = NULL)
	{
		if($key === NULL) return $this->server;
		if(array_key_exists($key, $this->server)) return $this->server[$key];
		throw new RequestException("No key found in server array. key: '$key'");
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
	
	public function setParams(array $params)
	{
		$this->requestParams = $params;
	}
	
	public function param($key=NULL)
	{
		if ( $key === NULL ) return $this->requestParams;
		else return $this->requestParams[$key];
	}
	
	public function isHttps()
	{
		return array_key_exists('HTTPS', $this->server ) AND $this->server['HTTPS'] === "on";
	}
	
	/**
	 * Redirects to same or specified url, with or without https.
	 * If $https is NULL, will redirect to current protocol.
	 * If $https is false, will redirect to http://
	 * If $https is true, will redirect to https://
	 *
	 * @param  String $uri              URI to redirect to. If null, will redirect to current url 
	 * @param  Mixed/Bool/NULL $https   Variable that says whether to go to http, https, or current 
	 * @param  Bool $regardless         Redirect regardless 
	 * @return void                
	 * @author Andrew Perlitch
	 */
	public function redirect($uri = NULL, $https = NULL, $regardless = false){
		// a uri same as $this->uri is equivalent to $uri being null
		$uri = ( $uri == $this->uri() ) ? NULL : $uri;
		
		// set uri
		$redirect_uri = ($uri == NULL) ? $this->uri() : $uri ;
		
		// check whether asking for https
		if ( $https === true ) {
			
			// if not https, definitely redirect
			if ( ! $this->isHttps() ) {
				$location = "https://".$this->server['HTTP_HOST'] . $uri;
				header("Location: $location");
				exit();
			}
			// if IS https, check if uri is null or if regardless was true
			elseif ( $uri !== NULL OR $regardless ) {
				$location = $redirect_uri;
				header("Location: $location");
				exit();
			}

		} elseif ( $https === false ) {
			
			// if not http, definitely redirect
			if ( $this->isHttps() ) {
				$location = "http://".$this->server['HTTP_HOST'] . $uri;
				header("Location: $location");
				exit();
			}
			// if IS http, check if uri is null or if regardless was true
			elseif ( $uri !== NULL OR $regardless ) {
				$location = $redirect_uri;
				header("Location: $location");
				exit();
			}
			
		} elseif ( $uri !== NULL OR $regardless ) {
				$location = $redirect_uri;
				header("Location: $location");
				exit();
		}
	}			
}