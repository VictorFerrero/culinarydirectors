<?php

class UserModel{
		
		
		
	private $dbo;
	
	
	 public function __construct($dbNickname, $user, $pass) {
			$db = new DB_Connections()->getNewDBO($dbNickname, $user, $pass);
			$this->dbo = $db;
	 }

	public function __destruct() {
		$this->dbo = null;
	}
		
}

?>
