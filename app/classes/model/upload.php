<?php defined('SYSPATH') or die('No direct script access.');

class Model_UploadException extends Exception{}
class Model_Upload extends Model{
	
	protected $private_upload_dir;
	protected $public_upload_dir;
	protected $mime;
	protected $files;
	protected $fm;
	
	function __construct($files, $server, $public_upload_dir = "uploads", Factory_Model $fm)
	{
		// store model factory
		$this->fm = $fm;
		
		// store files in field
		$this->files = $files;
		
		// Set path to the private upload directory (one level above the root)
		$this->private_upload_dir = preg_replace( '/(\/[^\/]+)$/' , '/uploads' , DOCROOT );
		
		// Set path to the public upload directory (below the root)
		$this->public_upload_dir = DOCROOT . $public_upload_dir ;
		
		
		
		// Check to ensure that directories exists
		if ( ! is_dir($this->private_upload_dir) ) {
			// Try to create dir
			if ( ! mkdir($this->private_upload_dir) ) throw new Model_UploadException("Private upload directory not found and could not be created (should be one level above the doc_root, folder called uploads).");
		} elseif ( ! is_dir($this->public_upload_dir) ) {
			// Try to create dir
			if ( ! mkdir($this->public_upload_dir) ) throw new Model_UploadException("Public upload dir not found and could not be created (set as a param in FileUpload::__construct)");
		}
	}
	
	// general
    /*
 	* prefix:"img-",                               // general prefix used for input names[]
    * uploaddir:"/media/uploads/",                  // directory to (ultimately) upload images to
    * uploadinputname:"files",                     // the name of the input[type="file"] when uploading
    * uploadtext:"click to upload",                // text of the main "upload button"
    * uploadaction:"/upload",                      // action of the upload form
    * maxfiles:0,                                  // maximum number of files to be selected
    * allowimagesonly:false,                       // whether or not to allow other images
    * 
    * // title
    * allowtitle:true,
    * 
    * // thumbnails
    * makethumbs:true,                             // making thumbs?
    * thumbext:"-thb",                             // extension for thumbnail files
    * thumbwidth:60,                               // width of thumbs
    * thumbratio:1,                                // ratio of thumb dimensions => width/height
    * thumbadjusttext:"adjust thumb",              // text of the button to adjust thumbnail
    * 
    * // captions
    * makecaptions:true,                           // making captions?
    * captionlabel:"caption",
    * 
    * // cropping
    * allowcrop:false,                              // allowing crop?
    * maximgwidth:null,                            // max image width (if maximgheight not set, taken as width)
    * maximgheight:null,                           // max image height (if maximgwidth not set, taken as height)
    * imgratio:0,                                  // ratio to crop image at
    * cropaction:'/crop'                           // action for cropping form (same for thumbnail and images)
	*/
	
	public function doUpload($specific_key = null, array $options = array() )
	{		
		// make keys boolean
		$this->mkPostKeyBoolean(&$options, array('allowimagesonly',));
		
		$return_files_array = array();
		
		// get next index
		$idx = (int) $options['nextindex'];
		
		// loop through file inputs in superglobal
		foreach ($this->files as $key => $file) {
			
			if ($specific_key !== null && $key != $specific_key) continue;
			
			// set new array for all (or one) file(s)
			$input_files = array();
			// file count for this input
			$input_files_count = 1;
			
			// check if multiple attribute enabled (if so, the name key should be an array)
			if ( is_array($this->files[$key]["name"]) ) {
				$input_files_count = count($this->files[$key]["name"]);
				for($i = 0; $i < $input_files_count; $i++) {
					$input_files[] = array(
						'name' => $this->files[$key]["name"][$i],
						'type' => $this->files[$key]["type"][$i],
						'tmp_name' => $this->files[$key]["tmp_name"][$i],
						'error' => $this->files[$key]["error"][$i],
						'size' => $this->files[$key]["size"][$i]
					);
				}
			} else {
				$input_files[] = $this->files[$key];
			}
			
			foreach ($input_files as &$file) {
				
				// Error::log('$file:'.print_r($file,true),'model/upload.php',__LINE__);
				
				// set index
				$file['index'] = $idx++;

				// Check for file error
				if ($file["error"] === UPLOAD_ERR_OK ) {
					
					// clean filename
					$file["name"] = $this->cleanFilename($file["name"]);
					if ($file["name"] === false) {
						$file["errorMessage"] = "The name of this file is unvalid.";
						continue;
					}

					// Try to move file
					if ( ! move_uploaded_file( $file["tmp_name"] , "$this->private_upload_dir/{$file['name']}") ) {
						$file["errorMessage"] = "An error occurred uploading this file.";
						continue;
					}

					// Check mime type
					$mime = $this->getMimeType("$this->private_upload_dir/{$file['name']}");
					if ($mime == "other") {
						$file["errorMessage"] = "This file is of an unaccepted filetype.";
						// delete file
						unlink("$this->private_upload_dir/{$file['name']}");
						continue;
					}
					
					if ( $options['allowimagesonly'] ) {
						// check if image
						if (!in_array($mime, array('jpg','png','gif'))) {
							$file["errorMessage"] = "Image was not of the correct type.";
							// delete file
							unlink("$this->private_upload_dir/{$file['name']}");
							continue;
						}
						// set original height and width
						$dims = getimagesize("$this->private_upload_dir/{$file['name']}");
						$file["width"] = $dims[0];
						$file["height"] = $dims[1];
					}

					// Upload succeeded, move to readable location
					$this->moveFileToPublicUpload($file, $mime);
					
					// if set, make thumbnails
					if ( $options['makethumbs'] && in_array($mime, array('jpg','gif','png')) ) {
						$file['thumb'] = $this->mkThumbnail($file, $mime, $options);
					}
					else $file['thumb'] = null;
					
					// unset unnecessary fields
					unset($file["tmp_name"]);
					
				} else {
					// log upload error
					Error::log('Error on upload file. UPLOAD ERROR CODE:'.$file["error"],'model/upload.php',__LINE__);
					// set error message
					$file['errorMessage'] = 'There was an error uploading this file ('.$file['error'].')!';
				}
				
				// if error, reset index back
				
			}
			
			// add to the return array
			$return_files_array[$key] = $input_files;
		}
		
		// return final array
		if ( $specific_key !== null ) {
			if (array_key_exists($specific_key, $return_files_array)) return $return_files_array[$specific_key];
			else return false;
		} else {
			return $return_files_array;
		}
	}
	
	protected function getMimeType($filename) 
	{
	    $accepted_exts = array('jpg','jpeg','gif','png','txt','pdf','doc');
		preg_match('/\.([^\.]+)$/', $filename, $matches);
		$ext = $matches[1];
		if (in_array($ext, $accepted_exts)) return $ext;
		throw new Model_UploadException("File extension not accepted. \$ext: $ext");
	}
	
	protected function moveFileToPublicUpload(&$file, $ext)
	{
		// get random filename
		$filename = $this->getNewName($file["name"],$ext);
		
		// move file to public upload dir
		$move = rename("{$this->private_upload_dir}/{$file["name"]}" , "{$this->public_upload_dir}/$filename");
		
		// change "name" value to new value
		$file["name"] = $filename;
		
		// if successful, return the generated filename
		if ( !$move ) throw new Model_UploadException("Could not move file to public upload location");
	}
	
	protected function getNewName($filename, $ext, $k = 0)
	{
        // check if $filename is already in public upload dir
		if ( !file_exists( "{$this->public_upload_dir}/$filename" ) ) return $filename;
		
		// $filename exists in dir, make change
		// iterate suffix
		$k++;
		// get basename of file (no extension)
		$filename = $this->stripExtension($filename);
		// concat new suffix with extension, send through same function
		return $this->getNewName("{$filename}-$k.$ext", $ext, $k);
	}
	

	protected function mkThumbnail(&$file, $ext,  $options)
	{
		// get basename of file (no extension)
		$basename = $this->stripExtension($file["name"]);
		
		// use getNewName
		$thumbname = basename($this->getNewName("{$basename}{$options['thumbext']}.$ext",$ext),".$ext");
		
		try {
			// create new image
			$thumb = $this->fm->build("image", array("{$this->public_upload_dir}/{$file["name"]}"), false, false);

			// scale so that minWidth and minHeight are size of thumb
			$thumb->scale($options['thumbwidth'],null,null,null,null,$options['thumbwidth']/$options['thumbratio']);
			$thumb->crop(0,0,$options['thumbwidth'],$options['thumbwidth']/$options['thumbratio']);
			
			// save new image
			$saved = $thumb->save("{$this->public_upload_dir}/$thumbname");
			
			// return basename
			return $saved[0];
			
		} catch (ImageException $e) {
			$file["error"] = true;
			$file["errorMessage"] = $e->getMessage();
			return null;
		}
	}
	
	protected function stripExtension($filename)
	{
		return preg_replace( '/([-]*[\d]*\.[^\.]+)$/' , '' , $filename  );
	}
	
	protected function cleanFilename($filename)
	{
		// strip of spaces
		$filename = preg_replace( '/[\s]+/' , '-' , $filename );
		// strip all periods except the last one
		$filename = preg_replace( '/\.(?=.*\.)/' , '-' , $filename );
		// trim periods
		$filename = trim($filename,'.');
		// replace multiple dashes with single dashes
		$filename = preg_replace( '/[-]{2,}/','-', $filename);
		return preg_match('/^[^\.\s]+\.[^\.\s]+$/', $filename) ? $filename : false;
	}
	
	protected function mkPostKeyBoolean(&$options, array $keys)
	{
		if (!empty($keys)){
			
			foreach ($keys as $key) {
				$options[$key] = $options[$key] === "false" ? false : !!$options[$key] ;
			}
		}
	}
}

?>