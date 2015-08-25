<?php
require_once('DB_Connection.php');
// TODO: we need accessors for the menu table. 
// not sure what field we should be selecting on.
class MenuModel
{
	private $dbo;
		
	 public function __construct() {
		$db = new DB_Connections();
		$this->dbo = $db->getNewDBO();
	 }

	public function __destruct() {
		$this->dbo = null;
	}

	/**
		expected input: 
		$arrValues = array( 
		'id' => id for where clause
		'where_clause' => must be of the form 'column'=:id
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly selected, false otherwise
		'data' => the array of menus which satisfied the where clause
		);
	*/
	public function getMenu($arrValues) {
		$id = $arrValues['id'];
		$whereClause = $arrValues['where_clause'];
		$arrResult = array();
		$success = false;
		$sql = "SELECT * FROM menu WHERE " . $whereClause;
		 try {
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":id", $id);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
			$success = true;
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
	    return $arrResult;
	}
	/**
		expected input: 
		$arrValues = array( 
		'chef_id' => the id of the chef who is making the menu,
		'week' => the week of the year (0-52)
		'day' => day of the week (0-6)
		'approve' => 1 if menu is approved, 0 otherwise
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	public function createMenu($arrValues) {
		$arrResult = array();
		$success = false;		
		$chef_id = $arrValues['chef_id'];
		$week = $arrValues['week'];
	    $day = $arrValues['day'];
		$approved = $arrValues['approved'];
		 try {
			$data = array( 'chef_id' => $chef_id, 'week' => $week, 'day' => $day, 'approved' => $approved);
			$STH = $this->dbo->prepare("INSERT INTO menu VALUES (NULL, :chef_id, :week, :day, :approved)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	/**
	   for fields that are not being edditted, the associative array must still be set with the 
	   value of the empty string
		expected input: 
		$arrValues = array( 
		'id' => id of the menu being editted
		'chef_id' => the new id of the chef, "" otherwise
		'week' => the new week for this menu, (0-52)
		'day' => the new day of the week for this menu(0-6)
		'approve' => 1 if menu is approved, 0 otherwise
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
*/
	public function editMenu($arrValues) {
	 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $chef_id = $arrValues['chef_id'];
	 $week = $arrValues['week'];
	 $day = $arrValues['day'];
	 $approved = $arrValues['approved'];
	 $sql = "UPDATE menu SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($chef_id, "") != 0) {
		 $sql = $sql . "chef_id=?, ";
		 $data[$index] = $chef_id;
		 $index = $index + 1;
	 }
	 if(strcmp($week, "") != 0) {
		 $sql = $sql . "week=?, ";
		 $data[$index] = $week;
		 $index = $index + 1;
	 }
	 if(strcmp($day, "") != 0) {
		 $sql = $sql . "day=?, ";
		 $data[$index] = $day;
		 $index = $index + 1;
	 }
	 if(strcmp($approved, "") != 0) {
		 $sql = $sql . "approved=?, ";
		 $data[$index] = $approved;
		 $index = $index + 1;
	 }
	 // get rid of the last two characters
	 $sql = substr($sql,0,-2);
	 $sql = $sql . " WHERE id=?";
	 $data[$index] = $id;
	try {
		 $stm = $this->dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	return $arrResult;
	}
	
		/**
		expected input: id of the menu being deleted
		
		output:
		$arrResult = array (
		'db_result1' => result of running delete query
		'error1' => exception object for the first db query (DELETE FROM Menu)
		'menu_success' => true if menu was successfuly removed, false otherwise
		'db_result2' => result of running second delete query (DELETE FROM Menu_Item)
		'error2' => exception object for the second db query
		'menu_item_success' => true if the menu items on this menu were successfully removed
		'db_result3' => result of running the third delete query (DELETE FROM Menu_Feedback)
		'error3' => the error message from running the 3 delete query
		'menu_feedback_success' => true if the feedback for this menu was removed
		);
	*/
	public function deleteMenu($id) {
		$arrResult = array();
		$arrResult['error'] = array();
		$arrResult['db_result'] = array();
		$success = false;
		$sql = "DELETE FROM menu WHERE id=:id";
		try {
			$stm = $this->dbo->prepare($sql);
			$stm->bindParam(":id", $id);
			$arrResult['db_result'][] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_success'] = $success;
		// now we will try to delete every menu item that is on this menu
		$success = false;
		$sql = "DELETE FROM menu_item WHERE menu_id=:menu_id";
		try{
			$stm = $this->dbo->prepare($sql);
			$stm->bindParam(":menu_id", $id);
			$arrResult['db_result'][] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_item_success'] = $success;
		
		// set menu_id = 0
		$success = false;
		$sql = "UPDATE menu_feedback SET menu_id=0 WHERE menu_id=" . $id;
		try{
			$stm = $this->dbo->prepare($sql);
			$stm->bindParam(":menu_id", $id);
			$arrResult['db_result'][] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_feedback_success'] = $success;
		return $arrResult;
	}
	
	
	/**
		expected input: 
		$arrValues = array( 
		'menu_id' => the id of the menu that this item belongs on
		'item_name' => the name of the item
		'meal' => lunch or dinner?
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);

		//TODO: batch create function that uses a transaction instead of repeating inserts
	*/
	public function createMenuItem($arrValues) {
		$arrResult = array();
		$success = false;
		$menu_id = $arrValues['menu_id'];
		$item_name = $arrValues['item_name'];
		$meal = $arrValues['meal'];
		 try {
			$data = array( 'menu_id' => $menu_id, 'item_name' => $item_name, 'meal' => $meal);
			$STH = $this->dbo->prepare("INSERT INTO menu_item VALUES (NULL, :menu_id, :item_name, :meal)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
		 // --each menu_item has id | menu_id | item_name | meal (0 for lunch, 1 for dinner)
	 	/**
		// must use empty string for fields not being updated.
		expected input: 
		$arrValues = array( 
		'id' => id of the menu item being editted
		'menu_id' => the new id of the menu this item is on, "" otherwise
		'item_name' => the new item name, "" otherwise
		'meal' => lunch or dinner, "" otherwise
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
*/
	public function editMenuItem($arrValues) {
	 $arrResult = array();
	 $success = false;	
	 $id = $arrValues['id'];
	 $menu_id = $arrValues['menu_id'];
	 $item_name = $arrValues['item_name'];
	 $meal = $arrValues['meal'];
	 $sql = "UPDATE menu_item SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($menu_id, "") != 0) {
		 $sql = $sql . "menu_id=?";
		 $data[$index] = $menu_id;
		 $index = $index + 1;
	 }
	 if(strcmp($item_name, "") != 0) {
		 $sql = $sql . ", item_name=?";
		 $data[$index] = $item_name;
		 $index = $index + 1;
	 }
	 if(strcmp($meal, "") != 0) {
		 $sql = $sql . ", meal=?";
		 $data[$index] = $meal;
		 $index = $index + 1;
	 }
	 $sql = $sql . " WHERE id=?";
	 $data[$index] = $id;
	try {
		 $stm = $this->dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	return $arrResult;
	}
	
			/**
		expected input: id of the menu item being deleted
		
		output:
		$arrResult = array (
		'db_result' => result of running delete query
		'error' => exception object for the first db query 
		'success' => true if menu was successfuly removed, false otherwise
		);
	*/
	public function deleteMenuItem($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM menu_item WHERE id=:id";
		try{
			$stm = $this->dbo->prepare($sql);
			$stm->bindParam(":id", $id);
			$arrResult['db_result'] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
	
/*	expected input: id of the menu to get all menu items on that menu
		
		output:
		$arrResult = array (
		'db_result' => result of running delete query
		'error' => exception object for the first db query 
		'success' => true if menu was successfuly removed, false otherwise
		);
	*/
	public function getMenuItemForMenu($menuId) {
		$arrResult = array();
		$success = false;
		$sql = "SELECT * FROM menu_item WHERE menu_id=:menu_id";
		try {
			$stm = $this->dbo->prepare($sql);
			$stm->bindParam(":menu_id", $menuId);
			$arrResult['db_result'] = $stm->execute();
			$fetch = $stm->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
/*
--we need a menu_feedback table with id, feedback_type (enum, ["lateplate","noshow","thumbs"],
*  and feedback_value (1 for lateplate/noshow -- any entries in here mean "i do have a 
* lateplate/noshow", there is no entry in this table for other cases, 1 for thumbs up and 0 for thumbs down)
* we also need to have a field for menu_item_id
* can we also have a field for menu_id?? => will make deletes easier for deleting a menu
	*/
	
	/**
		expected input: 
		$arrValues = array( 
		'feedback_type' => enum, ["lateplate","noshow","thumbs"]
		'feedback_value' => 1 for lateplate/noshow -- any entries in here mean "i do have a lateplate/noshow",
							there is no entry in this table for other cases, 1 for thumbs up and 0 for thumbs down
		'menu_item_id' => the id of the menu item that this feedback corresponds to
		'menu_id' => the id of the menu that this feedback is for (same menu item could be on a different menu, but done better?)
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	public function createFeedback($arrValues) {
		$arrResult = array();
		$success = false;
		$feedback_type = $arrValues['feedback_type'];
		$feedback_value = $arrValues['feedback_value'];
		$menu_item_id = $arrValues['menu_item_id'];
		$menu_id = $arrValues['menu_id'];
		 try {
			$data = array( 'feedback_type' => $feedback_type, 'feedback_value' => $feedback_value, 'menu_item_id' => $menu_item_id, 'menu_id' => $menu_id);
			$STH = $this->dbo->prepare("INSERT INTO menu_feedback VALUES (NULL, :feedback_type, :feedback_value, :menu_item_id, :menu_id)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
 // --each menu_item has id | menu_id | item_name | meal (0 for lunch, 1 for dinner)
	 	/**
		// must use empty string for fields not being updated.
		expected input: 
		$arrValues = array( 
		'id' => id of the feedback being editted
		'feedback_type' => the new id of the menu this item is on, "" otherwise
		'feedback_value' => the new item name, "" otherwise
		'menu_item_id' => the new id of the menu item this feedback is for
		'menu_id' => the new id of the menu this feedback is for
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
*/
	public function editFeedback($arrValues) {
	 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $feedback_type = $arrValues['feedback_type'];
	 $feedback_value = $arrValues['feedback_value'];
	 $menu_item_id = $arrValues['menu_item_id'];
	 $menu_id = $arrValues['menu_id'];
	 $sql = "UPDATE menu_feedback SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($feedback_type, "") != 0) {
		 $sql = $sql . "feedback_type=?, ";
		 $data[$index] = $feedback_type;
		 $index = $index + 1;
	 }
	 if(strcmp($feedback_value, "") != 0) {
		 $sql = $sql . "feedback_value=?, ";
		 $data[$index] = $feedback_value;
		 $index = $index + 1;
	 }
	 if(strcmp($feedback_value, "") != 0) {
		 $sql = $sql . ", menu_item_id=?, ";
		 $data[$index] = $menu_item_id;
		 $index = $index + 1;
	 }
	  if(strcmp($menu_id, "") != 0) {
		 $sql = $sql . "menu_id=?, ";
		 $data[$index] = $menu_id;
		 $index = $index + 1;
	 }
	 // get rid of the last two characters 
	 $sql = substr($sql,0,-2);
	 $sql = $sql . " WHERE id=?";
	 $data[$index] = $id;
	try {
		 $stm = $this->dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	return $arrResult;
	}
	
			/**
		expected input: id of the feedback being deleted
		
		output:
		$arrResult = array (
		'db_result' => result of running delete query
		'error' => exception object for the first db query 
		'success' => true if menu was successfuly removed, false otherwise
		);
	*/
	public function deleteFeedback($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM menu_feedback WHERE id=:id";
		try {
			$stm = $this->dbo->prepare($sql);
			$stm->bindParam(":id", $id);
			$arrResult['db_result'] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
/*	
	expected input: id of the menu to get feedback for
		
		output:
		$arrResult = array (
		'db_result' => result of running the query
		'error' => exception object for the first db query 
		'success' => true if menu was successfuly removed, false otherwise
		'data' => array containing all feedback for the given menu id
		);
	*/
	public function getFeedbackForMenu($menuId) {
		$arrResult = array();
		$success = false;
		$sql = "SELECT * FROM menu_feedback WHERE menu_id=:menu_id";
		try {
			$stm = $this->dbo->prepare($sql);
			$stm->bindParam(":menu_id", $menuId);
			$arrResult['db_result'] = $stm->execute();
			$fetch = $stm->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;	
	}
}
?>
