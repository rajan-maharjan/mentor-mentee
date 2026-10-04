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
		if(!$this->connection = $GLOBALS["___mysqli_ston"])
			echo ('<font color="red">System error: "could not make connection. Either server or/and username or/and password is invalid."</font>');
		else{
			if(!mysqli_select_db($GLOBALS["___mysqli_ston"], $this->database)){				
				echo ('<font color="red">System error: "could not find database. </font>');
			}
		}
		
	}
	
	function fetch(){		
		return @mysqli_fetch_object($this->result);
	}	
	
	
	function execute(){	
		$this->result=mysqli_query($GLOBALS["___mysqli_ston"], $this->sql);	
		if($this->result)
			return $this->result;
		else
			return mysqli_error($GLOBALS["___mysqli_ston"]);
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