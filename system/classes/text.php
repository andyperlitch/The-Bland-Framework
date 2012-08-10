<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Util class for prepping text from database for html.
 *
 * @package default
 * @author Andrew Perlitch
 */
class Text{
	
	public static function prep($message, $links = true, $images = true)
	{
		// check for empty string
		if (trim($message) == "") return "";
		
		// hsc and nl2br
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
		// check for span class/color
		$span_pattern = "/--(.*?)--\((.*?)\)/";
		$message = preg_replace_callback(
			$span_pattern,
			function($matches){
				if (preg_match('/#[A-Za-z0-9]{6}/',$matches[2])) return "<span style=\"color:".$matches[2].";\">".trim($matches[1],'>) ')."</span>";
				return "<span class=\"".$matches[2]."\">".trim($matches[1],'>) ')."</span>";
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
	
}

?>