<?php 
require_once PHPUNIT_PATH.'Framework/TestCase.php';
include SYSPATH.'classes/db.php';
class DBTest extends PHPUnit_Framework_TestCase {
	
	private $db;
	private $insertedId;
	public function setUp()
	{
		// var_dump(class_exists("Config"));
		$config = new Config("phpunit");
		$this->db = new DB($config);
	}
	
	public function tearDown()
	{
		$this->db = null;
	}
	
	public function testInstance()
	{
		$this->assertInstanceOf('mysqli',$this->db , 'Should be instance of mysqli object');
	}
	
	public function testPrep()
	{
		$stmt = $this->db->prep("SELECT `id`,`name` FROM `test_table` WHERE `id` > ?");
		$this->assertInstanceOf('mysqli_stmt_extended',$stmt , '$stmt should be an instance of mysqli_stmt_extended');
	}
	
	public function testExec()
	{
		$stmt = $this->db->prep("SELECT `id`,`name` FROM `test_table`");
		$this->db->exec($stmt);
		$this->assertTrue($this->db->affected_rows > 0 , 'Should have affected more than zero rows');
	}
	
	public function testBadExec()
	{
		try {
			$stmt = $this->db->prep("SELECT `id`,`name` FROM `a_nonexistent_table`");
			$this->db->exec($stmt);
		} catch (DBException $e) {
			return;
		}
		$this->fail("Should have thrown a DBException");
	}
	
	public function testMfa()
	{
		// prepared statement
		$stmt = $this->db->prep("SELECT `id`,`name` FROM `test_table`");
		// execute query
		$this->db->exec($stmt);
		// get result
		$result = $this->db->mfa($stmt);
		// test result
		$this->assertTrue(
			is_array($result) 
			&& !empty($result) 
			&& array_key_exists('name',$result) 
			&& array_key_exists('id',$result),
			'mfa() should return a non-empty array'
		);
		
		// straight query dog
		$this->db->q("SELECT `id`,`name` FROM `test_table`");
		$result2 = $this->db->mfa();
		// test result
		$this->assertTrue(
			is_array($result2) 
			&& !empty($result2) 
			&& array_key_exists('name',$result2) 
			&& array_key_exists('id',$result2),
			'mfa() should return a non-empty array'
		);
		$this->db->clear();
	}
	
	public function testMfa2()
	{
		// prepared statement
		$stmt = $this->db->prep("SELECT `id`,`name` FROM `test_table`");
		// execute query
		$this->db->exec($stmt);
		// get results
		$results = $this->db->mfa2($stmt);
		$result = $results[0];
		// test result
		$this->assertTrue(
			is_array($result) 
			&& !empty($result) 
			&& array_key_exists('name',$result) 
			&& array_key_exists('id',$result),
			'mfa() should return a non-empty array'
		);
		
		// straight query dog
		$this->db->q("SELECT `id`,`name` FROM `test_table`");
		$results2 = $this->db->mfa2();
		$result2 = $results2[0];
		// test result
		$this->assertTrue(
			is_array($result2) 
			&& !empty($result2) 
			&& array_key_exists('name',$result2) 
			&& array_key_exists('id',$result2),
			'mfa() should return a non-empty array'
		);
		$this->db->clear();
	}
	
	public function testBadMfa()
	{
		try {
			$this->db->mfa();
		} catch (DBException $e) {
			try {
				$this->db->mfa2();
			} catch (DBException $e) {
				return;
			}
		}
		$this->fail("A DBException should have been thrown");
	}
	
	
	public function testIns()
	{
		$this->insertedId = $this->db->ins(
			'test_table',
			array(
				'id' => 'NULL',
				'name' => 'phpunit',
				'type_id' => 7,
			)
		);
		$this->assertTrue(is_numeric($this->insertedId) && $this->insertedId > 0 , 'insertedId should be a number');
	}
	
	public function testInsMult()
	{
		$first_id = $this->db->ins(
			'test_table',
			array(
				array('id' => 'NULL','name' => 'phpunit2','type_id' => 7),
				array('id' => 'NULL','name' => 'phpunit3','type_id' => 7),
				array('id' => 'NULL','name' => 'phpunit4','type_id' => 7),
			),
			true
		);
		$this->assertTrue(is_numeric($first_id) && $first_id > 0 , 'insertedId should be a number');
	}
	
	public function testBadIns()
	{
		try {
			// bad table name
			$this->db->ins(
				'not_a_table',
				array(
					'id' => 'NULL',
					'name' => 'badname',
					'type_id' => 7
				)
			);
		} catch (DBException $e) {
			return;
		}
		$this->fail("Should throw a DBException");
		
		
		
	}
	
	public function testBadInsMult()
	{
		try {
			$this->db->ins(
				'not_a_table',
				array(
					'id' => 'NULL',
					'name' => 'badname',
					'type_id' => 7
				),
				true
			);
		} catch (DBException $e) {
			return;
		}
		$this->fail("Should throw a DBException");
	}
	
	public function testUpd()
	{
		
		$update = $this->db->upd(
			'test_table',
			array('type_id' => 8),
			array(
				array("type_id", "=", 7),
			)
		);
		
		$this->assertTrue($update , 'Update should return true.');
		
	}
	
	public function testDel()
	{
		$delete = $this->db->del(
			'test_table',
			array(
				array("type_id","=",8),
			)
		);
		
		$this->assertTrue($delete , 'Delete should return true');
	}
	
	public function testBadDel()
	{
		try {
			$this->db->del(
				'not_a_table',   // bad table name
				array(
					array("id",">",10)
				)
			);
		} catch (DBException $e) {
			return;
		}
		$this->fail("Should have thrown a DBException");
	}
	
	public function testSel1()
	{
		$result = $this->db->sel(array(), 'test_table' );
		$this->assertTrue(is_array($result) , 'result should be an array');
	}
	
	public function testSel2()
	{
		$result = $this->db->sel(
			array('name','type'),                              // field names
			'test_table',                                      // table name
			array("test_join_table", "type_id", "id"),         // joins
			array('name','REGEXP','a'),                        // where clauses
			array('test_table', 'type_id', false),             // order clause
			2,                                                 // offset
			10                                                 // limit
		);
		$this->assertTrue(is_array($result) , 'SELECT statement failed. Should return an array');
	}
	
	public function testBadSel()
	{
		try {
			$this->db->sel(
				array(),
				'not_a_table'
			);
		} catch (DBException $e) {
			return;
		}
		$this->fail("Should have thrown a DBException");
	}
	
}