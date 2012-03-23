<?php defined('SYSPATH') or die('No direct script access.');
class Email {
	public $message;
	public $fromName;
	public $fromMail;
	public $toMail;
	public $subject;
	
	public function SendMail($html=false){
		$headers = "";
		if($html){
			$headers .= "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
		} else {
			$message = stripslashes($this->message);
		}
		$headers .="From: ".$this->fromName."<".$this->fromMail.">\r\n";
		$headers .="Reply-To: ".$this->fromName."<".$this->fromMail.">\r\n";
		$headers .="X-Mailer: PHP/" . phpversion();
		$headers .="Origin: ".$_SERVER['REMOTE_ADDR']."\r\n";
		
		return mail($this->toMail, $this->subject, $this->message, $headers);
	}
}

?>