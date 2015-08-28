<?php


class DatabaseConnectionStrings{

	private static $MySQL_DSCS = "localhost:root:11235813:culinarydirectors"; // TODO: need to get MySql connection string
	
	// just default to MySql right now
	public static function getDBCS($dbNickname) {		
	//	if(strcmp($dbNickname, "MySQL") == 0) {
			return DatabaseConnectionStrings::$MySQL_DSCS;
	//	}
	}
	
	public static function getDBCredentials($environment){
		$environment = 'local';
		//TODO: get nickname from environment->nickname mapping (maybe assoc array)?
		$numericalArr = explode(':',DatabaseConnectionStrings::getDBCS("MySQL_local"));
		$optionsArr = array();
		return array("host"=>$numericalArr[0],
					"username"=>$numericalArr[1],
					"password"=>$numericalArr[2],
					"schema"=>$numericalArr[3],
					"dsn"=>'mysql:host='.$numericalArr[0].';dbname='.$numericalArr[3].'',
					"options"=>$optionsArr);
	}

}
?>
