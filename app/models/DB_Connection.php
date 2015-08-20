<?php
require_once ("config/config.php");
class DB_Connections extends PDO
{
		
	 public function __construct() {
		
	 }
		
	public function getNewDBO() {
		$arrReturn = array();
		$success = false;
		$db = null;
		$arrCredentials = DatabaseConnectionStrings::getDBCredentials();
		$dbNickname = NULL; // need to do something here
		$user = $arrCredentials['username'];
		$password = $arrCredentials['password'];
		try {
		 $dscs = $this->getDSCS($dbNickname); 
		 $db = new PDO($dcsc, $user, $password);
		 $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // make PDO throw exceptions
		 $success = true;
	 } catch(Exception $e) {
		 $success = false;
	//	 $arrReturn['error'] = $e;
	 }
	//	 $arrReturn['success'] = $success;
	//	 $arrReturn['DBO'] = $db;
		 return $db;
	}	
	// DSCS = database specific connection string
	// helper function to access the dscs from config file
	private function getDSCS($nickname) {
		$dscs = DatabaseConnectionStrings::getDSCS($nickname);
		return $dscs;
	}
}

?>
