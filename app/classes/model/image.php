<?php

class ImageException extends Exception {}
class Model_Image {
	
	// Set fields
	protected $image = "";
	protected $imageInfo = array();
	protected $fileInfo = array();
	protected $tmpfile = array();
	protected $pathToTempFiles = "";
	protected $Watermark;
	
	/**
	 * Constructor of this class
	 * @param string $image (path to image)
	 */
	function __construct($image){
	
		//Set path to temp files
		if(function_exists("sys_get_temp_dir")){
			$this->setPathToTempFiles(sys_get_temp_dir());
		}else{
			$this->setPathToTempFiles(rtrim(DOCROOT,'/'));
		}//if
	
		//Does file exist?
		if(file_exists($image)){
			$this->image  = $image;
			$this->readImageInfo();
		}else{
			throw new ImageException("File does not exist: ".$image);
		}
	}//function
	
	/************************************
	/* PUBLIC METHODS
	/************************************/
	
	public function info()
	{
		return $this->imageInfo;
	}
	
	public function crop( $x, $y, $width = null, $height = null, $w_over_h_ratio = null, $jpegQuality = 85, $pngCompression = 9)
	{
		// get current height and width
		$currentWidth = $this->imageInfo['width'];
		$currentHeight = $this->imageInfo['height'];
		
		// check for use of ratio
		if ($w_over_h_ratio != null) {
			// check if height supplied but not width
			if (!$width && $height) {
				// use height with ratio to calc width
				$width = floor($w_over_h_ratio * $height);
			} 
			// check if width supplied but not height
			elseif(!$height && $width){
				// use width with ratio to calc height
				$h_over_w_ratio = 1 / $w_over_h_ratio ;
				$height = floor($h_over_w_ratio * $width);
			}
		}
		
		// check if width and height are null
		if ($width === null) $width = $currentWidth - $x;
		if ($height === null) $height = $currentHeight - $y;
		
		// create true color image and image to be created from source image
		$canvas = imagecreatetruecolor($width, $height);
		$image;
		
		// place image on canvas
		switch($this->getType()){
			case "gif":
    			$image = imagecreatefromgif($this->image);
				imagecopyresampled( $canvas, $image, 0, 0, $x, $y, $currentWidth, $currentHeight, $currentWidth, $currentHeight );
				imagegif($canvas, $this->tmpfile);
    			break;
			
    		case "jpg":
    			$white = imagecolorallocate($canvas, 255, 255, 255);
    			imagefill($canvas, 0, 0, $white);
    			$image = imagecreatefromjpeg($this->image);
				imagecopyresampled( $canvas, $image, 0, 0, $x, $y, $currentWidth, $currentHeight, $currentWidth, $currentHeight );
				imagejpeg($canvas, $this->tmpfile, $jpegQuality);
				break;
			
			case "jpeg":
    			$white = imagecolorallocate($canvas, 255, 255, 255);
    			imagefill($canvas, 0, 0, $white);
    			$image = imagecreatefromjpeg($this->image);
				imagecopyresampled( $canvas, $image, 0, 0, $x, $y, $currentWidth, $currentHeight, $currentWidth, $currentHeight );
				imagejpeg($canvas, $this->tmpfile, $jpegQuality);
				break;

			case "png":
    			imagecolorallocate($canvas, 255, 0, 0);
    			$image = imagecreatefrompng($this->image);
				imagecopyresampled( $canvas, $image, 0, 0, $x, $y, $currentWidth, $currentHeight, $currentWidth, $currentHeight );
				imagealphablending($image, false);
				imagesavealpha($image, false);
				imagepng($canvas, $this->tmpfile, $pngCompression);
				break;
				
			default:
				throw new ImageException("Could not crop because image type not valid.");
				break;
		}
		
		// clear memory
		imagedestroy($canvas);
		imagedestroy($image);
		
		//Set new main image
		$this->setNewMainImage($this->tmpfile);
	}
	
	public function scale( 
		$width = null, 
		$height = null, 
		$maxWidth = null, 
		$maxHeight = null,
		$minWidth = null,
		$minHeight = null,
		$keepProportions = true, 
		$jpegQuality = 85, 
		$pngCompression = 9
	)
	{
		// get current height and width, ratio
		$currentWidth = $this->imageInfo['width'];
		$currentHeight = $this->imageInfo['height'];
		$w_over_h_ratio = $currentWidth / $currentHeight;
		
		// check for assummed values
		if ($width && !$height) $height = floor((1/$w_over_h_ratio) * $width);
		if ($height && !$width) $width = floor($w_over_h_ratio * $height);
		
		// if both null, make width and height current
		if (!$width && !$height) {
			$width = $currentWidth;
			$height = $currentHeight;
		}
		
		// check against maxWidth
		if ($maxWidth && $width > $maxWidth) {
			// change to maxWidth
			$width = $maxWidth;
			if ($keepProportions) $height = floor((1/$w_over_h_ratio) * $width);
		}
		
		// check against maxHeight
		if ($maxHeight && $height > $maxHeight) {
			// change to maxHeight
			$height = $maxHeight;
			if ($keepProportions) $width = floor($w_over_h_ratio * $height);
		}
		
		// check against minWidth
		if ($minWidth && $width < $minWidth) {
			$width = $minWidth;
			if ($keepProportions) $height = floor((1/$w_over_h_ratio) * $width);
		}
		
		// check against minHeight
		if ($minHeight && $height < $minHeight) {
			$height = $minHeight;
			if ($keepProportions) $width = floor($w_over_h_ratio * $height);
		}
		
		// create true color image and image to be created from source image
		$canvas = imagecreatetruecolor($width, $height);
		$image;
		
		// place image on canvas
		switch($this->getType()){
			case "gif":
    			$image = imagecreatefromgif($this->image);
				imagecopyresampled( $canvas, $image, 0, 0, 0, 0, $width, $height, $currentWidth, $currentHeight );
				imagegif($canvas, $this->tmpfile);
    			break;
			
    		case "jpg":
    			$white = imagecolorallocate($canvas, 255, 255, 255);
    			imagefill($canvas, 0, 0, $white);
    			$image = imagecreatefromjpeg($this->image);
				imagecopyresampled( $canvas, $image, 0, 0, 0, 0, $width, $height, $currentWidth, $currentHeight );
				imagejpeg($canvas, $this->tmpfile, $jpegQuality);
				break;
			
			case "jpeg":
    			$white = imagecolorallocate($canvas, 255, 255, 255);
    			imagefill($canvas, 0, 0, $white);
    			$image = imagecreatefromjpeg($this->image);
				imagecopyresampled( $canvas, $image, 0, 0, 0, 0, $width, $height, $currentWidth, $currentHeight );
				imagejpeg($canvas, $this->tmpfile, $jpegQuality);
				break;

			case "png":
    			imagecolorallocate($canvas, 255, 0, 0);
    			$image = imagecreatefrompng($this->image);
				imagecopyresampled( $canvas, $image, 0, 0, 0, 0, $width, $height, $currentWidth, $currentHeight );
				imagealphablending($image, false);
				imagesavealpha($image, false);
				imagepng($canvas, $this->tmpfile, $pngCompression);
				break;
				
			default:
				throw new ImageException("Could not scale because image type not valid.");
				break;
		}
		
		// clear memory
		imagedestroy($canvas);
		imagedestroy($image);
		
		//Set new main image
		$this->setNewMainImage($this->tmpfile);
	}
	
	public function save( $filename, $overwrite = true )
	{
		// get path of filename
		$path = dirname($filename).DIRECTORY_SEPARATOR;
		
		// check if current working dir
		if ($path == ".") $path = realpath(".").DIRECTORY_SEPARATOR;
		
		// get basename of filename
		$filename = basename($filename);
		
		// get path from webroot
		$webroot_path = preg_match('~^'.DOCROOT.'~', $path ) ? preg_replace( '~^'.DOCROOT.'~' , '/' , $path  ) : false ;
		
		// strip filename of ending extension
		$filename = preg_replace( '/(\.[^\.]*$)/' , '' , $filename  );
		
		// add extension
		$filename .= $this->getExtension(true);
	
		// create full path
		$fullPath = $path.$filename;
	
		// Copy file
		if(!copy($this->image, $fullPath)){
			throw new ImageException("Cannot save file ".$fullPath);
		}//if
		
		// Set new main image
		$this->setNewMainImage($fullPath);
		
		
		return array(
			$filename,
			$path,
			$fullPath,
			$webroot_path
		);
	}
	
	public function width()
	{
		return $this->imageInfo["width"];
	}
	public function height()
	{
		return $this->imageInfo["height"];
	}
	
	/**
	 * Gets mime type of image
	 * @return string
	 */
	public function getMimeType(){
		return $this->imageInfo["mime"];
	}//function	

	/**
	 * Gets mime type of image
	 * @return string
	 */
	public function getType(){
		return substr(strrchr($this->imageInfo["mime"], '/'), 1);
	}//function
	
	
	/************************************
	/* PROTECTED METHODS
	/************************************/
	
	/**
	 * Read and set some basic info about the image
	 * @param string $image (path to image)
	 */
	protected function readImageInfo(){
		
		//get data
		$data = getimagesize($this->image);
		
		//make readable
		$this->imageInfo["width"] = $data[0];
		$this->imageInfo["height"] = $data[1];
		$this->imageInfo["imagetype"] = $data[2];
		$this->imageInfo["htmlWidthAndHeight"] = $data[3];
		$this->imageInfo["mime"] = $data["mime"];
		$this->imageInfo["channels"] = ( isset($data["channels"]) ? $data["channels"] : NULL );
		$this->imageInfo["bits"] = $data["bits"];
		
		return true;
		
	}//function
	
	/**
	 * Gets type of image
	 * @return string
	 */
	protected function getExtension($withDot=false){
	
		$extension = image_type_to_extension($this->imageInfo["imagetype"]);
		$extension = str_replace("jpeg", "jpg", $extension);
		if(!$withDot){
			$extension = substr($extension, 1);
		}//if	
		
		return $extension;
	}//function
	
	
	/************************************
	/* SETTERS
	/************************************
	
	/**
	 * Sets path to temp files
	 * @param string $path
	 */
	protected function setPathToTempFiles($path){
	
		//Set path
		$path = realpath($path).DIRECTORY_SEPARATOR;
		$this->pathToTempFiles = $path;
		
		//Set tmpfile to save temporary files
		$this->tmpfile = tempnam($this->pathToTempFiles, "classImagePhp_");

		return true;
	}
	
	/**
	 * Sets new main image
	 * @param string $pathToImage
	 */
	protected function setNewMainImage($pathToImage){
	
		//set now
		$this->image = $pathToImage;
		
		//Read new image info
		$this->readImageInfo();
		
		return true;
		
	}//function
	
	public function __destruct(){
		if(file_exists($this->tmpfile)){
			unlink($this->tmpfile);
		}//if
	}//function
}

?>