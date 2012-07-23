<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Crop extends Controller{
	
	public function action_index()
	{
		// get image src
		$src = basename($this->request->post("src"));
		$dir = "media/uploads/";
		$src_with_path = DOCROOT . $dir . $src;
		
		try { // to instantiate an instance of Model_Image
			$image = $this->fm->build("image", array($src_with_path), false, false );
			
			// get max width and max height values, plus ratio
			$scale_width = floor( (float) $this->request->post("width",true) );
			$scale_height = floor( (float) $this->request->post("height",true) );
			$scale_maxWidth = floor( (float) $this->request->post("maxWidth",true) );
			$scale_maxHeight = floor( (float) $this->request->post("maxHeight",true) );
			$scale_minWidth = floor( (float) $this->request->post("minWidth",true) );
			$scale_minHeight = floor( (float) $this->request->post("minHeight",true) );
			$scale_keepProportions = ! (!!$this->request->post("keepProportions",true));
			
			$ratio = (float) $this->request->post("ratio");
			$crop_w = floor( (float) $this->request->post("w") );
			$crop_h = floor( (float) $this->request->post("h") );
			$crop_x = floor( (float) $this->request->post("x") );
			$crop_y = floor( (float) $this->request->post("y") );
			
			// do crop
			$image->crop( $crop_x, $crop_y, $crop_w, $crop_h, $ratio);
			// do scale
			$image->scale( 
				$scale_width, 
				$scale_height, 
				$scale_maxWidth, 
				$scale_maxHeight,
				$scale_minWidth,
				$scale_minHeight,
				$scale_keepProportions
			);
			

			// get basename of destination file with no extension
			$dest = basename($this->request->post("dest"));

			// save image
			$saved_image = $image->save(DOCROOT.$dir.$dest);

			// set response json
			$this->response->body(
				array(
					"success" => is_array($saved_image), 
					"queryString" => '?'.time(),
					"dir" => $saved_image[3],
					"src" => $saved_image[0],
					"width" => $image->width(),
					"height" => $image->height()
				)
			, true);
			
		} catch (Exception $e) {
			//log exc
			Error::logExc( $e , __FILE__ , __LINE__ );
			// set response to error
			$this->response->body(array("err"=>true),true);
		}
		
		
	}
	
}

?>