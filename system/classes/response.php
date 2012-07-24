<?php defined('SYSPATH') or die('No direct script access.');

class ResponseException extends Exception{}

class Response{
	
	/**
	 * Headers to be sent
	 *
	 * @var string
	 */
	protected $headers = array();
	
	/**
	 * Body of response, as string.
	 *
	 * @var mixed
	 */
	protected $body;
	
	function __construct()
	{
		$this->headers = array(
			'HTTP/1.1 200 OK' => false,
			'Content-Type'  => 'text/html; charset=utf-8',
			'Cache-Control' => 'private',
			'Host'          => 'www.example.com',
			
		);
	}
	
	/**
	 * Adds to $this->body, which will be echoed in $this->send()
	 *
	 * @param string $content 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function body($content,$equal=false)
	{
		if ( $equal ) $this->body = $content;
		else $this->body .= $content;
	}

	/**
	 * Prepends to $this->body, which will be echoed in $this->send()
	 *
	 * @param string $content 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function bodyPrepend($content)
	{
		$this->body = $content . $this->body;
	}
	
	/**
	 * Clears $this->body of string.
	 *
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function bodyClear()
	{
		$this->body = '';
	}
	
	/**
	 * Sends response.
	 * Also sends headers if not already sent.
	 *
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function send()
	{
		// check if body is an array
		if ( is_array($this->body) ) {
			// add json header
			$this->headers['Content-Type'] = 'application/json; charset=utf-8';
			
			// check if headers already sent
			if (! headers_sent() ) $this->sendHeaders();
			
			// encode for utf8
			$this->utf_prepare($this->body);
			
			// echo the json-encoded array
			echo json_encode( $this->body );
			
		} else {
			// check if headers already sent
			if (! headers_sent() ) $this->sendHeaders();
			
			// echo body of response
			echo $this->body;
		}
	}
	
	private function utf_prepare(&$array)
	{
	    foreach($array AS $key => &$value)
	    {
	        if (is_array($value))
	        {
	            $this->utf_prepare($value);
	        } else
	        {
	            $value = utf8_encode($value);
	        }
	    }
	}
	
	/**
	 * Sets $this->headers to custom headers.
	 *
	 * @param array $headers 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function setCustomHeaders(array $headers, $remove_defaults = false)
	{
		if ($remove_defaults === true) $this->headers = array(); 
		// Useful headers:
		//  
		
		$this->headers = array_merge($this->headers, $headers);
	}
	
	public function sendHeaders($headers = array(), $remove_defaults = false)
	{
		// check for custom headers
		if ( ! empty($headers) ) $this->setCustomHeaders($headers, $remove_defaults);
		
		// loop through headers, send out
		foreach ($this->headers as $field => $value) {
			$header = $field;
			if ($value) $header .= ": ".$value;
			header($header);
		}
	}
	
}