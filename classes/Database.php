<?php
	# PDO database class
	# connect to database
	# create prepared statements
	# bind values
	# return rows and results

	class Database {
		private $host = DB_HOST;
		private $user = DB_USER;
		private $pass = DB_PASS;
		private $name = DB_NAME;
		
		private $handler;
		private $stmt;
		private $error;
		private $proc;
		
		public function __construct() {
			# set DSN
			$dsn = "mysql:host=$this->host;dbname=$this->name";
			
			# PDO options array
			$options = [
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			];
			
			# create PDO instance
			try {
				$this->handler = new PDO($dsn, $this->user, $this->pass, $options);
			}
			catch (PDOException $e) {
				$this->error = $e->getMessage();
			}			
		}
		
		# prepare statement with query
		public function query($sql) {
			$this->stmt = $this->handler->prepare($sql);
			$this->proc = $sql;
		}
		
		# bind values to parameters
		public function bind($param, $value, $type=null) {
			if (is_null($type)) {
				switch(true) {
					case is_int($value):
						$type = PDO::PARAM_INT;
						break;
					case is_bool($value):
						$type = PDO::PARAM_BOOL;
						break;
					case is_null($value):
						$type = PDO::PARAM_NULL;
						break;
					default:
						$type = PDO::PARAM_STR;						
				}
			}
			
			$this->stmt->bindValue($param, $value, $type);
		}
		
		# bind multiple values to multiple parameters
		# $arr = [param => value], i.e., [":id" => 1];
		public function bindArray($arr) {
			foreach($arr as $key => $val) {
				$this->bind($key, $val);
			}
		}
		
		# execute the prepared statement
		public function execute() {
			try {
				$this->stmt->execute();
				return true;
			}
			catch (PDOException $e) {
				$this->error = "{$this->proc}, {$e->getMessage()}";
				return false;
			}
		}
		
		# execute the prepared statement and return id of inserted row
		public function executeAndGetId() {
			if ($this->execute()) {
				return $this->stmt->fetch(PDO::FETCH_OBJ);	
			}
			else {
				return false;
			}			
		}
		
		# get result set as array of objects
		public function resultSet() {			
			if ($this->execute()) {
				return $this->stmt->fetchAll(PDO::FETCH_OBJ);	
			}
			else {
				return false;
			}
		}
		
		# get single record as object
		public function single() {			
			if ($this->execute()) {
				return $this->stmt->fetch(PDO::FETCH_OBJ);	
			}
			else {
				return false;
			}
		}
		
		# get error message
		public function getError() {
			return $this->error;
		}
	}
?>