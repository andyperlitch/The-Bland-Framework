<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Exception class for Database class
 *
 * @package Database
 * @author Andrew Perlitch
 */
class DBException extends Exception{ }

/**
 * Database access class (extends mysqli).
 *
 * @package Database
 * @author Andrew Perlitch
 */
class DB extends mysqli{
	/**
	 * The main config object (passed by factory)
	 *
	 * @var Config
	 */
	protected $config;
	/**
	 * Holds result from unprepared queries
	 *
	 * @var mixed (mysqli_result or bool)
	 */
	protected $result;
	/**
	 * Array of values to not parameterize/escape
	 *
	 * @var array
	 */
	protected $no_quotes = array("NOW()","NULL","CURDATE()");
	/**
	 * Array of acceptable operators in WHERE clauses
	 *
	 * @var array
	 */
	protected $operators = array("=","!=",">",">=","<=","<", "IS","IS NOT","REGEXP");
	/**
	 * Array of acceptable JOIN types
	 *
	 * @var array
	 */
	protected $join_types = array("LEFT", "RIGHT", "INNER", "OUTER","");
	
	/**
	 * Constructor
	 *
	 * @param Config $c 
	 * @author Andrew Perlitch
	 */
	function __construct(Config $c)
	{
		// store config file
		$this->config = $c;

		// do PDO constructor
		parent::__construct(
		$this->config['db_host'],
		$this->config['db_user'],
		$this->config['db_pass'],
		$this->config['db_dbname']
		);
	}
	
	/**
	 * Performs a simple unprepared query
	 *
	 * @param string $query    SQL statement to send
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function q($query)
	{
		$this->result = $this->query($query);
	}
	
	/**
	 * Prepares and executes a prepared select statement, returns an array of results.
	 * Note: if $limit is set to 1, will return array of row, 
	 * otherwise will return multidimensional array of results.
	 *
	 * @param string $table_name 
	 * @param array $fields 
	 * @param array $joins 
	 * @param array $where 
	 * @param array $order 
	 * @param int $offset 
	 * @param int $limit 
	 * @return array
	 * @author Andrew Perlitch
	 */
	public function sel(
		$table_name, 
		array $fields = array(), 
		array $joins = array(), 
		array $where = array(), 
		array $order = array(), 
		$offset = NULL, 
		$limit = NULL 
	)
	{
		// vars
		$query = "SELECT ";
		$params = array();
		$paramReferences = array();
		$paramTypes = "";
		$bindParamsMethod = new ReflectionMethod('mysqli_stmt','bind_param');
		$whereWord = "WHERE";
		
		// fields clause
		if ( empty($fields) ) $query .= "*";
		else {
			foreach ($fields as $field) {
				// check if array
				if ( is_array($field) ) {
					$elems = count($field);
					switch ($elems) {
						// one elem, check if string
						case 1:
							if ( is_string($field[0]) ) $query .= "`{$field[0]}`, ";
							else throw new DBException("Wrong format for fields param");
						break;
						// two elems, field AS alias
						case 2:
							$query .= "`{$field[0]}` AS `{$field[1]}`, ";
						break;
						// three elems, table.field AS alias
						case 3:
							$query .= "`{$field[0]}`.`{$field[1]}` AS `{$field[2]}`, ";
						break;
						// issue with array
						default:
							throw new DBException("Too many (or zero) elements in (one or more of the) fields arrays");
						break;
					}
				}
				// check to ensure string
				elseif ( is_string($field) ) $query .= "`{$field}`, ";
				// throw exc
				else throw new DBException("Invalid \$field value. Must be string or array");
			}
		}
		
		// add from clause
		$query = rtrim($query,', ');
		$query .= " FROM `$table_name`";
		
		// do joins, if there
		if ( !empty($joins) ) {
			
			if (is_array($joins[0])) {
				foreach ($joins as $join) {
					$this->buildJoinClause($join, $query, $table_name );
				}
			} else {
				$this->buildJoinClause($joins, $query, $table_name );
			}
			
		}
		
		// check for where clauses
		if ( ! empty($where) ) {
			$this->buildWhereClauses( $where, $whereWord, $query, $params, $paramTypes);
		}
		
		// check order
		// format of order: array(string table, string field, bool asc)
		//              or: array( array(string table, string field, bool asc), ... )
		if ( !empty($order) ) {
			// start order clause
			$query .= " ORDER BY ";
			// check if multiple order statements
			if ( is_array($order[0]) ) {
				// loop through order statement array
				foreach ($order as $ordItem) {
					$this->buildOrderClause($query, $ordItem);
				}
			} else {
				// do single order statement
				$this->buildOrderClause($query, $order);
			}
			// take out column at end
			$query = rtrim($query,',');
		}

		// check if offset & limit was supplied
		if ( $limit !== NULL && $offset !== NULL ) {
			$offset = (int) $offset;
			$limit = (int) $limit;
			if ( $limit > 0 ) $query .= " LIMIT $offset, $limit";
		}
		
		// add semicolon
		$query .= ";";
		
		// prep statement
		if ( !($stmt = $this->prep($query)) ) throw new DBException("failed to prepare SELECT query. \$query: \"$query\"");
		
		// fill reference array
		foreach($params as $key => $value){
			$paramReferences[$key] = &$params[$key];  
		}

		// prepend paramTypes to array
		array_unshift($paramReferences,$paramTypes);
		
		if ( !empty($params) ) {
			try {
				// call bind_param
				$bindParamsMethod->invokeArgs($stmt,$paramReferences);
			} catch (Exception $e) {
				throw new DBException("Likely an error in query: ".$this->error.", or problem with bind_param: {$e->getMessage()}");
			}
		}
		
		// execute statement
		$this->exec($stmt);
		
		if ( $limit === 1 ) return $this->mfa($stmt);
		else return $this->mfa2($stmt);
		
	}
	
	/**
	 * Prepares and executes INSERT statement.
	 *
	 * @param string $table_name   Name of table to insert into
	 * @param array $data          Data to insert ('field' => 'value')
	 * @param bool $multi          Whether or not to use multi-insert syntax (VALUES (..),(..),(..),...)
	 * @return int                 Returns last inserted id, or 0 on failure
	 * @author Andrew Perlitch
	 */
	public function ins($table_name, array $data, $multi = false)
	{
		// check that data not empty
		if ( empty($data) ) throw new DBException("\$data array must not be empty");

		// check if multiple that data is acceptable
		if ( $multi && !isset($data[0]) ) throw new DBException("Value array not formatted, possible multi = true issue.");

		// vars
		$query = "INSERT INTO `$table_name` ";                                   // start query
		$fields = "(";                                                           // field clause string
		$values = "(";                                                           // value clause string
		$params = array();                                                       // array to store values
		$paramReferences = array();                                              // array to store references to those values
		$paramTypes = "";                                                        // string to hold param types
		$first = $multi ? $data[0] : $data ;                                     // set first (or only) data array
		$bindParamsMethod = new ReflectionMethod('mysqli_stmt','bind_param');    // set reflection method to call later

		// loop through first (or only row) to insert
		foreach ($first as $key => $val) {
			// set field
			$fields .= "`".$key."`, ";
			// check for special value
			if ( in_array($val,$this->no_quotes) ) {
				$values .= "$val, ";
				continue;
			}
			// otherwise parameterize
			$values .= "?, ";
			// add value to params
			$params[] = $val;
			// auto-detect paramType
			$paramTypes .= $this->detectParamType($val, $key);
		}
		$fields = rtrim($fields,', ') . ")";
		$values = rtrim($values,', ') . ")";

		// check for multiples
		if ($multi) {

			$i = 0;
			foreach ($data as $row) {
				// don't add first row (already added above)
				$i++;
				if ($i === 1) continue;

				// build on $values string
				$values .= ", (";
				foreach ($row as $key => $val) {
					// check for special value
					if ( in_array($val,$this->no_quotes) ) {
						$values .= "$val, ";
						continue;
					}
					// otherwise add param
					$values .= "?, ";
					$params[] = $val;
					$paramTypes .= $this->detectParamType($val, $key);
				}
				$values = rtrim($values,', ') . ")"; 
			}
		}

		// concat statement
		$query .= $fields . " VALUES " . $values . ";";

		// prepare query
		if ( ! ($stmt = $this->prep($query)) ) throw new DBException("Could not prepare INSERT query.");

		// fill reference array
		foreach($params as $key => $value){
			$paramReferences[$key] = &$params[$key];  
		}

		// prepend paramTypes to array
		array_unshift($paramReferences,$paramTypes);
		
		try {
			// call bind_param
			$bindParamsMethod->invokeArgs($stmt,$paramReferences);
		} catch (Exception $e) {
			throw new DBException("Likely an error in query: ".$this->error);
		}

		// execute statement
		$this->exec($stmt);

		// get insert id (using procedural approach b/c issue with larger ints with OOP: http://php.net/manual/en/mysqli.insert-id.php#usernotes)
		$return_id =  mysqli_insert_id($this);

		// check if valid result
		if ( $return_id === 0 ) throw new DBException("Error in INSERT statement. \$query=\"$query\", err:'{$this->error}'");

		// return id
		return $return_id;

	}

	/**
	 * Prepares and executes an UPDATE statement
	 *
	 * @param string $table_name    Name of table to update
	 * @param array $data           Data to update ('field' => 'value')
	 * @param array $where          Array of one or several WHERE clauses [array(] array("field(no outside backticks)", "operator","value") [)]
	 * @param int $limit         Limit number to be updated
	 * @return bool
	 * @author Andrew Perlitch
	 */
	public function upd($table_name, array $data, array $where = array(), $limit = NULL )
	{
		// check that data not empty
		if ( empty($data) ) throw new DBException("\$data array must not be empty");

		// vars
		$query = "UPDATE `$table_name` SET ";
		$params = array();                                                       // array to store values
		$paramReferences = array();                                              // array to store references to those values
		$paramTypes = "";                                                        // string to hold param types
		$bindParamsMethod = new ReflectionMethod('mysqli_stmt','bind_param');    // set reflection method to call later
		$whereWord = "WHERE";
		
		// write field setting
		foreach ($data as $field => $value) {

			// get delimiter
			if ( in_array($value,$this->no_quotes) ) {
				$query .= "`$field` = $value, ";
				continue;
			}
			// add param to query
			$query .= "`$field` = ?, ";
			// add value to params
			$params[] = $value;
			// auto-detect paramType
			$paramTypes .= $this->detectParamType($value, $field);

		}
		// trim query of trailing comma
		$query = rtrim($query,', ');

		// check for where clauses
		if ( ! empty($where) ) {
			$this->buildWhereClauses( $where, $whereWord, $query, $params, $paramTypes);
		}
		
		// check if limit was supplied
		if ( $limit !== NULL ) {
			$limit = (int) $limit;
			if ( $limit > 0 ) $query .= " LIMIT $limit";
		}
		
		// add semicolon
		$query .= ";";
		
		// prep statement
		if ( !($stmt = $this->prep($query)) ) throw new DBException("failed to prepare UPDATE query. \$query: \"$query\"");
		
		// fill reference array
		foreach($params as $key => $value){
			$paramReferences[$key] = &$params[$key];  
		}

		// prepend paramTypes to array
		array_unshift($paramReferences,$paramTypes);
		
		try {
			// call bind_param
			$bindParamsMethod->invokeArgs($stmt,$paramReferences);
		} catch (Exception $e) {
			throw new DBException("Likely an error in query: ".$this->error);
		}
		
		// return true
		return true;

	}
	
	/**
	 * Prepares and executes DELETE statement
	 *
	 * @param string $table_name     Name of table to update
	 * @param array $where           Array of one or several WHERE clauses (see above or below for format)
	 * @param int $limit             Limit number to be deleted
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function del($table_name, array $where = array(), $limit = NULL)
	{
		// vars
		$query = "DELETE FROM `$table_name`";
		$params = array();                                                       // array to store values
		$paramReferences = array();                                              // array to store references to those values
		$paramTypes = "";                                                        // string to hold param types
		$bindParamsMethod = new ReflectionMethod('mysqli_stmt','bind_param');    // set reflection method to call later		
		$whereWord = "WHERE";                                                    // where clause word to use (changed dynamically)
		
		// check for where clause
		if ( !empty($where) ) {
			// build where clause
			$this->buildWhereClauses( $where, $whereWord, $query, $params, $paramTypes);
		}
		
		// limit
		if ( $limit !== NULL ) {
			$limit = (int) $limit;
			if ( $limit > 0 ) $query .= " LIMIT $limit";
		}
		
		// add semicolon
		$query .= ";";
		
		// prepare query
		if ( ! ($stmt = $this->prep($query)) ) throw new DBException("Could not prepare DELETE query.");

		// fill reference array
		foreach($params as $key => $value){
			$paramReferences[$key] = &$params[$key];  
		}

		// prepend paramTypes to array
		array_unshift($paramReferences,$paramTypes);
		
		try {
			// call bind_param
			$bindParamsMethod->invokeArgs($stmt,$paramReferences);
		} catch (Exception $e) {
			throw new DBException("Likely an error in query: ".$this->error);
		}
		
		return true;
		
	}
	
	/**
	 * Detects best fit param type for use while preparing params for mysqli::bind_param
	 *
	 * @param mixed $val       Value to be eval'd
	 * @param string $key      Key of the dataset that is being eval'd (for use in exception message)
	 * @return string
	 * @author Andrew Perlitch
	 */
	protected function detectParamType($val, $key)
	{
		// auto-detect paramType
		if ( is_string($val) ) return "s";
		elseif ( is_float($val) ) return "d";
		elseif ( is_integer($val) ) return "i";
		else throw new DBException("Bad type of value given for: `$key` field. ");
	}

	/**
	 * Escape a value using mysqli::real_escape_string
	 *
	 * @param string $value    Value to escape
	 * @return string
	 * @author Andrew Perlitch
	 */
	public function esc($value)
	{
		return $this->real_escape_string($value);
	}

	/**
	 * Prepares a statement. Returns extended class of mysqli_stmt.
	 *
	 * @param string $query 
	 * @return mysqli_stmt_extended
	 * @author Andrew Perlitch
	 */
	public function prep($query)
	{
		return new mysqli_stmt_extended($this, $query);
	}

	/**
	 * Executes a mysqli_stmt_extended object.
	 *
	 * @param mysqli_stmt_extended $stmt 
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function exec(mysqli_stmt_extended $stmt)
	{
		// execute
		$stmt->execute();

		// store result
		$stmt->store_result();

		// check for error
		if ( $this->affected_rows === -1 ) {
			throw new DBException("Error in query: '".$this->error."'");
		}

	}

	/**
	 * Returns array of first result row.
	 * Note: if no statement provided, will look to $this->result.
	 * 
	 * @param mysqli_stmt_extended $stmt 
	 * @return array
	 * @author Andrew Perlitch
	 */
	public function mfa(mysqli_stmt_extended $stmt = NULL)
	{
		$response;
		if ( $stmt !== NULL && $stmt instanceof mysqli_stmt_extended) {
			// get associative array
			$response = $stmt->fetch_assoc();
		} elseif($this->result instanceof mysqli_result) {
			// get associative array
			$response = $this->result->fetch_assoc();
		} else {
			throw new DBException("No query made or no prepared statement passed");
		}
		return $response;
	}

	/**
	 * Returns multi-dimensional array of all results.
	 * Note: if no stmt provided, will look to $this->result
	 *
	 * @param string $stmt 
	 * @return array
	 * @author Andrew Perlitch
	 */
	public function mfa2($stmt = NULL)
	{
		$response = array();
		if ( $stmt !== NULL && $stmt instanceof mysqli_stmt_extended) {
			// get associative array
			while ( $row = $stmt->fetch_assoc() ) {
				$response[] = $row;
			}
		} elseif($this->result instanceof mysqli_result) {
			// get associative array
			while ( $row = $this->result->fetch_assoc() ) {
				$response[] = $row;
			}
		} else {
			throw new DBException("No query made or prepared statement passed");
		}
		return $response;
	}

	/**
	 * Clears out $this->result.
	 *
	 * @return void
	 * @author Andrew Perlitch
	 */
	public function clear()
	{
		$this->result = NULL;
	}
	
	/**
	 * Builds join clauses for DB::sel().
	 *
	 * @param array $join         Array of join clause options
	 * @param string $query       Current query to build on (passed by reference)
	 * @param string $table_name  Table name being joined to
	 * @return void
	 * @author Andrew Perlitch
	 */
	protected function buildJoinClause(array $join, &$query, $table_name )
	{
		// format of join: array(TABLENAME, FIELD_MAIN_TBL, FIELD_JOINING_TBL)
		//             or: array(TABLENAME, FIELD_BOTH_TBLS)
		//             or: array(TABLENAME, FIELD_MAIN_TBL, FIELD_JOINING_TBL, TYPE_OF_JOIN)
		$elems = count($join);
		switch ($elems) {
			case 4:
				// check join type
				if ( !in_array($join[3], $this->join_types) ) 
					throw new DBException("4th elem in join array does not have valid join type:\$join[3] = '{$join[3]}'");
					
				// add to query
				$query .= " {$join[3]} JOIN `{$join[0]}` ON `$table_name`.`{$join[1]}` = `{$join[0]}`.`{$join[2]}`";
			break;
			case 3:
				$query .= " LEFT JOIN `{$join[0]}` ON `$table_name`.`{$join[1]}` = `{$join[0]}`.`{$join[2]}`";
			break;
			case 2:
				$query .= " LEFT JOIN `{$join[0]}` ON `$table_name`.`{$join[1]}` = `{$join[0]}`.`{$join[1]}`";
			break;
			default:
				throw new DBException("\$join array has too many or too few elements");
			break;
		}
	}
	
	/**
	 * Builds where clauses for DB::sel() and DB::del() and DB::upd().
	 *
	 * @param array $where          Array of where clauses
	 * @param string $whereWord     Word to precede each clause (WHERE or AND)
	 * @param string $query         Query to build on (passed by reference).
	 * @param array $params         Params to add to for prep.
	 * @param string $paramTypes    Param Type string for bind_param to add.
	 * @return void
	 * @author Andrew Perlitch
	 */
	protected function buildWhereClauses(array $where, &$whereWord, &$query, array &$params, &$paramTypes)
	{
		// check if where clause is empty
		if ( empty($where) ) return;
		// check if multiple clauses
		if ( is_array($where[0]) ) {
			// loop through
			foreach ($where as $arr) {
				// build individual where clause
				$this->buildWhereClause($arr, $whereWord, $query, $params, $paramTypes);
			}
		} else {
			$this->buildWhereClause($where, $whereWord, $query, $params, $paramTypes);
		}
		
	}
	
	/**
	 * Builds individual where clause.
	 *
	 * @param array $where         Single where clause (in array form: array(field,operator,value))
	 * @param string $whereWord    Word to precede each clause
	 * @param string $query        Query to build on (passed by reference)
	 * @param array $params         Params to add to for prep.
	 * @param string $paramTypes    Param Type string for bind_param to add.
	 * @return void
	 * @author Andrew Perlitch
	 */
	protected function buildWhereClause(array $where, &$whereWord, &$query, array &$params, &$paramTypes)
	{
		// check if valid
		if ( !isset($where[1]) || !in_array($where[1],$this->operators) ) {
			throw new DBException("A clause array in the \$where array was not in the correct format or did not have a valid operator. (Expected Structure: array[ array[FIELD,OPERATOR,VALUE], array[FIELD,OPERATOR,VALUE] , ... ])");
		}

		// check for special value
		if ( in_array($where[2], $this->no_quotes) ) {
			// append to query
			$query .= " $whereWord `{$where[0]}` {$where[1]} {$where[2]}";
			// change whereword, continue
			if ( $whereWord == "WHERE" ) $whereWord = "AND";
			continue;
		}

		// parameterize
		$query .= " $whereWord `{$where[0]}` {$where[1]} ?";

		// add value to params
		$params[] = $where[2];

		// auto-detect paramType
		$paramTypes .= $this->detectParamType($where[2], $where[0]);

		// change where word
		if ( $whereWord == "WHERE" ) $whereWord = "AND";
	}
	
	/**
	 * Builds order clause.
	 *
	 * @param string $query 
	 * @param string $order 
	 * @return void
	 * @author Andrew Perlitch
	 */
	protected function buildOrderClause(&$query, $order)
	{
		if ( count($order) !== 3 ) throw new DBException("Order array passed to buildOrderClause was not in the correct format (3 elements)");
		$query .= "`{$order[0]}`.`{$order[1]}` ";
		$query .= $order[2] ? "ASC," : "DESC,";
	}
	
	/**
	 * Destructor: closes connection
	 *
	 * @return void
	 * @author Andrew Perlitch
	 */
	function __destruct()
	{
		$this->close();
	}
}

/**
 * Extended mysqli_stmt class. 
 *
 * @package Database
 * @author php at johnbaldock dot co dot uk
 * @link http://php.net/manual/en/mysqli-stmt.fetch.php#usernotes
 */
class mysqli_stmt_extended extends mysqli_stmt {
	/**
	 * Flag for determining if vars have been bound.
	 *
	 * @var bool
	 */
	protected $varsBound = false;
	
	/**
	 * Stores results.
	 *
	 * @var unsure
	 */
	protected $results;

	public function fetch_assoc()
	{
		// checks to see if the variables have been bound, this is so that when
		//  using a while ($row = $this->stmt->fetch_assoc()) loop the following
		// code is only executed the first time
		if (!$this->varsBound) {
			$meta = $this->result_metadata();
			while ($column = $meta->fetch_field()) {
				// this is to stop a syntax error if a column name has a space in
				// e.g. "This Column". 'Typer85 at gmail dot com' pointed this out
				$columnName = str_replace(' ', '_', $column->name);
				$bindVarArray[] = &$this->results[$columnName];
			}
			call_user_func_array(array($this, 'bind_result'), $bindVarArray);
			$this->varsBound = true;
		}

		if ($this->fetch() != null) {
			// this is a hack. The problem is that the array $this->results is full
			// of references not actual data, therefore when doing the following:
			// while ($row = $this->stmt->fetch_assoc()) {
				// $results[] = $row;
				// }
				// $results[0], $results[1], etc, were all references and pointed to
				// the last dataset
				foreach ($this->results as $k => $v) {
					$results[$k] = $v;
				}
				return $results;
			} else {
				return null;
			}
		}

	}