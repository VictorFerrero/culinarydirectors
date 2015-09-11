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
	
	// password, email, userRole, orgId, fname, lname
	public function registerUsersWithCSV() {
	try {
	$arrResult = array();
	$arrResult[0] = $_POST;
	$arrResult[1] = $_FILES;
		$arrValues = array();
		$intIndex = 0; 
		$x = 1;
		// populate $arrValues with all the fields
		if(isset($_POST['submit'])) {
			if($_FILES['csv']['error'] == 0) {
				$tmpName = $_FILES['csv']['tmp_name'];
				$csvAsArray = array_map('str_getcsv', file($tmpName));
				foreach($csvAsArray as $index => $numericalArr) {
					if($index == 0 && strcmp($numericalArr[2], "userRole") == 0) {
						// sometimes the first line in the csv could be the column names, so we do nothing
					}
					else { 
						$userRole = $numericalArr[2];
						$arrValues[$intIndex]['password'] = $numericalArr[0];
						$arrValues[$intIndex]['email'] = $numericalArr[1];
						$arrValues[$intIndex]['userRole'] = $numericalArr[2];
						$arrValues[$intIndex]['orgId'] = $numericalArr[3];
						$arrValues[$intIndex]['fname'] = $numericalArr[4];
						$arrValues[$intIndex]['lname'] = $numericalArr[5];
						if($userRole == 0) { // only members have these two fields
							$arrValues[$intIndex]['meal_plan'] = $numericalArr[6];
							$arrValues[$intIndex]['dietary_restrictions'] = $numericalArr[7];
						}
						$arrValues[$intIndex]['profileJSON'] = $numericalArr[8]; // everyone gets a json profile
						$intIndex = $intIndex + 1;
					}
				}
			}
			else {
				$arr = array('error' => "error uploading the csv file");
				return $arr;
			}
		}
		} catch (Exception $e) {
			return $e->getMessage();
		}
		$arrResult = $this->userModel->register($arrValues);
		return $arrResult;
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
		$arrValues[0]['password'] = $_REQUEST['password'];
		$arrValues[0]['email'] = $_REQUEST['email'];
		$arrValues[0]['userRole'] = $_REQUEST['userRole'];
		$arrValues[0]['orgId'] = $_REQUEST['orgId'];
		$arrValues[0]['fname'] = $_REQUEST['fname'];
		$arrValues[0]['lname'] = $_REQUEST['lname'];
		if($arrValues[0]['userRole'] == 0) { // only members have these two fields
			$arrValues[0]['meal_plan'] = $_REQUEST['meal_plan'];
			$arrValues[0]['dietary_restrictions'] = $_REQUEST['dietary_restrictions'];
		}
		$arrValues[0]['profileJSON'] = $_REQUEST['profileJSON'];
		$arrResult['valid_input'] = false; // assume invalid input 
		if($this->isInputValid($arrValues['userRole'], 0)) {
			$arrResult = $this->userModel->register($arrValues);
			$arrResult['valid_input'] = true;
		}
		return $arrResult;
	}
	
	public function deleteUser() {
		$arrValues = array();
		$arrValues['email'] = $_REQUEST['email'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrValues['userRole'] = $_REQUEST['userRole'];
		$arrResult = $this->userModel->deleteUser($arrValues);
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
