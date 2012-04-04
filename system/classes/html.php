<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class of static methods for HTML helpers.
 * Only to be called in views!
 *
 * @package default
 * @author Andrew Perlitch
 */
class HTML{
	
	public static function script($src, $type = 'text/javascript')
	{
		return '<script type="'.$type.'" charset="utf-8" src="'.$src.'"></script>';
	}
	
	public static function style($href, $media, $title = null)
	{
		$style = '<link rel="stylesheet" href="'.$href.'" charset="utf-8" media="'.$media.'"';
		if ($title !== null) $style .= ' title="'.$title.'"';
		$style .= '>';
		return $style;
	}
	
	public static function meta(array $attributes)
	{
		$meta = '<meta';
		foreach ($attributes as $name => $value) {
			$meta .= ' '.$name . '="'.$value.'"';
		}
		$meta .= '>';
		return $meta;
	}
}