<?php
class FeedController
{
	private $feedModel;
	
	// id | to | from | message
	public function __construct() {
		$this->feedModel = new FeedModel();
	}
	
	public function __destruct() {
		$this->feedModel = null;
	}
	
	public function addMessage() {
		$arrValues = array();
		$arrValues['sender'] = $_REQUEST['sender'];
		$arrValues['receiver'] = $_REQUEST['receiver'];
		$arrValues['message'] = $_REQUEST['message'];
		
		$arrResult = $this->feedModel->addMessage($arrValues);
		return $arrResult;
	}
	
	public function deleteMessageById() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id']; // id of thing we want to delete
		$arrValues['where_clause'] = "id=:id"; // where clause specifying what condition is to delete
		$arrResult = $this->feedModel->deleteMessage($arrValues);
		return $arrResult;
	}
	
	// -1 means the message is TO everyone
	public function getMessagesBySenderId() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['senderId'];
		$arrValues['where_clause'] = "sender=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
		return $arrResult;
	}
	
	public function getMessagesByReceiverId() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['receiverId'];
		$arrValues['where_clause'] = "receiver=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
		return $arrResult;
	}
	
	public function getMessagesById() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['where_clause'] = "id=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		
		$arrMessages = $arrResult['data'];
		return $arrResult;
	}
}
?>
<?php
class MenuController{
	/*
	 * does feed_back table need a field for menu_item_id. Otherwise, how do we know
	 * which menu item the feedback is for?
	 * 
	 * 
	 *   MenuController - handles create/edit/delete of menus
--a menu has id | chef_id | week (0-52) | day (0-7) | approved (0-1)
-----(the way we are building calendar, day/week is better than a date or timestamp)
--each menu_item has id | menu_id | item_name | meal (0 for lunch, 1 for dinner)
--we need a menu_feedback table with id, feedback_type (enum, ["lateplate","noshow","thumbs"],
*  and feedback_value (1 for lateplate/noshow -- any entries in here mean "i do have a 
* lateplate/noshow", there is no entry in this table for other cases, 1 for thumbs up and 0 for thumbs down)
	 * 
	 * add menu_item_id to feedback
	 */
	
	 private $menuModel; 
	
	public function __construct() {
		// TODO: 
		 $this->menuModel = new MenuModel();
	 }
	 
	 public function __destruct() {
		 // ensure that the MenuModel destructor gets called to properly
		 // close the database connection
		 $this->menuModel = null;
	 }	 

// create a menu. Menu Table schema = 
// id | chef_id | week (0-52) | day (0-7) | approved (0-1)
	 public function createMenu() {
		$arrInsertValues = array();
		$arrInsertValues['chef_id'] = $_REQUEST['chef_id'];
		$arrInsertValues['week'] = $_REQUEST['week'];
	    $arrInsertValues['day'] = $_REQUEST['day'];
		$arrInsertValues['approved'] = $_REQUEST['approved'];
		$arrResult = array();
		$arrResult['success'] = false; // assume it does not work
		// make sure that week, day, and approved are valid values
		if($this->isInputValid($arrInsertValues['week'], 0)) {
			if($this->isInputValid($arrInsertValues['day'], 1)) {
				if($this->isInputValid($arrInsertValues['approved'], 2)) {
				$arrResult = $this->menuModel->createMenu($arrInsertValues);
				}
			}
		}
		return $arrResult;
	 }
	 
	// every field for a menu must be in the $_REQUEST variable. Fields not being
	// eddited should be set to the empty string 
	 public function editMenu() {
		$arrEdit = array();
		$arrEdit['id'] = $_REQUEST['id'];
		$arrEdit['chef_id'] = $_REQUEST['chef_id'];
		$arrEdit['week'] = $_REQUEST['week']; 
		$arrEdit['day'] = $_REQUEST['day'];
		$arrEdit['approved'] = $_REQUEST['approved'];
		$arrResult = array();
		$arrResult['success'] = false; // assume it does not work
		// do some error checkking
		if($this->isInputValid($arrEdit['week'], 0)) {
			if($this->isInputValid($arrEdit['day'], 1)) {
				if($this->isInputValid($arrEdit['approved'], 2)) {
				$arrResult = $arrResult = $this->menuModel->editMenu($arrEdit);
				}
			}
		}
		return $arrResult;
	 }
	 
	 public function deleteMenu() {
		$id = $_REQUEST['id'];
		$arrResult = $this->menuModel->deleteMenu($id); 
		return $arrResult;
	 }
	 
	 // --each menu_item has id | menu_id | item_name | meal (0 for lunch, 1 for dinner)
	 public function createMenuItem() {
		$arrValues = array();
		$arrValues['menu_id'] = $_REQUEST['menu_id']; // do we have to check that this id exists?
		$arrValues['item_name'] = $_REQUEST['item_name'];
		$arrValues['meal'] = $_REQUEST['meal'];
		$arrResult = array();
		$arrResult['success'] = false;
		if($this->isInputValid($arrValues['meal'], 2)) { // meal can only be 0 or 1
			$arrResult = $this->menuModel->createMenuItem($arrValues);
		}
		return $arrResult;
	 }
	 
	 public function deleteMenuItem() {
		$id = $_REQUEST['id'];
		$arrResult = $this->menuModel->deleteMenuItem($id); 
	 }
	 
	 // every field for a menu_item must be in the $_REQUEST variable. Fields not being
	// editted should be set to the empty string
	 public function editMenuItem() {
		$arrEdit = array();
		$arrEdit['id'] = $_REQUEST['id'];
		$arrEdit['menu_id'] = $_REQUEST['menu_id'];
		$arrEdit['item_name'] = $_REQUEST['item_name']; 
		$arrEdit['meal'] = $_REQUEST['meal'];
		$arrResult = array();
		$arrResult['success'] = false;
//		if(strcmp($arrEdit['meal'], "" != 0)) {
//			if($this->isInputValid($arrEdit['meal'], 2)) {
				$arrResult = $this->menuModel->editMenuItem($arrEdit);
//			}
//		}
		return $arrResult;
	 }
	 
	 	public function createFeedback() {
	// might need error checking, or we could put constraints on db fields
		$arrInsertValues = array();
		$arrInsertValues['feedback_type'] = $_REQUEST['feedback_type'];
		$arrInsertValues['feedback_value'] = $_REQUEST['feedback_value'];
		$arrInsertValues['menu_item_id'] = $_REQUEST['menu_item_id'];
		$arrInsertValues['menu_id'] = $_REQUEST['menu_id'];
		$arrResult = $this->menuModel->createFeedback($arrInsertValues);
		return $arrResult;
	}

	public function deleteFeedback() {
		$id = $_REQUEST['id'];
		$arrResult = $this->menuModel->deleteFeedback($id);
		return $arrResult;
	}

	public function editFeedBack() {
		$arrEdit = array();
		$arrEdit['id'] = $_REQUEST['id'];
		$arrEdit['feedback_type'] = $_REQUEST['feedback_type'];
		$arrEdit['feedback_value'] = $_REQUEST['feedback_value']; 
		$arrEdit['menu_item_id'] = $_REQUEST['menu_item_id'];
		$arrEdit['menu_id'] = $_REQUEST['menu_id'];
		$arrResult = $this->menuModel->editFeedBack($arrEdit);
		return $arrResult;
	}
	
	public function getFeedbackForMenu() {
		$id = $_REQUEST['id'];
		$arrResult = $this->menuModel->getFeedbackForMenu($id);
		return $arrResult;
	}
	 
	 private function isInputValid($input, $flag) {
		switch($flag) {
			case 0:  //check valid week
			if($input >= 0 AND $input <= 52) {
				return true;
			}		
			return false;
			break;
			case 1: // check for valid day
				if($input >= 0 AND $input <= 6) {
					return true;
				}
				return false;
				break;	
			case 2: // check for valid approved value
				if($input == 0 OR $input == 1) {
						return true;
				}
			break;
		}
	 }
}
?>
<?php

// id | name | address | city | state | zip | phone | email | phone2 | profileJSON
class OrgController{
		
		
//		public $arrOrgInfo; // keep org info stored in associative array
		private $orgModel;
		// TODO: use ID to select this from db
	public function __construct() {
		$this->orgModel = new OrgModel(); // TODO: 
	 }
	 
	 public function __destruct() {
		 // ensure that the OrgModel destructor gets called to properly
		 // close the database connection
		 $this->orgModel = null;
	 }
	 	 
	 public function createOrg() {
		$arrValues = array();
		$arrValues['name'] = $_REQUEST['name'];
		$arrValues['address'] =  $_REQUEST['address'];
	    $arrValues['city'] =  $_REQUEST['city'];
		$arrValues['state'] =  $_REQUEST['state'];
		$arrValues['zip'] =  $_REQUEST['zip'];
		$arrValues['phone'] =  $_REQUEST['phone'];
		$arrValues['email'] =  $_REQUEST['email'];
		$arrValues['phone2'] = $_REQUEST['phone2'];
		$arrValues['profileJSON'] = $_REQUEST['profileJSON'];
		
		$arrResult = $this->orgModel->createOrg($arrValues);
		return $arrResult;
	 }
	 
	 public function editOrg() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['name'] = $_REQUEST['name'];
		$arrValues['address'] =  $_REQUEST['address'];
	    $arrValues['city'] =  $_REQUEST['city'];
		$arrValues['state'] =  $_REQUEST['state'];
		$arrValues['zip'] =  $_REQUEST['zip'];
		$arrValues['phone'] =  $_REQUEST['phone'];
		$arrValues['email'] =  $_REQUEST['email'];
		$arrValues['phone2'] = $_REQUEST['phone2'];
		$arrValues['profileJSON'] = $_REQUEST['profileJSON'];
		$arrValues['id'] = $_REQUEST['id'];
		$arrResult = $this->orgModel->editOrg($arrValues);
		return $arrResult;
	 }
	 
	 public function deleteOrg() {
		 $id = $_REQUEST['id'];
		 $arrResult = $this->orgModel->deleteOrg($id);
		 return $arrResult;
	 }
	 
	 public function getOrgById() {
		$id = $_REQUEST['id'];
		$arrResult = $this->orgModel->getOrgById($id);
		return $arrResult;
	 }
}
?>
<?php
class Test{
	public static function getIndex(){return TestModel::getIndexText();}
}
?><?php
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
