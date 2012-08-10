<?php defined('SYSPATH') or die('No direct script access.');
class Model_NotificationException extends Exception{}
class Model_Notification extends Model{
	
	protected $id;
	protected $ref;
	protected $title;
	protected $text;
	
	function __construct($ref, DB $db, Config $config)
	{
		// call parent construct
		parent::__construct($db,$config);
		
		// set fields to db record, if exists
		$this->setFields($ref);
	}
	
	public function writeOut()
	{
		// put title into h4, text into p
		$html = '<h4>'.Text::prep($this->title).'</h4>';
		$html .= '<p>'.Text::prep($this->text).'</p>';
		return $html;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getText()
	{
		return $this->text;
	}
	
	protected function setFields($ref)
	{
		// check db for record
		$results = $this->db->sel( 
		    array('id','ref','title','text'), 
		    "notifications", 
		    array(), 
		    array( "ref" , "=" , $ref ), 
		    array(), 
		    0, 
		    1 
		);
		
		// set fields
		if ($results) {
			$this->id = $results['id'];
			$this->ref = $results['ref'];
			$this->title = $results['title'];
			$this->text = $results['text'];
		} else {
			throw new Model_NotificationException("Record for '$ref' not found in notifications table.");
		}
	}
	
	
	
}

?>