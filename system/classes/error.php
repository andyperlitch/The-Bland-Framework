<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Error class for logging to the php error log.
 * 
 * 
 * @package Error
 */
class Error {
	
	/**
	 * Utilizes the error_log() method, formats entries for nice readability.
	 * Logs file, line, and priority of message
	 *
	 * @param string $message The message to print out
	 * @param string $file    File from whence it came (__FILE__)
	 * @param int $line       Line of file where logged (__LINE__)
	 * @param int $priority   Priority of error message
	 * @return void
	 */
	public static function log($message,$file,$line=0,$priority=1){
        
        $message = "
From $file, line $line:
$message
----------------------";
        
        switch($priority){
            case "1":
                error_log('----------------------
PROPER_NAME_OF_PROJECT DEBUGGING MESSAGE (1): 
----------------------'.$message);
            break;
            case "2":
                error_log('----------------------
PROPER_NAME_OF_PROJECT SYSTEM ERROR (2): 
----------------------'.$message);
            break;
            case "3":
                error_log('----------------------
PROPER_NAME_OF_PROJECT SYSTEM ERROR, URGENT (3): 
----------------------'.$message);
                $e = new Email;
                $e->fromName = "ERROR_NOTIFIER_NAME";
                $e->fromMail = "ERR_FROM_MAIL";
                $e->toMail = "ERR_TO_MAIL";
                $e->subject = "PROPER_NAME_OF_PROJECT SYSTEM ERROR, URGENT";
                $e->message = $message;
                // $e->SendMail(false);
            break;
        }
    }

	public static function exc(Exception $e , $file_caught , $line_caught , $priority = 2 ){

        $message = '
----------------------
!! EXCEPTION !!
----------------------
Caught in ' . $file_caught . ', line ' . $line_caught . ' : 
Message: ' . $e->getMessage() . '
Thrown in: '.$e->getFile() .' on line '.$e->getLine().'
Trace: '.print_r($e->getTrace(),true);
        $message .= '
----------------------';
        error_log($message);
    }	

	public static function dump($variable,$file,$line=0,$priority=1)
	{
		ob_start();
		var_dump($variable);
		$variable_dumped = ob_get_contents();
		ob_end_clean();
		self::log('Dumped variable: '.$variable_dumped,$file,$line,$priority);
	}
}