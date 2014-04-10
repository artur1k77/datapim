<?php

class database extends mysqli {

	private static $instance = null;
	
	private $dbHost = DB_HOST;
	private $dbUser = DB_USER;
	private $dbPwd = DB_PASS;
	private $dbName = DB_DATABASE;
	
	function __construct(){

		@parent::__construct( $this->dbHost, $this->dbUser, $this->dbPwd, $this->dbName);
	
		if(mysqli_connect_errno()) {
			utils::throwExcption(mysqli_connect_error(), mysqli_connect_errno());
		}
	
	}

	public static function getInstance(){
	
		if(self::$instance === null){
			$c = __CLASS__;
			self::$instance = new $c;
		}
	
		return self::$instance;
	
	}
	
	public function __clone(){
		utils::throwExcption('Cannot clone '.__CLASS__.' class');
	}

}