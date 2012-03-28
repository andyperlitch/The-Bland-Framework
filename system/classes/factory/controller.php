<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Builds controller object for given server and request info.
 *
 * @package Controller
 * @author Andrew Perlitch
 */
class Factory_Controller{
	
	/**
	 * Returns controller object.
	 * Request object requires $_SERVER, $_GET, and $_POST arrays.
	 *
	 * @param array $server 
	 * @param array $get 
	 * @param array $post 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function build(array $server, array $get, array $post, $environment)
	{
		// instantiate new config, request, and session objects for controller.
		$c = new Config($environment);
		$r = new Request($server, $get, $post);
		$s = new Session();
		
		// Reads config file with route info
		$routes = include(APPPATH.'config/routes.php');
		
		// Get request params (must include action key and controller key)
		$params = $this->_getRequestParams($routes, $r->uri());
		
		try {
			// Returns correct child object of Controller.
			return new $params['controller']($c, $r, $s, $params['action']);
		} catch (AutoloadException $e) {
			// Return 404 page
			return new Controller_Error($c, $r, $s, 'action_404');
		}
	}
	
	/**
	 * Determines request params using routes and uri.
	 *
	 * @param array $routes    Routes to choose from
	 * @param string $uri      The URI of the request
	 * @return array
	 * @author Andrew Perlitch
	 */
	protected function _getRequestParams($routes,$uri)
	{
		// Keep string for failure reasons
		$reasons = '';
		
		// Begin loop through $routes to determine first match
		foreach ($routes as $route) {
						
			// Create the regex params array
			if( ! preg_match( $route['pattern'], $uri, $matches) ) {
				$reasons .= "'{$route['pattern']}': No match, ";
				continue;
			}
			
			// Take off first capture
			array_splice(&$matches,0,1);
			
			// Check that matches count is the same as keys
			if (count($route['keys']) < count($matches) ) {
				$reasons .= "'{$route['patter']}': Not enough keys provided for matches, ";
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
		throw new FactoryException("No route found for \$uri:'$uri'. Reasons: ".rtrim($reasons,', '));
		
	}
	
	protected function _getControllerClassName($controller)
	{
		// Add 'Controller_' prefix, make uppercase letters
		return 'Controller_'.ucfirst( preg_replace( '/(_([a-z]{1}))/e' , "strtoupper('\\1')" , $controller  ) );
	}
}