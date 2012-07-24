<?php defined('SYSPATH') or die('No direct script access.');

class Form {
	
	protected static $attribute_order = array(
		'action',
		'method',
		'type',
		'id',
		'name',
		'value',
		'href',
		'src',
		'width',
		'height',
		'cols',
		'rows',
		'size',
		'maxlength',
		'rel',
		'media',
		'accept-charset',
		'accept',
		'tabindex',
		'accesskey',
		'alt',
		'title',
		'class',
		'style',
		'selected',
		'checked',
		'readonly',
		'disabled',
	);
	
	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 * Attributes will be sorted using self::$attribute_order for consistency.
	 *
	 *     echo '<div'.self::attributes($attrs).'>'.$content.'</div>';
	 *
	 * @param   array   attribute list
	 * @return  string
	 */
	public static function attributes(array $attributes = NULL)
	{
		if (empty($attributes))
			return '';

		$sorted = array();
		foreach (self::$attribute_order as $key)
		{
			if (isset($attributes[$key]))
			{
				// Add the attribute to the sorted list
				$sorted[$key] = $attributes[$key];
			}
		}

		// Combine the sorted attributes
		$attributes = $sorted + $attributes;

		$compiled = '';
		foreach ($attributes as $key => $value)
		{
			if ($value === NULL)
			{
				// Skip attributes that have NULL values
				continue;
			}

			if (is_int($key))
			{
				// Assume non-associative keys are mirrored attributes
				$key = $value;
			}

			// Add the attribute value
			$compiled .= ' '.$key.'="'.self::chars($value).'"';
		}

		return $compiled;
	}
	
	public static function createFormInput($name,$title,$type,$value=NULL,$post=array(),$errors=array(),$note='',array $attributes=array(),array $values=NULL)
	{	
		// error condition
		$err = array_key_exists($name,$errors);
		$err_msg = $err ? $errors[$name] : '';
		
		// default attributes
		$default_attributes = array('id' => $name);
		
		$new_attributes = array_merge($default_attributes, $attributes);
		
		// begin fieldset
		$html = '<div id="'.$name.'-wrapper"';
		$html .= $err ? ' class="input-error"' : '';
		$html .= '>';
		
		// check post for value
		if (array_key_exists($name,$post)) $value = $post[$name];
		
		switch ($type) {
			case 'select':
				$html .= self::label($name, $title, NULL, $err_msg);
				$html .= self::select($name, $values, $value, $new_attributes);
			break;
			case 'password':
				$html .= self::label($name, $title, NULL, $err_msg);
				$html .= self::password($name, $value, $new_attributes);
			break;
			case 'hidden':
				$html = self::hidden($name, $value, $new_attributes);
				return $html;
			break;
			case 'checkbox':
				
			break;
			case 'textarea':
				$html .= self::label($name, $title, NULL, $err_msg);
				$html .= self::textarea($name, $value, $new_attributes, TRUE);
			break;
			case 'file':
				$html .= self::label($name, $title, NULL, $err_msg);
				$html .= self::file($name, $value, $new_attributes);
			break;
			case 'bland_file':
				$html .= self::label($name, $title, NULL, $err_msg);
				$values = is_array($value) ? $value : array();
				$html .= self::bland_file($name, $values, $post, $attributes, $default_attributes);
			break;
			default:
				$html .= self::label($name, $title, NULL, $err_msg);
				$html .= self::input($name, $value, $new_attributes);
			break;
		}
		
		// note
		if($note != '') {
            $html .= '<span class="inp_note">'.$note.'</span>';
        }
		
		// end fieldset
        $html .= '</div>';
        return $html;
	}
	
	public static function label($input, $text = NULL, array $attributes = NULL, $err_msg = '')
	{
		if ($text === NULL)
		{
			// Use the input name as the text
			$text = ucwords(preg_replace('/[\W_]+/', ' ', $input));
		}
		
		// Set the label target
		$attributes['for'] = $input;
		
		// begin return string
		$res = '<label'.self::attributes($attributes).'>'.$text.'</label>';
		
		// check for error
		if ($err_msg != '') $res .= '<label for="'.$input.'" class="err">'.$err_msg.'</label>';
		
		return $res;
	}

	/**
	 * Creates a select form input.
	 *
	 *     echo self::select('country', $countries, $country);
	 *
	 * @param   string   input name
	 * @param   array    available options
	 * @param   mixed    selected option string, or an array of selected options
	 * @param   array    html attributes
	 * @return  string
	 * @uses    self::attributes
	 */
	public static function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
	{
		// Set the input name
		$attributes['name'] = $name;

		if (is_array($selected))
		{
			// This is a multi-select, god save us!
			$attributes['multiple'] = 'multiple';
		}

		if ( ! is_array($selected))
		{
			if ($selected === NULL)
			{
				// Use an empty array
				$selected = array();
			}
			else
			{
				// Convert the selected options to an array
				$selected = array( (string) $selected);
			}
		}

		if (empty($options))
		{
			// There are no options
			$options = '';
		}
		else
		{
			foreach ($options as $value => $name)
			{
				if (is_array($name))
				{
					// Create a new optgroup
					$group = array('label' => $value);

					// Create a new list of options
					$_options = array();

					foreach ($name as $_value => $_name)
					{
						// Force value to be string
						$_value = (string) $_value;

						// Create a new attribute set for this option
						$option = array('value' => $_value);

						if (in_array($_value, $selected))
						{
							// This option is selected
							$option['selected'] = 'selected';
						}

						// Change the option to the HTML string
						$_options[] = '<option'.self::attributes($option).'>'.self::chars($_name, FALSE).'</option>';
					}

					// Compile the options into a string
					$_options = "\n".implode("\n", $_options)."\n";

					$options[$value] = '<optgroup'.self::attributes($group).'>'.$_options.'</optgroup>';
				}
				else
				{
					// Force value to be string
					$value = (string) $value;

					// Create a new attribute set for this option
					$option = array('value' => $value);

					if (in_array($value, $selected))
					{
						// This option is selected
						$option['selected'] = 'selected';
					}

					// Change the option to the HTML string
					$options[$value] = '<option'.self::attributes($option).'>'.self::chars($name, FALSE).'</option>';
				}
			}

			// Compile the options into a single string
			$options = "\n".implode("\n", $options)."\n";
		}

		return '<select'.self::attributes($attributes).'>'.$options.'</select>';
	}
	
	/**
	 * Creates a hidden form input.
	 *
	 *     echo self::hidden('csrf', $token);
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    self::input
	 */
	public static function hidden($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'hidden';

		return self::input($name, $value, $attributes);
	}
	
	/**
	 * Creates a textarea form input.
	 *
	 *     echo self::textarea('about', $about);
	 *
	 * @param   string   textarea name
	 * @param   string   textarea body
	 * @param   array    html attributes
	 * @param   boolean  encode existing HTML characters
	 * @return  string
	 * @uses    self::attributes
	 * @uses    self::chars
	 */
	public static function textarea($name, $body = '', array $attributes = NULL, $double_encode = TRUE)
	{
		// Set the input name
		$attributes['name'] = $name;

		// Add default rows and cols attributes (required)
		$attributes += array('rows' => 10, 'cols' => 50);

		return '<textarea'.self::attributes($attributes).'>'.self::chars($body, $double_encode).'</textarea>';
	}
	
	/**
	 * Creates a form input. If no type is specified, a "text" type input will
	 * be returned.
	 *
	 *     echo self::input('username', $username);
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    self::attributes
	 */
	public static function input($name, $value = NULL, array $attributes = NULL)
	{
		// Set the input name
		$attributes['name'] = $name;

		// Set the input value
		$attributes['value'] = $value;

		if ( ! isset($attributes['type']))
		{
			// Default type is text
			$attributes['type'] = 'text';
		}

		return '<input'.self::attributes($attributes).' />';
	}
	
	/**
	 * Creates a checkbox form input.
	 *
	 *     echo self::checkbox('remember_me', 1, (bool) $remember);
	 *
	 * @param   string   input name
	 * @param   string   input value
	 * @param   boolean  checked status
	 * @param   array    html attributes
	 * @return  string
	 * @uses    self::input
	 */
	public static function checkbox($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'checkbox';

		if ($checked === TRUE)
		{
			// Make the checkbox active
			$attributes['checked'] = 'checked';
		}

		return self::input($name, $value, $attributes);
	}
	
	/**
	 * Creates a password form input.
	 *
	 *     echo self::password('password');
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 * @uses    self::input
	 */
	public static function password($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'password';

		return self::input($name, $value, $attributes);
	}
	
	/**
	 * Creates a file input
	 *
	 * @param string $name 
	 * @param string $value 
	 * @param array $attributes 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public static function file($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'file';
		return self::input($name, $value, $attributes);
	}
	
	public static function bland_file($prefix, array $values = NULL, array $post, array $options = NULL, array $attributes)
	{
		// add prefix to options
		$options['prefix'] = $prefix;
		
		// merge options with defaults
		$options = self::merge_bland_file_options($options);
		
		$html = '<div id="'.$prefix.'upload" ';
		if (!empty($options)){
			foreach ($options as $key => $value) {
				$html .= 'data-'.$key.'="'.$value.'" ';
			}
		}
		$html .= '>';
		
		// build rows from post data
		// Error::log('$post:'.print_r($post,true),'form.php',__LINE__);
		
		
		$html .= '</div>';
		return $html;
	}
	
	protected static function merge_bland_file_options($options)
	{
		// define the default options for jquery.blandUpload
		$defaults = array(
			// general
	        'prefix' => "img-",                               // general prefix used for input names[]
	        'uploaddir' => "/media/uploads/",                  // directory to (ultimately) upload images to
	        'uploadinputname' => "files",                     // the name of the input[type="file"] when uploading
	        'uploadtext' => "click to upload",                // text of the main "upload button"
	        'uploadaction' => "/upload",                      // action of the upload form
	        'maxfiles' => 0,                                  // maximum number of files to be selected
	        'allowimagesonly' => false,                       // whether or not to allow other images
        
	        // title
	        'allowtitle' => true,
        
	        // thumbnails
	        'makethumbs' => true,                             // making thumbs?
	        'thumbext' => "-thb",                             // extension for thumbnail files
	        'thumbwidth' => 60,                               // width of thumbs
	        'thumbratio' => 1,                                // ratio of thumb dimensions => width/height
	        'thumbadjusttext' => "adjust thumb",              // text of the button to adjust thumbnail
        
	        // captions
	        'makecaptions' => true,                           // making captions?
	        'captionlabel' => "caption",
        
	        // cropping
	        'allowcrop' => false,                              // allowing crop?
	        'maximgwidth' => null,                            // max image width (if maximgheight not set, taken as width)
	        'maximgheight' => null,                           // max image height (if maximgwidth not set, taken as height)
	        'imgratio' => 0,                                  // ratio to crop image at
	        'cropaction' => '/crop'                           // action for cropping form (same for thumbnail and images)
		);
		
		// merge with options
		return array_merge($defaults, $options);
	}
	
	/**
	 * Uses htmlspecialchars() to escape html chars.
	 *
	 * @param string $value              Value to encode
	 * @param bool $double_encode        Whether or not to double-encode
	 * @return string
	 * @author Andrew Perlitch
	 */
	public static function chars($value, $double_encode = TRUE)
	{
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8', $double_encode);
	}
}