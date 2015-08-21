<?php


class DatabaseConnectionStrings{

	private static $MySQL_DSCS = "MySQL"; // TODO: need to get MySql connection string
	
	// just default to MySql right now
	public function getDBCS($dbNickname) {
		
	//	if(strcmp($dbNickname, "MySQL") == 0) {
			return DatabaseConnectionStrings::$MySQL_DSCS;
	//	}
	}
	
	public function getDBCredentials($userRole){
		$arr = array();
		// TODO: accessing db credentials
		/*
		$arr['username'] = "root";
		$arr['password'] = "password";
		return $arr;
		*/ 
		
		// OR 
		
		/*
		switch ($userRole) {
			
			case 0: 
			$arr['username'] = "member";
			$arr['password'] = "password";
			break; // frat member credentials
		
			case 1: 
			$arr['username'] = "chef";
			$arr['password'] = "password";
			break; // chef credentials
			
			case 2: 
			$arr['username'] = "root";
			$arr['password'] = "password";
			break; // admin credentials
		}
		*/
		return $arr;
	}

}
?>
