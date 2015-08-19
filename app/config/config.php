<?php


class DatabaseConnectionStrings{

	
	private static $MySQL = "";
	
	
	public function getDBCS($dbNickname) {
		
		if(strcmp($dbNickname, "MySQL")) {
			return DatabaseConnectionStrings::$MySQL;
		}
	}

}
?>
