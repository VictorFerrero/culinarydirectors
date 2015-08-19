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
	
	 private $menuModel; // this would prevent PDO connection from being made every time we create a MenuModel object
	
	public function __construct() {
		// TODO: 
		// $menuModel = new MenuModel();
	 }
	 
	 public function __destruct() {
		 // ensure that the OrgModel destructor gets called to properly
		 // close the database connection
		 $this->menuModel = null;
	 }
		 
	public function createFeedback() {
		$arrInsertValues = array();
		$arrInsertValues['feedback_type'] = $_REQUEST['feedback_type'];
		$arrInsertValues['feedback_value'] = $_REQUEST['feedback_value'];
		
		$menuModel = new MenuModel();
		$arrResult = $menuModel->createFeedback($arrInsertValues);
		$menuModel = null;
		return $arrResult;
	}

	public function deleteFeedback() {
		$id = $_REQUEST['id'];
		$menuModel = new MenuModel();
		$arrResult = $menuModel->deleteFeedback($id);
		
		return $arrResult;
	}

	public function editFeedBack() {
		$arrEdit = array();
		$arrEdit['id'] = $_REQUEST['id'];
		$arrEdit['feedback_type'] = $_REQUEST['feedback_type'];
		$arrEdit['feedback_value'] = $_REQUEST['feedback_value']; 
		
		$menuModel = new MenuModel();
		$arrResult = $menuModel->editFeedBack($arrEdit);
		return $arrResult;
	}	 

// create a menu. Menu Table schema = 
// id | chef_id | week (0-52) | day (0-7) | approved (0-1)
	 public function createMenu() {
		$arrInsertValues = array();
		$arrInsertValues['chef_id'] = $_REQUEST['chef_id'];
		$arrInsertValues['week'] = $_REQUEST['week'];
	    $arrInsertValues['day'] = $_REQUEST['day'];
		$arrInsertValues['approved'] = $_REQUEST['approved'];
		
		$menuModel = new MenuModel();
		$arrResult = $menuModel->createMenu($arrInsertValues);
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
		
		$menuModel = new MenuModel();
		$arrResult = $menuModel->editMenu($arrEdit);
		return $arrResult;
	 }
	 
	 public function deleteMenu() {
		$id = $_REQUEST['id'];
		$menuModel = new MenuModel();
		$arrResult = $menuModel->deleteMenu($id); // dont forget to remove menu items too
		
		return $arrResult;
	 }
	 
	 // --each menu_item has id | menu_id | item_name | meal (0 for lunch, 1 for dinner)
	 public function createMenuItem() {
		$arrValues = array();
		$arrValues['menu_id'] = $_REQUEST['menu_id'];
		$arrValues['item_name'] = $_REQUEST['item_name'];
		$arrValues['meal'] = $_REQUEST['mean'];
		
		$menuModel = new MenuModel();
		$arrResult = $menuModel->createMenuItem($arrValues);
		return $arrResult;
	 }
	 
	 public function deleteMenuItem() {
		$id = $_REQUEST['id'];
		
		$menuModel = new MenuModel();
		$arrResult = $menuModel->deleteMenuItem($id); 
	 }
	 
	 // every field for a menu_item must be in the $_REQUEST variable. Fields not being
	// editted should be set to the empty string
	 public function editMenuItem() {
		$arrEdit = array();
		$arrEdit['id'] = $_REQUEST['id'];
		$arrEdit['menu_id'] = $_REQUEST['menu_id'];
		$arrEdit['item_name'] = $_REQUEST['item_name']; 
		$arrEdit['meal'] = $_REQUEST['meal'];
		
		$menuModel = new MenuModel();
		$arrResult = $menuModel->editMenuItem($arrEdit);
		return $arrResult;
	 }
}
?>
