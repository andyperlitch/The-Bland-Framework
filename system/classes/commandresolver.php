<?php defined('SYSPATH') or die('No direct script access.');

class CommandResolverException extends Exception{}

/**
 * Uses routes and URI to determine request parameters and create controller.
 *
 * @package default
 * @author Andrew Perlitch
 */
class CommandResolver {
	
	/**
	 * Command params extracted from uri with routes.
	 *
	 * @var string
	 */
	protected $params;
	
	/**
	 * Interprets URI from $req and routes from $app to create correct Controller obj.
	 * Loops through routes until it finds a match for given uri.
	 *
	 * @param RequestRegistry $req 
	 * @param SessionRegistry $ses 
	 * @param ApplicationRegistry $app 
	 * @author Andrew Perlitch
	 */
	function __construct( array $routes, $uri)
	{
		// Check that uri is a string
		if ( ! is_string($uri) ) throw new CommandResolverException("Expecting a string for \$uri. Given type: ".gettype($uri));
		
		// Get command params
		$this->params = $this->_setParams($routes, $uri);
		
	}
	
	/**
	 * Returns controller object.
	 * Based on URI and routes given in self::__construct
	 * 
	 * @return Controller
	 * @author Andrew Perlitch
	 */
	public function getParams()
	{
		return $this->params;
	}
	
	
	/**
	 * Returns formatted controller name.
	 * Prepends 'Controller_' and capitalizes letters after underscores.
	 *
	 * @param string $controller 
	 * @return string
	 * @author Andrew Perlitch
	 */
	protected function _getControllerClassName($controller)
	{
		// Add 'Controller_' prefix, make uppercase letters
		return 'Controller_'.ucfirst( preg_replace( '/(_([a-z]{1}))/e' , "strtoupper('\\1')" , $controller  ) );
	}
	
	/**
	 * Determines request params using routes and uri.
	 *
	 * @param array $routes    Routes to choose from
	 * @param string $uri      The URI of the request
	 * @return array
	 * @author Andrew Perlitch
	 */
	protected function _setParams($routes,$uri)
	{
		// Keep string for failure reasons
		$reasons = '';
		
		// Begin loop through $routes to determine first match
		foreach ($routes as $route) {
						
			// Create the regex params array
			if( ! preg_match( $route['pattern'], $uri, $matches) ) {
				$reasons .= "no match";
				continue;
			}
			
			// Take off first capture
			array_splice(&$matches,0,1);
			
			// Check that matches count is the same as keys
			if (count($route['keys']) < count($matches) ) {
				$reasons .= "not enough keys provided for matches, ";
				continue;
			}
			
			// eliminate any empty values
			$matches = array_filter($matches);

			// reduce keys to count of $matches
			$keys = $route['keys'];
			$count = count($matches);
			array_splice(&$keys, $count );
						
			// Combine keys to matches
			$combined = $count > 0 ? array_combine($keys,$matches) : array();
			
			// Merge defaults with $combined
			$merged = array_merge($route['defaults'], $combined);
			
			// Check that controller and action are specified in resulting array
			if ( (! array_key_exists('controller', $merged)) || (! array_key_exists('action',$merged)) ) {
				$reasons .= "controller and/or action not found in final params array, ";
				continue;
			}
			
			// All tests pass, change controller and action, then return $merged
			$merged['controller'] = $this->_getControllerClassName($merged['controller']);
			$merged['action'] = 'action_'.strtolower($merged['action']);
			return $merged;
		}
		
		// No routes found
		throw new CommandResolverException("No route found for \$uri:'$uri'. Reasons: ".rtrim($reasons,', '));
		
	}
}
