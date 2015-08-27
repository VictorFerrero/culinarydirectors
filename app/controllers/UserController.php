<?php
// TODO: login and logout need work

class UserController{
		
	private $userRole; // 0 = fratmember, 1 = chef, 2 = admin
	private $username;
	private $loggedIn;
	private $userModel;
	
	public function __construct() {
		$this->userModel = new UserModel();
	}
	
	public function __destruct() {
		// ensure that the UserModel destructor gets called to properly
		// close the database connection
		$this->userModel = null;
	}
	
	public function isUserInOrg($userId, $orgId) {
		$userId = $_REQUEST['userId'];
		$orgId = $_REQUEST['orgId'];
		$arrResult = $this->userModel->isUserInOrg($userId, $orgId);
		return $arrResult;
	}
	// TODO: cookies
	public function login() {
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrResult = $this->userModel->login($arrValues['username'], $arrValues['password']);
		if($arrResult['login']) {
			$arrUser = $arrResult['userInfo'];
			$this->username = $arrUser['username'];
			$this->userRole = $arrUser['userRole'];
			$this->loggedIn = true;
		}
		else {
			$this->loggedIn = false;
			$this->username = "";
			$this->userRole = -1;
			print_r($arrResult);
		}
		return $arrResult;
	}
	
	// TODO: cookies
	public function logout() {
		$this->loggedIn = false;
		$this->username = "";
		$this->userRole = -1;
		$arrResult = array();
		$arrResult['logout'] = true;
		return $arrResult;
	}
	
	public function getAllUsers(){
		return $this->userModel->getAllUsers();
	}
	
	public function register(){
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrValues['email'] = $_REQUEST['email'];
		$arrValues['userRole'] = $_REQUEST['userRole'];
		$arrValues['orgId'] = $_REQUEST['orgId'];
		$arrResult = $this->userModel->register($arrValues);
		return $arrResult;
		/*
		if($arrResult['success']) {
			//successfully added user
			return $arrResult;
		}
		else {
			//there was an error
			print_r($arrResult);
		}
		* */
	}
	
	public function deleteUser() {
		$arrValues = array();
		$arrValues['username'] = $_REQUEST['username'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrResult = $this->userModel->deleteUser($arrValues['username'], $arrValues['password']);
		return $arrResult;
		/*
		if($arrResult['success']) {
			//successfully added user
			return $arrResult;
		}
		else {
			//there was an error
			print_r($arrResult);
		}
		*/
	}
}

?>
