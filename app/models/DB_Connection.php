<?php

class DB_Connections extends PDO
{
	
	private $dbo;
	private $configFilePath;
	
	
	
	 public function __construct($dbNickname, $user, $pass) {
		 
		 $dscs = $this->getDSCS($dbNickname, $user, $pass); // include username and password in $dscs?
		 $db = new PDO($dcsc);	 
		 $this->dbo = $db;
		 $this->configFilePath = "config/db_connection_strings.txt";
	 }
	
	
	public function getDBO() {
		return $this->dbo;
	}
	
	// DSCS = database specific connection string
	// helper function to access the dscs from config file
	private function getDSCS($nickname) {
		$myfile = fopen($this->configFilePath, "r") or die("Unable to open file!");
		$dscs = "";
		$found = false;
		while(!feof($myfile)) {
		$fileLine = trim(fgets($myfile));
		// config file structure ->   nickname: dscs
		$arr = explode(":", $fileLine); // use colon to split the string
		// $arr[0] = nickname, $arr[1] = dcsc
			if(strcmp($arr[0], $nickname) == 0) {
				$dscs = $arr[1];
				$found = true;
				break;
			}
		}
		fclose($myfile);
		if(!$found) {
			$dscs = "could not fine the connection string, check config file.";
		}
		return $dscs;
	}
	
	
	
}

?>
