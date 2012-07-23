<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Upload extends Controller{
	
	public function before()
	{
		// parent function
		parent::before();
	}
	
	public function action_index()
	{
		// get instance of upload model
		$uploader = $this->fm->build("upload", array($this->request->files(), $this->request->server(), "media/uploads", $this->fm), false, false);
		// try uploading
		try {
			$response = $uploader->doUpload("files",$this->request->post());
			$this->response->body($response, true);
		} catch (Model_UploadException $e) {
			$this->response->body(array('err' => true, 'message' => $e->getMessage()), true);
		}
	}
	
}

?>