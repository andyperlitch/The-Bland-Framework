<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Basic template for head and body.
 *
 * @package Templates
 * @author Andrew Perlitch
 */
class Controller_Templates_Html5 extends Controller{
	
	/**
	 * Holds all variables for use in views.
	 * Can be used with Factory_View::build() as 2nd param.
	 *
	 * @var array
	 */
	protected $template = array();
	
	/**
	 * Sets up base styles and scripts to include.
	 *
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function before()
	{
		parent::before();
		// meta tags
		$this->template['meta_tags'] = array(
			// charset
			'charset' => 
				array(
					'charset' => 'utf-8'
				), 
				
			// google opt-out of odp
			'odp' =>
				array(
					'NAME' => 'ROBOTS',
					'CONTENT' => 'NOODP'
				),
			// description
			'description' =>
				array(
					'name' => 'description',
					'content' => 'The Bland Framework is a light-weight PHP framework, focused on testability, security, and ease-of-use'
				),
		);
		
		// favicon
		$this->template['favicon'] = array(
			'href' => '/media/images/favicon.gif',
			'type' => 'image/gif',
		);
		
		// directories (used by HTML functions)
		$this->template['js_dir'] = $this->config['js_dir'];
		$this->template['css_dir'] = $this->config['css_dir'];
		
		// scripts
		$this->template['scripts'][] = $this->config['jquery'];
		$this->template['scripts'][] = $this->config['main_js'];
		
		// styles
		$this->template['styles'][] = array(
			'href'  => $this->config['main_css'],
			'media' => 'screen',
			'title' => 'main styles'
		);
	}
	
	public function after()
	{
		$this->response->body(
			$this->fv->build( 'templates/html5', $this->template )
		);
		parent::after();
	}
	
}