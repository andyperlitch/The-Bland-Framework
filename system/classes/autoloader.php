<?php defined('SYSPATH') or die('No direct script access.');
class AutoloadException extends Exception { }
class AutoLoader{
	public static function autoload($class) {
		
		// convert to path (replace underscores)
		$file = preg_replace('/_/', '/', strtolower($class));
		// first try in controllers
		if(file_exists(APPPATH."classes/$file.php")) {
			include(APPPATH."classes/$file.php");
		}elseif(file_exists(SYSPATH."classes/$file.php")) {
			include(SYSPATH."classes/$file.php");
		}
		
		// does the class requested actually exist now?
		if (class_exists($class)) {
			// yes, we're done
			return;
		}

		// no, create a new one!
		eval("
			class $class {
				function __construct() {
			 		throw new AutoloadException('Class $class not found');
				}

				static function __callstatic(\$m, \$args) {
			 		throw new AutoloadException('Class $class not found');
				}
			}
		");
	}
}