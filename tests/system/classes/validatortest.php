<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/validator.php';
class ValidatorTest extends PHPUnit_Framework_TestCase {
	
	private $validator;
	private $post;
	
	public function setUp()
	{
		$this->post = array(
			'empty_string'   => '',
			'test_regex'     => '456_abc',
			'test_regex2'    => '123_def',
			'positive_int'   => '43',
			'negative_int'   => '-5',
			'positive_float' => '34.592',
			'negative_float' => '-239.93',
			'zero'           => '0',
			'good_name'      => 'Andy Perlitch',
			'bad_name'       => '*#)@(#)',
			'great_email'    => 'andy@andyperlitch.com',
			'good_email'     => 'andy@gmail',
			'bad_email'      => 'andy#alkdn.eok',
			'good_file'      => 'index.php',
			'bad_file'       => 'asldkfasndwlkeflwkenfklajwenlfkansdljkfnasldf.jpg',
			'f_date_double'  => '2020-10-18',
			'f_date_leading' => '2020-02-03',
			'f_date_single'  => '2020-3-4',
			'f_date_invalid' => '2019-18-94',
			'p_date_double'  => '2000-11-12',
			'p_date_leading' => '2000-01-04',
			'p_date_single'  => '2000-5-6',
			'p_date_invalid' => '1999-32-48',
		);
		
		$this->validator = new Validator( $this->post );
	}
	
	public function tearDown()
	{
		$this->validator = null;
	}
	
	public function testV_regex()
	{
		$data = array();
		$errors = array();
		$msg = 'bad regex';
		$rules = array(
			'regex' => array( '/^[\d]{3}_[a-z]{3}$/', $msg ),
		);
		
		// expecting data filled
		$this->validator
			->v( 'test_regex', $rules, $data, $errors )
			->v( 'test_regex2', $rules, $data, $errors );
			
		$this->assertEquals($this->post['test_regex'], $data['test_regex'], 'Data should equal post');
		$this->assertEquals($this->post['test_regex2'], $data['test_regex2'], 'Data should equal post');
		
		// expecting errors filled
		$this->validator
			->v( 'empty_string', $rules, $data, $errors )
			->v( 'zero', $rules, $data, $errors );
			
		$this->assertEquals($msg, $errors['empty_string'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['zero'], 'Errors should equal message');
	}
	
	public function testV_date()
	{
		// 'f_date_double'  => '2020-10-18',
		// 'f_date_leading' => '2020-02-03',
		// 'f_date_single'  => '2020-3-4',
		// 'f_date_invalid' => '2019-18-94',
		// 'p_date_double'  => '2000-11-12',
		// 'p_date_leading' => '2000-01-04',
		// 'p_date_single'  => '2000-5-6',
		// 'p_date_invalid' => '1999-32-48',
		$data = array();
		$errors = array();
		$msg = 'bad date';
		$rules_past = array( 'date' => array( 'past', $msg ), );
		$rules_future = array( 'date' => array( 'future', $msg ), );
		$rules_any = array( 'date' => array( 'any' , $msg ), );
		$this->validator
			// expecting data filled
			->v('f_date_double' , $rules_future, $data, $errors)
			->v('f_date_leading', $rules_future, $data, $errors)
			->v('f_date_single' , $rules_future, $data, $errors)
			->v('p_date_double' , $rules_past, $data, $errors)
			->v('p_date_leading', $rules_past, $data, $errors)
		    ->v('p_date_single' , $rules_past, $data, $errors);
		
		$this->assertEquals($this->post['f_date_double'], $data['f_date_double'], 'Data should equal post');
		$this->assertEquals($this->post['f_date_leading'], $data['f_date_leading'], 'Data should equal post');
		$this->assertEquals($this->post['f_date_single'], $data['f_date_single'], 'Data should equal post');
		$this->assertEquals($this->post['p_date_double'], $data['p_date_double'], 'Data should equal post');
		$this->assertEquals($this->post['p_date_leading'], $data['p_date_leading'], 'Data should equal post');
		$this->assertEquals($this->post['p_date_single'], $data['p_date_single'], 'Data should equal post');
		
		$data = array();
		$errors = array();
		
		$this->validator
			// expecting errors filled
			->v('f_date_double' , $rules_past, $data, $errors)
			->v('p_date_double' , $rules_future, $data, $errors)
			->v('f_date_invalid', $rules_any, $data, $errors)
		    ->v('p_date_invalid', $rules_any, $data, $errors)
			->v('empty_string', $rules_any, $data, $errors);
		
		$this->assertEquals($msg, $errors['f_date_double'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['p_date_double'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['f_date_invalid'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['p_date_invalid'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['empty_string'], 'Errors should equal message');
		
	}
	
	public function testV_name()
	{
		$data = array();
		$errors = array();
		$msg = 'bad name';
		$rules = array(
			'name' => array( '{2,100}', $msg ),
		);
		$rules_bad = array(
			'name' => array('2,100', $msg),
		);
		
		// expecting data filled
		$this->validator
			->v( 'good_name', $rules, $data, $errors )
			->v( 'test_regex2', $rules, $data, $errors );
			
		$this->assertEquals($this->post['good_name'], $data['good_name'], 'Data should equal post');
		$this->assertEquals($this->post['test_regex2'], $data['test_regex2'], 'Data should equal post');
		
		// reset
		$data = array();
		$errors = array();
		
		// expecting errors filled
		$this->validator
			->v( 'bad_name', $rules, $data, $errors )
			->v( 'empty_string', $rules, $data, $errors );
			
		$this->assertEquals($msg, $errors['bad_name'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['empty_string'], 'Errors should equal message');
		
		// bad rule, should throw exception
		try {
			$this->validator->v('good_name', $rules_bad, $errors, $data);
		} catch (ValidatorException $e) {
			return;
		}
		$this->fail("Expecting a ValidationException to be thrown.");
		
	}
	public function testV_int()
	{
		$data = array();
		$errors = array();
		$msg = 'bad int';
		
		
		$rules_positive = array(
			'int' => array( array('options' => array('min_range' => 1)), $msg ),
		);
		$rules_negative = array(
			'int' => array( array('options' => array('max_range' => -1)), $msg ),
		);
		$rules_any = array( 
			'int' => array( array() , $msg ),
		);
		
		// expecting data filled
		$this->validator
			->v( 'positive_int', $rules_positive, $data, $errors )
			->v( 'negative_int', $rules_negative, $data, $errors )
			->v( 'zero' , $rules_any, $data, $errors );
			
		$this->assertEquals( (int) $this->post['positive_int'], $data['positive_int'], 'Data should equal post');
		$this->assertEquals( (int) $this->post['negative_int'], $data['negative_int'], 'Data should equal post');
		$this->assertEquals( (int) $this->post['zero'], $data['zero'], 'Data should equal post');
		
		// reset
		$data = array();
		$errors = array();
		
		// expecting errors filled
		$this->validator
			->v( 'positive_int', $rules_negative, $data, $errors )
			->v( 'negative_int', $rules_positive, $data, $errors )
			->v( 'zero' , $rules_positive, $data, $errors )
			->v( 'empty_string', $rules_any, $data, $errors );
		
		$this->assertEquals($msg, $errors['positive_int'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['negative_int'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['zero'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['empty_string'], 'Errors should equal message');
	}

	public function testV_float()
	{
		$data = array();
		$errors = array();
		$msg = 'bad float';
		
		
		$rules_positive = array(
			'float' => array( array('options' => array('greater_than' => 0)), $msg ),
		);
		$rules_negative = array(
			'float' => array( array('options' => array('less_than' => 0)), $msg ),
		);
		$rules_any = array( 
			'float' => array( array() , $msg ),
		);
		
		// expecting data filled
		$this->validator
			->v( 'positive_float', $rules_positive, $data, $errors )
			->v( 'negative_float', $rules_negative, $data, $errors )
			->v( 'zero' , $rules_any, $data, $errors );
			
		$this->assertEquals( (float) $this->post['positive_float'], $data['positive_float'], 'Data should equal post');
		$this->assertEquals( (float) $this->post['negative_float'], $data['negative_float'], 'Data should equal post');
		$this->assertEquals( (float) $this->post['zero'], $data['zero'], 'Data should equal post');
		
		// reset
		$data = array();
		$errors = array();
		
		// expecting errors filled
		$this->validator
			->v( 'positive_float', $rules_negative, $data, $errors )
			->v( 'negative_float', $rules_positive, $data, $errors )
			->v( 'zero' , $rules_positive, $data, $errors )
			->v( 'empty_string', $rules_any, $data, $errors );
		
		$this->assertEquals($msg, $errors['positive_float'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['negative_float'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['zero'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['empty_string'], 'Errors should equal message');
	}

	public function testV_email()
	{
		
		$data = array();
		$errors = array();
		$msg = 'bad email';
		$rules = array(
			'email' => array('', $msg),
		);
		
		// expecting data filled
		$this->validator
			->v( 'great_email', $rules, $data, $errors );
			
		$this->assertEquals( $this->post['great_email'], $data['great_email'], 'Data should equal post');
		
		// reset
		$data = array();
		$errors = array();
		
		// expecting errors filled
		$this->validator
			->v( 'good_email', $rules, $data, $errors )
			->v( 'empty_string', $rules, $data, $errors );
		
		$this->assertEquals($msg, $errors['good_email'], 'Errors should equal message');
		$this->assertEquals($msg, $errors['empty_string'], 'Errors should equal message');
	}

	public function testV_file()
	{
		$data = array();
		$errors = array();
		$msg = 'bad file';
		$rules = array(
			'file' => array(DOCROOT, $msg),
		);
		$this->validator->v( 'good_file', $rules, $data, $errors );
		$this->assertEquals( $this->post['good_file'], $data['good_file'], 'Data should equal post.');
	
		$data = array();
		$errors = array();
		$this->validator->v( 'bad_file', $rules, $data, $errors );
		$this->assertEquals( $msg, $errors['bad_file'], "Errors should equal message");
		
	}

	public function testV_notEqual()
	{
		$data = array();
		$errors = array();
		$msg = 'not equal';
		$rules = array(
			'notEqual' => array('', $msg),
		);
		$this->validator->v('test_regex', $rules, $data, $errors );
		$this->assertEquals($this->post['test_regex'],$data['test_regex'] , 'Data should equal post.');
		
		$data = array();
		$errors = array();
		$this->validator->v('empty_string' , $rules, $data, $errors );
		$this->assertEquals( $msg,$errors['empty_string'] , 'Errors should equal message.');
	}
	
	public function testV_Equal()
	{
		$data = array();
		$errors = array();
		$msg = 'not equal';
		$rules = array(
			'equal' => array('', $msg),
		);
		$this->validator->v('empty_string', $rules, $data, $errors );
		$this->assertEquals($this->post['empty_string'],$data['empty_string'] , 'Data should equal post.');
		
		$data = array();
		$errors = array();
		$this->validator->v('test_regex' , $rules, $data, $errors );
		$this->assertEquals( $msg, $errors['test_regex'] , 'Errors should equal message.');
	}
}