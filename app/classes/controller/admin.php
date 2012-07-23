<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Templates_Html5{
	
	public function before()
	{
		// parent function
		parent::before();
			
		// check if logged in
		if ( !isset( $this->session["user"] ) || in_array("admin",$this->session['user']['roles']) ) {
			// Error::log('User attempted to request /admin page without proper credentials','admin.php',__LINE__);
			$this->redirectUser();
		}
	}
	
	public function action_index()
	{
		// meta
		$this->template['bid'] = 'admin';
		$this->template['title'] = 'Admin Page';
		
		// admin hope page view
		$this->template['body'] = $this->fv->build('admin/home', $this->template);
	}
	
}

?>