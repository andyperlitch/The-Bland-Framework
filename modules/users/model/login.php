<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Class to handle login action
 *
 * @package Login
 * @author Andy Perlitch
 */
class Model_Login extends Model{
	
	/**
	 * Session object
	 *
	 * @var Session
	 */
	protected $session;
	
	function __construct(Session $session, DB $db, Config $config)
	{
		// store session object
		$this->session = $session;
		
		// parent constructor
		parent::__construct($db,$config);
	}
	
	/**
	 * Takes post array and tests against database for valid user.
	 *
	 * @param array $post              $_POST array
	 * @param Validator $validator     Validator object used to do validation
	 * @param array $errors            Array of errors for form elements
	 * @return void
	 * @author Andy Perlitch
	 */
	public function doLogin( array $post, Validator $validator, array &$errors )
	{
		// data arr
		$data = array();
		
		// init validations
		$validator
			->v(
				'username', 
				array(
					'notEqual' => array('',"Please provide a username."),
				),
				$data,
				$errors
			)
			->v(
				'password', 
				array(
					'regex' => array('/[a-z0-9]{64}/',"An error occurred."),
					'notEqual' => array('e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',"Please provide a password."),
				),
				$data,
				$errors
			);
		
		// check errors
		if ( empty($errors) ) {
			
			// do db call
			$user = $this->db->sel( 
			    array('user_id','email','username','logins','last_login','cookie'), 
			    "users", 
			    array(), 
			    array(
					array( "username", "=", $data['username'] ),
					array( "password", "=", $data['password'] ),
				),
				array(),
				0,
				1
			);
			
			// check if a user exists with that name and password
			if ( empty($user) ) {
				$errors['username'] = "That username/password combination was not found.";
			} else {
				// user is authenticated, set session variables
				return $this->setSessionVars($user);
			}
			
		}
		
	}
	
	/**
	 * Upon successful db look-up, sets session variables for logged in user.
	 * Also updates last_logged and logins fields for users.
	 *
	 * @param array $user           
	 * @return void
	 * @author Heather Perlitch
	 */
	private function setSessionVars(array $user)
	{
		// set roles
		$user["roles"] = $this->getRoles($user['user_id']);
		
		// set user session args
		$this->session["user"] = $user;
		
		// update database to reflect login
		$update = $this->db->upd(
			"users",
			array( 'logins' => $user['logins'] + 1, 'last_login' => 'NOW()' ),
			array( "user_id" , "=" , $user['user_id'] )
		);
		
		// check that update succeeded
		return $update;
		
	}
	
	/**
	 * Retrieves roles of a user based on user_id
	 *
	 * @param int $user_id         id of user
	 * @return array
	 * @author Andrew Perlitch
	 */
	private function getRoles($user_id)
	{
		// set roles array
		$roles = array();
		
		// get init selection
		$sel = $this->db->sel( 
		    array('role_name'), 
		    "roles_users", 
		    array( "roles" , "role_id" ), 
		    array( "user_id" , "=" , $user_id ) 
		);
		
		// loop through selection, fill roles array
		foreach ($sel as $el) $roles[] = $el['role_name'];
		
		return $roles;
	}
	
}

?>