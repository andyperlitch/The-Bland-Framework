<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class of static methods for HTML helpers.
 * Only to be called in views!
 *
 * @package default
 * @author Andrew Perlitch
 */
class HTML{
	
	public static function script($src, $type = NULL)
	{
		$tag = '<script';
		if ( $type !== NULL ) $tag .= ' type="'.$type.'"';
		$tag .= ' src="'.$src.'"></script>';
		return $tag;
	}
	
	public static function style($href, $media, $title = null)
	{
		$style = '<link rel="stylesheet" href="'.$href.'" media="'.$media.'"';
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
	
	public static function prep($message, $links = true, $images = true)
	{
		$message = nl2br(htmlspecialchars($message));
		
		// check for bold
		$bold_pattern = "/(\*\*.*?\*\*)/";
		$message = preg_replace_callback(
			$bold_pattern,
			function($matches){
				return "<strong>".trim($matches[0],'* ')."</strong>";
			},
			$message
		);
		// check for italics
		$italic_pattern = "/__(.*?)__/";
		$message = preg_replace_callback(
			$italic_pattern,
			function($matches){
				return "<em>".trim($matches[0],'_ ')."</em>";
			},
			$message
		);
		// check for links
		if ($links) {
			// link format = [link text](link_url)
			$pattern = '/\[([^\]]+)\]\(([^\)\s]+)\)(\([ei]\))?/';
			$message = preg_replace_callback( 
				$pattern ,
				function($matches){
					$extra = '';
					if( isset($matches[3]) && $matches[3] == '(i)' && preg_match('/gif|jpg|png|jpeg$/i',$matches[2]) ) {
						return '<img src="'.$matches[2].'" alt="'.$matches[1].'" />';
					}
					elseif ( isset($matches[3]) && $matches[3] == '(e)' ) {
						$extra = ' target="_blank"';
					}
					return '<a href="'.$matches[2].'"'.$extra.'>'.$matches[1].'</a>';
				}, 
				$message
			);
		}
		return $message;
	}
	
	public static function timeSinceOrUntil($timestamp, $to = null, $numClauses = 2)
	{
		// set $to
		$to = $to === null ? time() : $to;
		$to = is_int($to) ? $to : strtotime($to);
		$timestamp = is_int($timestamp) ? $timestamp : strtotime($timestamp) ;
		$past = false;
		$res = "";
		$clauseCount = 0;
		
		// units to divide by
		$units = array(
			"month"  => 2419200,
			"week"   => 604800,   // seconds in a week   (7 days)
			"day"    => 86400,    // seconds in a day    (24 hours)
			"hour"   => 3600,     // seconds in an hour  (60 minutes)
			"minute" => 60,       // seconds in a minute (60 seconds)
			"second" => 1         // 1 second
		);
		
		// get difference
		$diff = $timestamp - $to;
		// check for since or until
		$past = $diff < 0;
		// get absolute value
		$diff = abs($diff);
		// loop through units
		foreach($units as $unit => $mult){
			// if atleast one can go into diff...
			if ( $diff >= $mult ) {
				// get floor
				$num = floor($diff/$mult);
				$diff -= $num * $mult;
				$res .= $num . " " . $unit;
				$res .= $num > 1 ? "s" : "";
				$clauseCount++;
				if ( $clauseCount >= $numClauses ) break;
				$res .= ", ";
			}
		}
		
		$res = rtrim($res,', ');
		return $past ? $res . " ago" : "in " . $res;
	}
}