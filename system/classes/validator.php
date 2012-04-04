<?php defined('SYSPATH') or die('No direct script access.');

class ValidatorException extends Exception{}

/**
 * Main validation class.
 *
 * @package Validation
 * @author Andrew Perlitch
 */
class Validator{
	
	protected $post;
	protected $data;
	
	function __construct(array $post)
	{
		$this->post = $post;
		
		// trim all values
		foreach ($this->post as $key => &$value) {
			$value = trim($value);
		}
	}
	
	/**
	 * Main validate function.
	 * Returns $this to allow chaining
	 *
	 * @param string $key 
	 * @param array $rules 
	 * @param array $data 
	 * @param array $errors 
	 * @return Model_Validate  ($this)
	 * @author Andrew Perlitch
	 */
	public function v($key, array $rules, array &$data, array &$errors)
	{
		// loop through specified rules
		foreach ($rules as $ruleMethod => $ruleParams) {
			
			// checks if rule method exists
			if( ! method_exists($this, $ruleMethod) ) throw new ValidatorException("Method '$ruleMethod' not found.");
			
			// retrieve filtered value
			$filtered_value = $this->$ruleMethod($key, $ruleParams[0]);
			
			// if returned true, make value checked
			$filtered_value = $filtered_value === true ? $this->post[$key] : $filtered_value;
			
			// if returned false, set error message and end the foreach
			if ( $filtered_value === false ) {
				$errors[$key] = $ruleParams[1];
				break;
			}
			
			// record into data array
			$data[$key] = $filtered_value;
		}
		
		// return the object
		return $this;
	}
	
	
	protected function regex($key, $pattern)
	{
		if( !preg_match($pattern, $this->post[$key], $matches) ) return false;
		return $matches[0];
	}
	
	protected function date($key, $param)
	{
		// pattern for capturing date
		$pattern = '/^([\d]{4})-([\d]{1,2})-([\d]{1,2})$/';
		
		//  capture date parts
		if (! preg_match($pattern,$this->post[$key],$date_elements) ) return false;
		$year  = (int) $date_elements[1];
		$month = (int) $date_elements[2];
		$day   = (int) $date_elements[3];

		// check that date is valid
		if ( ! checkdate($month, $day, $year) ) return false;
		
		switch ($param) {
			case 'future':
				if ( strtotime($this->post[$key]) <= time() ) return false;
			break;
			
			case 'past':
				if (strtotime($this->post[$key]) >= time() ) return false;
			break;
		}
		
		return $this->post[$key];
	}
	
	protected function name($key, $range)
	{
		if ( ! preg_match('/^(\{[\d]*,[\d]+\}|\{[\d]+,[\d]*\}|[\+\*])$/', $range) ) throw new ValidatorException("\$range specified was not valid: '$range'.");
		return $this->regex($key, '/^[^!@#\$%\^&\*\(\)]'.$range.'$/');
	}
	
	protected function int($key, $options)
	{
		return filter_var($this->post[$key], FILTER_VALIDATE_INT, $options);
		
	}
	
	protected function float($key, $options)
	{
		$float = filter_var($this->post[$key], FILTER_VALIDATE_FLOAT);
		if ( $float === false ) return false;
		
		// check for 'greater_than' option
		if ( @array_key_exists('greater_than',$options['options']) ) {
			if ( $float <= $options['options']['greater_than'] ) return false;
		}
		
		// check for 'less_than' option
		if( @array_key_exists('less_than',$options['options']) ) {
			if ( $float >= $options['options']['less_than'] ) return false;
		}
		
		return $float;
	}
	
	protected function email($key, $param)
	{
		// use filter_var method
		return filter_var($this->post[$key], FILTER_VALIDATE_EMAIL);
	}
	
	protected function file($key, $dir)
	{
		if ( ! file_exists($dir.$this->post[$key]) ) return false;
		return $this->post[$key];
	}
	
	protected function notEqual($key, $value)
	{
		if ($this->post[$key] === $value) return false;
		return $this->post[$key];
	}
	
	protected function equals($key, $value)
	{
		if ($this->post[$key] != $value) return false;
		return $this->post[$key];
	}
}

?>