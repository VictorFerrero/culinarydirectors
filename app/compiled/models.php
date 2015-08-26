<?php

class DB_Connections
{
		
	 public function __construct() {
		
	 }
		
	public function getNewDBO() {
		$arrReturn = array();
		$success = false;
		$db = null;
		// TODO: accessing db credentials=> connection string, username and password??
		$arrCredentials = DatabaseConnectionStrings::getDBCredentials("local");
		
		$dsn = $arrCredentials['dsn'];
		$user = $arrCredentials['username'];
		$password = $arrCredentials['password'];
		$options = $arrCredentials['options'];
		
		try {
			 $db = new PDO($dsn, $user, $password);
			 $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // make PDO throw exceptions
			 $success = true;
		 } catch(Exception $e) {
			 $success = false;
		//	 $arrReturn['error'] = $e->getMessage();
		 }
	//	 $arrReturn['success'] = $success;
	//	 $arrReturn['DBO'] = $db;
		 return $db;
	}	
}

?>
<?php

class FeedModel
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
		'to' => enum, ["lateplate","noshow","thumbs"]
		'from' => 1 for lateplate/noshow -- any entries in here mean "i do have a lateplate/noshow",
							there is no entry in this table for other cases, 1 for thumbs up and 0 for thumbs down
		'message' => the id of the menu item that this feedback corresponds to
		)
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if message was successfuly added, false otherwise
		);
	*/
	public function addMessage($arrValues) {
		$arrResult = array();
		$success = false;		
		$sender = $arrValues['sender'];
		$receiver = $arrValues['receiver'];
	    $message = $arrValues['message'];
		 try {
			$data = array( 'sender' => $sender, 'receiver' => $receiver, 'message' => $message);
			$STH = $this->dbo->prepare("INSERT INTO feed VALUES (NULL, :sender, :receiver, :message)");
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
		expected input: 
		$id
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if delete was successfuly created, false otherwise
		);
	*/
	public function deleteMessage($arrValues) {
		$arrResult = array();
		$success = false;
		$id = $arrValues['id'];
		$whereClause = $arrValues['where_clause'];
		$sql = "DELETE FROM feed WHERE " . $whereClause;
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
	public function getMessages($arrValues) {
		$arrResult = array();
		$success = false;
		$id = $arrValues['id'];
		$whereClause = $arrValues['where_clause'];
		$sql = "SELECT * FROM feed WHERE " . $whereClause;
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
}

?>
<?php
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
		$sql = "UPDATE menu_feedback SET menu_id=0, menu_item_id=0 WHERE menu_id=" . $id;
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
		 $sql = $sql . "menu_id=?, ";
		 $data[$index] = $menu_id;
		 $index = $index + 1;
	 }
	 if(strcmp($item_name, "") != 0) {
		 $sql = $sql . "item_name=?, ";
		 $data[$index] = $item_name;
		 $index = $index + 1;
	 }
	 if(strcmp($meal, "") != 0) {
		 $sql = $sql . "meal=?, ";
		 $data[$index] = $meal;
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
	 if(strcmp($menu_item_id, "") != 0) {
		 $sql = $sql . "menu_item_id=?, ";
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
<?php
/*
 * Org table database schema: 
 * 
 * id | name | address | city | state | zip | phone | email | phone2 | profileJSON
 * 
 * */
class OrgModel{
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
		$arrValues => assoc array containing all fields neccessary 
					to add an org to the database
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if org was successfully retrieved from the db
		);
	*/
	 public function createOrg($arrValues) {
		$arrResult = array();
		$success = false;		
		$name = $arrValues['name'];
		$address = $arrValues['address'];
	    $city = $arrValues['city'];
		$state = $arrValues['state'];
		$zip = $arrValues['zip'];
		$phone = $arrValues['phone'];
		$email = $arrValues['email'];
		$phone2 = $arrValues['phone2'];
		$profileJSON = $arrValues['profileJSON'];
		 try {
			$data = array( 
			'name' => $name, 'address' => $address, 
			'city' => $city, 'state' => $state, 
			'zip' => $zip, 'phone' => $phone, 'email' => $email,
			'phone2' => $phone2, 'profileJSON' => $profileJSON);
			$STH = $this->dbo->prepare("INSERT INTO org VALUES (NULL, :name, :address, 
			:city, :state, :zip, :phone, :email, :phone2, :profileJSON)");
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
		expected input: 
		$arrValues => assoc array containing all fields to be changed.
					if a field is not being changed, it must be set to empty string
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if org was successfully retrieved from the db
		);
	*/
	 public function editOrg($arrValues) {
	$arrResult = array();
	$success = false;
	$id = $arrValues['id'];
	$name = $arrValues['name'];
	$address = $arrValues['address'];
	$city = $arrValues['city'];
	$state = $arrValues['state'];
	$zip = $arrValues['zip'];
	$phone = $arrValues['phone'];
	$email = $arrValues['email'];
	$phone2 = $arrValues['phone2'];
	$profileJSON = $arrValues['profileJSON'];
	 $sql = "UPDATE org SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($name, "") != 0) {
		 $sql = $sql . "name=?, ";
		 $data[$index] = $name;
		 $index = $index + 1;
	 }
	 if(strcmp($address, "") != 0) {
		 $sql = $sql . "address=?, ";
		 $data[$index] = $address;
		 $index = $index + 1;
	 }
	 if(strcmp($city, "") != 0) {
		 $sql = $sql . "city=?, ";
		 $data[$index] = $city;
		 $index = $index + 1;
	 }
	 if(strcmp($state, "") != 0) {
		 $sql = $sql . "state=?, ";
		 $data[$index] = $state;
		 $index = $index + 1;
	 }
	  if(strcmp($zip, "") != 0) {
		 $sql = $sql . "zip=?, ";
		 $data[$index] = $zip;
		 $index = $index + 1;
	 }
	  if(strcmp($phone, "") != 0) {
		 $sql = $sql . "phone=?, ";
		 $data[$index] = $phone;
		 $index = $index + 1;
	 }
	  if(strcmp($email, "") != 0) {
		 $sql = $sql . "email=?, ";
		 $data[$index] = $email;
		 $index = $index + 1;
	 }
	  if(strcmp($phone2, "") != 0) {
		 $sql = $sql . "phone2=?, ";
		 $data[$index] = $phone2;
		 $index = $index + 1;
	 }
	  if(strcmp($profileJSON, "") != 0) {
		 $sql = $sql . "profileJSON=?, ";
		 $data[$index] = $profileJSON;
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
	$arrResult['sql'] = $sql;
	$arrResult['data'] = $data;
	return $arrResult;
	 }
	 
	 /**
		expected input: 
		$id => the id of the org being deleted
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if org was successfully retrieved from the db
		'db_result' => result of running the query. 1 for success, 0 for failure
		);
	*/	
	 public function deleteOrg($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM org WHERE id=:id";
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
	 
	 /**
		expected input: the orgs id
		output:
		$arrResult = array (
		'data' => assoc array containing this orgs record from db
		'error' => exception object for db query
		'success' => true if org was successfully retrieved from the db
		);
	*/
	 public function getOrgById($id) {
		 $success = false;
		 $arrResult = array();
		 
		 try {
			$stmt = $this->dbo->prepare("SELECT * FROM org WHERE id=:id");
			$stmt->bindParam(':id', $id);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$success = true;
			$arrResult['data'] = $row; 
		 } catch(Exception $e) {
			 $success = false;
			 $arrResult['error'] = $e->getMessage();
		 }
		 $arrResult['success'] = $success;
		 return $arrResult;
	 }
}
?>
<?php
class TestModel{
	public static function getIndexText(){return "Hello World";}
}
?><?php
	// id | username | password | email | userRole | orgId
	//TODO: get true/false if user_id is in org_id
class UserModel{
			
	private $dbo;
	
	 public function __construct() {
			$db = new DB_Connections();
			$this->dbo = $db->getNewDBO();
	 }

	public function __destruct() {
		$this->dbo = null;
	}

// TODO: success is getting set to false when it should be true
	public function isUserInOrg($userId, $orgId) {
		$success = false;
		$arrResult = array();
		$success = false;
		$returnValue = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE id=:id");
			$STH->bindParam(":id", $userId);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$succcess = true;
			if($fetch[0]['orgId'] == $orgId) {
				$returnValue = true;
			}
			else {
				$returnValue = false;
			}	
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		$arrResult['returnValue'] = $returnValue;
		return $arrResult;
	}
	
	/**
		no input
		return list of users
	*/
	public function getAllUsers(){
		try{
			$STH = $this->dbo->prepare("SELECT * FROM user");
			$STH->execute();
			return $STH->fetchAll();
		}
		catch(Exception $e){
			return array("error"=>$e->getMessage());
		}
	}
	
	/**
		expected input: 
		$arrValues = array( 
		'username' => username,
		'password' => non-hashed user password
		'email' => user email address
		'userRole' => the users role
		
		output:
		$arrResult = array (
		'error' => array of errors that occurred
		'success' => true if user was successfuly added, false otherwise
		);
	*/
	public function register($arrValues) {
		// first we check if username already exists
		$arrResult = array();
		$success = false;
		$username = $arrValues['username'];
		$hashedPassword = password_hash($arrValues['password'], PASSWORD_BCRYPT);
		$email = $arrValues['email'];
		$userRole = $arrValues['userRole'];
		$orgId = $arrValues['orgId'];
		$arrResult['error'] = array();
		// see if username has been used already
		$boolValidUsername = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(is_array($fetch)) {
				// username exists in the db
				$boolValidUsername = false;
				$arrResult['error'][] = "the username already exists";
			}
			else {
				// username is available
				$boolValidUsername = true;
			}
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$boolValidUsername = false; // assume username is invalid if we get an exception
		}
		if(!$boolValidUsername) {
			$arrResult['success'] = false;
			return $arrResult;
		}
		// we have a valid username. So lets add it to the db
		 try {
			$data = array( 'username' => $username, 'password' => $hashedPassword, 'email' => $email, 'orgId' => $orgId, 'userRole' => $userRole);
			$STH = $this->dbo->prepare("INSERT INTO user VALUES (NULL, :username, :password, :email, :userRole, :orgId)");
			$STH->execute($data);
			$success = true;
			// TODO: now, based on the userRole, insert a new record into: member_info, chef_info, or admin_info
				//use same error checks as with the above insert query
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'][] = $e->getMessage();
		}
		// just send some stuff back to caller for debug
		$arrResult['success'] = $success;
		// below is for debug
		$arrResult['username'] = $username;
		$arrResult['hashed_password'] = $hashedPassword;
		$arrResult['email'] = $email;
		$arrResult['userRole'] = $userRole;
		return $arrResult;	
	}
	
	/**
		expected input: username and password pair
		
		output:
		$arrResult = array (
		'error' => exception object error message
		'success' => true if user was successfuly removed from db, false otherwise
		);
	*/
	public function deleteUser($username,$password) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(password_verify($password,$fetch['password'])){ //TODO: or if admin is deleting a user
				$STH = $this->dbo->prepare("DELETE FROM user WHERE username=:username");
				$STH->bindParam(":username", $username);
				$STH->execute();	
				$success = true;
			} else {
				$success = false;
				$arrResult['error'] = "not authorized to delete this acct";
			}
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$boolValidUsername = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}

	// id | username | password | email | userRole | orgId
	/**
		expected input: => values not being changed must be set to empty string
		$arrValues = array( 
		'username' => username,
		'password' => non-hashed user password
		'email' => user email address
		'userRole' => the users role
		
		output:
		$arrResult = array (
		'error' => exception object for query attempt
		'success' => true if successfully eddited, false otherwise
		);
	*/
	public function editUser($arrValues) {
	 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $username = $arrValues['username'];
	 $hashedPassword = password_hash($arrValues['password'], PASSWORD_BCRYPT);
	 $email = $arrValues['email'];
	 $userRole = $arrValues['userRole'];
	 $orgId = $arrValues['orgId'];
	 $sql = "UPDATE user SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($username, "") != 0) {
		 $sql = $sql . "username=?, ";
		 $data[$index] = $username;
		 $index = $index + 1;
	 }
	 if(strcmp($hashedPassword, "") != 0) {
		 $sql = $sql . "password=?, ";
		 $data[$index] = $hashedPassword;
		 $index = $index + 1;
	 }
	 if(strcmp($email, "") != 0) {
		 $sql = $sql . "email=?, ";
		 $data[$index] = $email;
		 $index = $index + 1;
	 }
	 if(strcmp($userRole, "") != 0) {
		 $sql = $sql . "userRole=?, ";
		 $data[$index] = $userRole;
		 $index = $index + 1;
	 }
	  if(strcmp($orgId, "") != 0) {
		 $sql = $sql . "orgId=?, ";
		 $data[$index] = $orgId;
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
		expected input: username and password pair
		
		output:
		$arrResult = array (
		'error_message' => invalid username and password pair
		'error' => exception object for first query attempt
		'userInfo' => the assoc array representing the users record in the db
		'success' => true if user was successfuly added, false otherwise
		);
	*/
	public function login($username, $password) {
		$success = false;
		$arrResult = array();	
		$arrResult['error_message'] = array();
		$arrResult['login'] = false;
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
		//	print_r($fetch);
			if(is_array($fetch)) {
				$hashedPassword = $fetch[0]['password'];
//				echo $hashedPassword;
				if(password_verify($password, $hashedPassword)) {
				// username exists in the database and pw hash compare returned true
				$arrResult['userInfo'] = $fetch[0]; // not sure what to return. just putting this here for now
				$arrResult['login'] = true; // the login had the correct credentials
				// find info specific to this type of user
				switch($fetch[0]['userRole']){
					case 0: //member
						//query user_info table and assign to ['member_info']
						break;
					case 1: //chef
						//query chef_info table and assign to ['chef_info']
						break;
					case 2: //admin
						//query admin_info table and assign to ['admin_info']
						break;
					default: 
						//throw error, somehow userRole isn't a number
						break;
				}
				$success = true;
			}
			else {
					$arrResult['error_message'][] = "invalid password";
					$success = false;
				}
			}
			else {
				// invalid username
				$arrResult['error_message'][] = "invalid username";
				$success = false;
			}
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; // assume username is invalid if we get an exception
		}
		if(!$success) {
			$arrResult['success'] = $success;
			return $arrResult;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
		/**
		expected input: => the id of the org to get users for
		
		output:
		$arrResult = array (
		'error' => exception object for query attempt
		'success' => true if successfully eddited, false otherwise
		'data' => array containing all users that are in the org
		);
	*/
	public function getUsersByOrgId($orgId) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE orgId=:orgId");
			$STH->bindParam(":orgId", $orgId);
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
}

?>
