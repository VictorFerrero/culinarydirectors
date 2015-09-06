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
		$arrValues['creation'] = date('Y-m-d H:i:s');
		$arrValues['reply_to'] = $_REQUEST['reply_to'];
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
	
	public function getMessagesBySenderId() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['senderId'];
		$arrValues['where_clause'] = "sender=:id";
		$arrResult = $this->feedModel->getMessages($arrValues);
		$arrMessages = $arrResult['data'];
		return $arrResult;
	}
	
	// -1 means the message is TO everyone
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
		$arrValues = array();
		$arrValues['chef_id'] = $_REQUEST['chef_id'];
		$arrValues['week'] = $_REQUEST['week'];
	    $arrValues['day'] = $_REQUEST['day'];
	    $arrValues['datestamp'] = $_REQUEST['datestamp'];
		$arrValues['approved'] = $_REQUEST['approved'];
		$arrResult = array();
		$arrResult['success'] = false; // assume it does not work
		// make sure that week, day, and approved are valid values
		if($this->isInputValid($arrValues['week'], 0)) {
			if($this->isInputValid($arrValues['day'], 1)) {
				if($this->isInputValid($arrValues['approved'], 2)) {
					$arrResult = $this->menuModel->createMenu($arrValues);
				}
			}
		}
		return $arrResult;
	 }
	 
	// every field for a menu must be in the $_REQUEST variable. Fields not being
	// eddited should be set to the empty string 
	 public function editMenu() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['chef_id'] = $_REQUEST['chef_id'];
		$arrValues['week'] = $_REQUEST['week']; 
		$arrValues['day'] = $_REQUEST['day'];
		$arrValues['datestamp'] = $_REQUEST['datestamp'];
		$arrValues['approved'] = $_REQUEST['approved'];
		$arrResult = array();
		$arrResult['success'] = false; // assume it does not work
		// do some error checkking
		if($this->isInputValid($arrValues['week'], 0)) {
			if($this->isInputValid($arrValues['day'], 1)) {
				if($this->isInputValid($arrValues['approved'], 2)) {
					$arrResult = $arrResult = $this->menuModel->editMenu($arrValues);
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
	 
	 // every field for a menu_item must be in the $_REQUEST variable. Fields not being
	// editted should be set to the empty string
	 public function editMenuItem() {
		$arrValues = array();
		$arrResult = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['menu_id'] = $_REQUEST['menu_id'];
		$arrValues['item_name'] = $_REQUEST['item_name']; 
		$arrValues['meal'] = $_REQUEST['meal'];
		$arrResult['success'] = false;
		if(strcmp($arrValues['meal'], "" != 0)) {
			if($this->isInputValid($arrValues['meal'], 2)) { // meal can only be 0 or 1
				$arrResult = $this->menuModel->editMenuItem($arrValues);
			}
		}
		return $arrResult;
	 }
	 
	 public function deleteMenuItem() {
		$id = $_REQUEST['id'];
		$arrResult = $this->menuModel->deleteMenuItem($id); 
	 }
	  
/*we need a menu_feedback table with id, feedback_type (enum, ["lateplate","noshow","thumbs"],
*  and feedback_value (1 for lateplate/noshow -- any entries in here mean "i do have a 
* lateplate/noshow", there is no entry in this table for other cases, 1 for thumbs up and 0 for thumbs down) 
	   * */
	  
	  
	 	public function createFeedback() {
	// might need error checking, or we could put constraints on db fields
		$arrValues = array();
		$arrResult = array();
		$arrValues['feedback_type'] = $_REQUEST['feedback_type'];
		$arrValues['feedback_value'] = $_REQUEST['feedback_value'];
		$arrValues['menu_item_id'] = $_REQUEST['menu_item_id'];
		$arrValues['menu_id'] = $_REQUEST['menu_id'];
		$arrResult['success'] = false; // assume the input is invalid
		if($this->isInputValid($arrValues['feedback_type'], 3)) { // can be 0,1 or 2
			$arrResult = $this->menuModel->createFeedback($arrValues);
		}
		return $arrResult;
	}

	public function deleteFeedback() {
		$id = $_REQUEST['id'];
		$arrResult = $this->menuModel->deleteFeedback($id);
		return $arrResult;
	}

	public function editFeedBack() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['feedback_type'] = $_REQUEST['feedback_type'];
		$arrValues['feedback_value'] = $_REQUEST['feedback_value']; 
		$arrValues['menu_item_id'] = $_REQUEST['menu_item_id'];
		$arrValues['menu_id'] = $_REQUEST['menu_id'];
		if($this->isInputValid($arrValues['feedback_type'], 3)) {
			$arrResult = $this->menuModel->editFeedBack($arrValues);
		}
		return $arrResult;
	}
	
	public function getFeedbackForMenu() {
		$id = $_REQUEST['id'];
		$arrResult = $this->menuModel->getFeedbackForMenu($id);
		return $arrResult;
	}
	 
	 private function isInputValid($input, $flag) {
		switch($flag) {
			case 0:  // // is 0 <= X <= 52 ???
			if($input >= 0 && $input <= 52) {
				return true;
			}		
				return false;
			break;
			
			case 1: // is 0 <= X <= 6 ???
				if($input >= 0 && $input <= 6) {
					return true;
				}
					return false;
				break;	
				
			case 2: // is X = 0 or 1?
				if($input == 0 || $input == 1) {
					return true;
				}
					return false;
			break;
			
			case 3: // is X = 0,1,2?  test used for feedback_type validation
				if($input >= 0 && $input <= 2) {
					return true;
				}
				return false;
			break;
		}
		return false;
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
		 $arrValues = array();
		 $arrValues['id'] = $_REQUEST['id'];
		 $arrValues['where_clause'] = "id=:id";
		 $arrResult = $this->orgModel->deleteOrg($arrValues);
		 return $arrResult;
	 }
	 
	 public function getOrgById() {
		$arrValues = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['where_clause'] = "id=:id";
		$arrResult = $this->orgModel->getOrgById($arrValues);
		return $arrResult;
	 }
}
?>
<?php
class Test{
	public static function getIndex(){return TestModel::getIndexText();}
}
?><?php
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
		$arrValues['email'] = $_REQUEST['email'];
		$arrValues['password'] = $_REQUEST['password'];
		$arrResult = $this->userModel->login($arrValues['email'], $arrValues['password']);
		if($arrResult['login']) { // login was successful
			// get the info about the user
			$arrUser = $arrResult;
			// get the info for the org
			$orgId = $arrUser['user_info']['orgId']; // the id of the org that the user is in
			$orgModel = new OrgModel();
			$arrValues = array();
			$arrValues['id'] = $orgId;
		    $arrValues['where_clause'] = "id=:id";
			$arrOrg = $orgModel->getOrg($arrValues);
			$orgModel = null;// call destructor, close db connections
			// get the info for the feed
			$feedModel = new FeedModel();
			$arrValues = array();
			$arrValues['id'] = $arrUser['user_info']['id']; 
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
			foreach($arrTemp['data'] as $index => $arrAssoc) {
				$arrTemp['data'][$index]['menu_items'] = $menuModel->getMenuItemsForMenu($arrTemp['data'][$index]['id']);
			}
			$arrMenu = $arrTemp; // [0] => assocArray for a menu, [1] => next menu, etc..
			// go through each menu, and get the menu_items for that menu
//			$arrTemp = $menuModel->getMenuItemsForMenu($arrMenu['data'][0]['id']);
//			$arrMenu['menu_items'] = $arrTemp['data'];
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
