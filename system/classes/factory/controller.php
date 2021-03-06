<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Builds controller object for given server and request info.
 *
 * @package Factory
 * @author Andrew Perlitch
 */
class Factory_Controller extends Factory{
	
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
	public function build(array $server, array $get, array $post, array $files, $environment)
	{
		// instantiate new config, request, and session objects for controller.
		$c = new Config($environment);
		$req = new Request($server, $get, $post, $files);
		$s = new Session();
		$res = new Response();
		$fm = new Factory_Model($c);
		$fv = new Factory_View();
		try {
			// Reads config file with route info
			$routes = include(APPPATH.'config/routes.php');
		
			// Get request params (must include action key and controller key)
			$params = $this->_getRequestParams($routes, $req->uri());
			
			// check and prep controller name
			$params['controller'] = $this->_prepControllerName($params['controller']);
			
			// set request params in Request object
			$req->setParams($params);
			
			// Returns correct child object of Controller.
			$class = new $params['controller']($c, $req, $s, $res, $fm, $fv, $params['action']);
			if( !method_exists($params['controller'],$params['action'])) throw new Exception("Action '{$params['action']}' not found in controller");
			return $class;
		}
		catch (FactoryException $e) {
			Error::logExc( $e , __FILE__ , __LINE__ );
			// Return 404 page
			return new Controller_Error($c, $req, $s, $res, $fm, $fv, 'action_404');
		} 
		catch (Exception $e) {
			// Return 404 page
			return new Controller_Error($c, $req, $s, $res, $fm, $fv, 'action_404');
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
			array_shift($matches);

			// Check that matches count is the same as keys
			if (count($route['keys']) < count($matches) ) {
				$reasons .= "'{$route['patter']}': Not enough keys provided for matches, ";
				continue;
			}
			
			// combine
			$combined = array_combine($route['keys'],$matches);
			
			// create merged array
			$merged = array();
			
			// make empty values default values
			foreach ($combined as $key => $value) {
				if ($value == "") {
					if (array_key_exists($key,$route['defaults'])) $merged[$key] = $route['defaults'][$key];
				} else {
					$merged[$key] = $value;
				}
			}
			
			// Check that controller and action are specified in resulting array
			if ( (! array_key_exists('controller', $merged)) || (! array_key_exists('action',$merged)) ) {
				$reasons .= "controller and/or action not found in final params array, ";
				continue;
			}
			
			// All tests pass, change controller and action, then return $merged
			$merged['controller'] = $this->_getClassName($merged['controller']);
			$merged['action'] = 'action_'.$this->_prepActionName($merged['action']);
			return $merged;
		}
		
		// No routes found
		throw new FactoryException("No route found for \$uri:'$uri'. Reasons: ".rtrim($reasons,', '));
		
	}
	
	private function _prepActionName($action)
	{
		return strtolower( preg_replace('/[-]/','_',$action) );
	}
	
	private function _prepControllerName($name)
	{
		return preg_replace( '/[-]/' , '' , $name  );
	}
}