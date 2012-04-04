<?php
/**
 * HTML5 template.
 *
 * @param Array $meta_tags     Array of info on meta tags. Format below.
 * @param String $title        Title of page.
 * @param  Array $favicon      Array of info on favicon. Format below. (optional)
 * @param  Array $styles       Array of stylesheets. Format below.
 * @param  String $css_dir     Directory to css files.
 * @param  String $bid         id attribute of body element.
 * @param  String $bclass      class attribute of body element (optional).
 * @param  String $body        Markup inside the body tags.
 * @param  Array $scripts      Array of scripts to be included at the bottom of the page.
 * @param  String $js_dir      Directory to js files.
 * @package Templates
 * @author Andrew Perlitch
 */

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
		foreach ($meta_tags as $tag) {
			echo HTML::meta($tag);
		}
		?>
		<title><?=$title?></title>
		<?php
		if (isset($favicon)) echo '<link rel="icon" href="'.$favicon['href'].'" type="'.$favicon['type'].'">';
		foreach ($styles as $style) {
			echo HTML::style($css_dir.$style['href'], $style['media'], @$style['title']);
		}
		?>
		<!--[if IE]>
        <link rel="stylesheet" href="/media/css/ie.css" type="text/css" media="screen" charset="utf-8">
        <![endif]-->
        <!--[if lte IE 7]>
        <link rel="stylesheet" href="/media/css/ie.lte7.css" type="text/css" media="screen" charset="utf-8">
        <![endif]-->
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js">IE7_PNG_SUFFIX=".png";</script>
		<![endif]-->
		<noscript>
			<link rel="stylesheet" href="/media/css/noscript.css" type="text/css" media="screen" charset="utf-8">
		</noscript>
	</head>
	<body id="<?=$bid?>"<?php if (isset($bclass)) echo ' class="'.$bclass.'"'?>>
		<?=$body?>
		<?php
		foreach($scripts as $script){
			echo ( preg_match('/^http:\/\//',$script) ) ? HTML::script($script) : HTML::script($js_dir.$script) ;
		}
		?>
	</body>
</html>