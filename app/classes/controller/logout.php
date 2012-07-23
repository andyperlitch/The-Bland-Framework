<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Logout extends Controller{
	
	public function action_index()
	{
		// unset all session vars, redirect
		$this->session->unsetSession();
		$this->request->redirect("/", NULL, true);
	}
	
}

?>