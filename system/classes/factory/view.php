<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Factory for processing view files
 *
 * @package Factory
 * @author Andrew Perlitch
 */
class Factory_View {
	
	public function build($file, $vars = array())
	{
		foreach ($vars as $key => $value) ${$key} = $value;
		ob_start();
		include(APPPATH.'view/'.$file.'.php');
		$res = ob_get_contents();
		ob_end_clean();
		return $res;
	}
	
}