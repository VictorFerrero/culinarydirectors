<?php
class UserController{
		
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

	public function login() {
		$arrValues = array();
		$email = $_REQUEST['email'];
		$password = $_REQUEST['password'];
		$arrResult = $this->userModel->login($email, $password);
		if($arrResult['login']) { // login was successful
			// get the info about the user
			$arrUser = $arrResult;
			$orgId = $arrUser['user_profile']['orgId']; // the id of the org that the user is in
			$userId = $arrUser['user_profile']['id'];
			// get the info for the org
			$orgModel = new OrgModel();
			$arrValues = array();
			$arrValues['id'] = $orgId;
		    $arrValues['where_clause'] = "id=:id";
			$arrOrg = $orgModel->getOrg($arrValues);
			$orgModel = null;// call destructor, close db connections
			// get the info for the feed
			$feedModel = new FeedModel();
			$arrValues = array();
			$arrValues['id'] = $userId; 
			$arrValues['where_clause'] = "receiver=:id"; // get all messages in feed sent to this user
			$arrTemp = $feedModel->getMessages($arrValues);
			$arrFeed['received'] = $arrTemp; // get the messages directed toward the user
			$arrValues = array();
			$arrValues['id'] = -1; // -1 means the message is sent to everyone
			$arrValues['where_clause'] = "receiver=:id";
			$arrTemp = $feedModel->getMessages($arrValues);
			$arrFeed['to_everyone'] = $arrTemp; // get the messages directed toward everyone
			// TODO: more queries to get more messages from the feed
			$feedModel = null;
			// get the menu
			$menuModel = new MenuModel();
			$arrValues = array();
			$arrTemp =  $menuModel->getMenuForOrg($orgId);
			// go through each menu and get the menu items that are on that menu
			foreach($arrTemp['data'] as $index => $arrAssoc) {
				$arr =  $menuModel->getMenuItemsForMenu($arrTemp['data'][$index]['id']);
				$arrLunchItems = array();
				$arrDinnerItems = array();
				foreach($arr['data'] as $int => $menu_item_record) {
					if($menu_item_record['meal'] == 0) {
						$arrLunchItems[] = $menu_item_record;
					}
					else {
						$arrDinnerItems[] = $menu_item_record;
					}
				}
				$arrTemp['data'][$index]['lunch_items'] = $arrLunchItems;
				$arrTemp['data'][$index]['dinner_items'] = $arrDinnerItems;
			}
			$arrMenu = $arrTemp;
			$menuModel = null;
			$arrResult = array();
			$arrResult['login'] = true; // since we destroy $arrResult values, we need this to send to client
			$arrResult['user'] = $arrUser;
			$arrResult['feed'] = $arrFeed;
			$arrResult['menu'] = $arrMenu;
			$arrResult['org'] = $arrOrg;
			return $arrResult;
		}
		else {
			// $arrResult will already have error info set from call to user model
		}
		return $arrResult;
	}
	
	public function logout() {
		$arrResult = array();
		return $arrResult;
	}
	
	public function getAllUsers(){
		return $this->userModel->getAllUsers();
	}
	
	public function register(){
		$arrValues = array();
		$arrResult = array();
		$arrValues['password'] = $_REQUEST['password'];
		$arrValues['email'] = $_REQUEST['email'];
		$arrValues['userRole'] = $_REQUEST['userRole'];
		$arrValues['orgId'] = $_REQUEST['orgId'];
		$arrValues['fname'] = $_REQUEST['fname'];
		$arrValues['lname'] = $_REQUEST['lname'];
		if($arrValues['userRole'] == 0) {
			$arrValues['meal_plan'] = $_REQUEST['meal_plan'];
			$arrValues['dietary_restrictions'] = $_REQUEST['dietary_restrictions'];
		}
		$arrValues['profileJSON'] = $_REQUEST['profileJSON'];
		$arrResult['valid_input'] = false; // assume invalid input 
		if($this->isInputValid($arrValues['userRole'], 0)) {
			$arrResult = $this->userModel->register($arrValues);
			$arrResult['valid_input'] = true;
		}
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
	}
	
	public function forgotPassword() {
		$email = $_REQUEST['email']; 
		$arrResult = $this->userModel->forgotPassword($email);
		return $arrResult;
	}
	
	private function isInputValid($input, $flag) {
		
		switch($flag) {
			case 0: // used to validate userRole when registering a user
				if($input >=0 && $input <= 2) {
					return true;
				}
				return false;
			break;
		}
		return false;
	}
}

?>
