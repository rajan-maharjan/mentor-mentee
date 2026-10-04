<?php
class connection {

	private $server;
	private $user;
	private $password;
	private $database;
	public $result;

	const CONNECTION_ERROR = 1;
	const SQL_EXECUTION_ERROR = 2;

	function __construct($server, $user, $password, $database)
		{
		$this->server 	= $server;
		$this->user 	= $user;
		$this->password = $password;
		$this->database = $database;
		$GLOBALS["___mysqli_ston"] = mysqli_connect($this->server,  $this->user,  $this->password);
		}

	function dbConnect(){
		if(!$this->connection = $GLOBALS["___mysqli_ston"]){
			error_log('DB connection error: could not connect to MySQL server.');
			echo ('<font color="red">System error: please contact the administrator.</font>');
		}
		else{
			if(!mysqli_select_db($GLOBALS["___mysqli_ston"], $this->database)){
				error_log('DB connection error: could not select database.');
				echo ('<font color="red">System error: please contact the administrator.</font>');
			}
		}

	}

	function fetch(){
		return @mysqli_fetch_object($this->result);
	}

	// Escapes a value for safe inclusion inside a single-quoted SQL literal.
	// Does NOT add the surrounding quotes - callers must still wrap the result in '...'.
	function escape($value){
		return mysqli_real_escape_string($GLOBALS["___mysqli_ston"], (string)$value);
	}

	function execute(){
		$this->result=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);
		if($this->result)
			return $this->result;
		else{
			// Never echo raw SQL/DB errors to the client - that leaks schema/query
			// structure and aids SQL injection. Log server-side only.
			error_log('SQL execution error: '.mysqli_error($GLOBALS["___mysqli_ston"]).' | Query: '.$this->sql);
			return false;
		}
	}

	function noOfRows(){
		return mysqli_num_rows($this->result);
	}

	function __destruct()
		{
		unset($this->user);
		unset($this->password);
		unset($this->database);
		unset($this->server);
		unset($this->result);
		}
}
?>