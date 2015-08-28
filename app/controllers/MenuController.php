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
		$arrValues['approved'] = $_REQUEST['approved'];
		$arrResult = array();
		$arrResult['valid_input'] = false; // assume it does not work
		// make sure that week, day, and approved are valid values
		if($this->isInputValid($arrValues['week'], 0)) {
			if($this->isInputValid($arrValues['day'], 1)) {
				if($this->isInputValid($arrValues['approved'], 2)) {
					$arrResult = $this->menuModel->createMenu($arrValues);
					$arrResult['valid_input'] = true;
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
		$arrValues['approved'] = $_REQUEST['approved'];
		$arrResult = array();
		$arrResult['valid_input'] = false; // assume it does not work
		// do some error checkking
		if($this->isInputValid($arrValues['week'], 0)) {
			if($this->isInputValid($arrValues['day'], 1)) {
				if($this->isInputValid($arrValues['approved'], 2)) {
					$arrResult = $arrResult = $this->menuModel->editMenu($arrValues);
					$arrResult['valid_input'] = true;
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
		$arrResult['valid_input'] = false;
		if($this->isInputValid($arrValues['meal'], 2)) { // meal can only be 0 or 1
			$arrResult = $this->menuModel->createMenuItem($arrValues);
			$arrResult['valid_input'] = true;
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
		$arrResult['valid_input'] = false;
		if(strcmp($arrValues['meal'], "" != 0)) {
			if($this->isInputValid($arrValues['meal'], 2)) { // meal can only be 0 or 1
				$arrResult = $this->menuModel->editMenuItem($arrValues);
				$arrResult['valid_input'] = true;
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
		$arrResult['valid_input'] = false; // assume the input is invalid
		if($this->isInputValid($arrValues['feedback_type'], 3)) { // can be 0,1 or 2
			$arrResult = $this->menuModel->createFeedback($arrValues);
			$arrResult['valid_input'] = true;
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
		$arrResult = array();
		$arrValues['id'] = $_REQUEST['id'];
		$arrValues['feedback_type'] = $_REQUEST['feedback_type'];
		$arrValues['feedback_value'] = $_REQUEST['feedback_value']; 
		$arrValues['menu_item_id'] = $_REQUEST['menu_item_id'];
		$arrValues['menu_id'] = $_REQUEST['menu_id'];
		$arrResult['valid_input'] = false;
		if($this->isInputValid($arrValues['feedback_type'], 3)) {
			$arrResult = $this->menuModel->editFeedBack($arrValues);
			$arrResult['valid_input'] = true;
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
