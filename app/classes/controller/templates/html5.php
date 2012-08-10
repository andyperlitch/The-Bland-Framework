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
		
		// if not ajax request, set up template stuff
		if ( ! $this->request->isAjax() )
		{
			// meta tags
			$this->template['meta_tags'] = array(
				// charset
				'charset' => array('charset' => 'utf-8'), 
				// google opt-out of odp
				'odp' => array( 'NAME' => 'ROBOTS', 'CONTENT' => 'NOODP'),
				// description
				'description' => array(
					'name' => 'description',
					'content' => 'The Bland Framework is a light-weight PHP framework, focused on testability, security, and ease-of-use'
				),
			);

			// favicon
			$this->template['favicon'] = array( 'href' => '/media/images/favicon.gif', 'type' => 'image/gif', );

			// directories (used by HTML functions)
			$this->template['js_dir'] = $this->config['js_dir'];
			$this->template['css_dir'] = $this->config['css_dir'];

			// styles
			$this->template['styles'][] = array(
				'href'  => $this->config['main_css'],
				'media' => 'screen',
				'title' => 'main styles'
			);
			
			// check for message in request params
			if ($this->request->param('notification')) {
				try {
					// get message model
					$msg = $this->fm->build("notification", array($this->request->param('notification')) );
					// set message to template
					$this->template['notification'] = $msg->writeOut();
				} catch (Exception $e) {
					// notification not recognized or problem with db
					Error::exc( $e , __FILE__ , __LINE__ );
				}
			}
		}
		
	}
	
	public function after()
	{
		if ( ! $this->request->isAjax() ) {
			$this->response->body(
				$this->fv->build( 'templates/html5', $this->template )
			);
		}
		parent::after();
	}
	
}