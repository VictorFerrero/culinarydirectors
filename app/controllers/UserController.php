<?php

class UserController{
		

	private $userRole; // 0 = fratmember, 1 = chef, 2 = admin
	private $username;
	private $password;
	private $userModel;
	
	public function __construct($userRole, $username, $password) {
		if($this->authenticate($username, $password) {
			$this->username = $username;
			$this->password = $this->hash($password); // store the password hashed?
		}
		else {
			throw new Exception("Invalid user credentials");
		}
		$this->userRole = $userRole;
		$this->userModel = new UserModel("MySQL", $username, $password);
	}
	
	public function __destruct() {
		// ensure that the OrgModel destructor gets called to properly
		// close the database connection
		$this->userModel = null;
	}
	
	
	private function authenticate($username, $password) {
		
		
		return true;
	}

	private function hash($password) {
		$hashedPassword = $password;
		
		return $hashedPassword;
	}
}

?>
