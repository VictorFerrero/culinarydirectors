<?php
require_once('DB_Connection.php');
class MenuModel
{
	private $dbo;
		
	 public function __construct() {
			$db = new DB_Connections()->getNewDBO();
			$this->dbo = $db;
	 }

	public function __destruct() {
		$this->dbo = null;
	}

	public function createMenu($arrValues) {
		$arrResult = array();
		$success = false;		
		$chef_id = $arrValues['chef_id'];
		$week = $arrValues['week'];
	    $day = $arrValues['day'];
		$approved = $arrValues['approved'];
		 try {
			$data = array( 'chef_id' => $chef_id, 'week' => $week, 'day' => $day, 'approved' => $approved);
			$STH = $dbo->prepare("INSERT INTO Menu VALUES (NULL, :chef_id, :week, :day, :approved)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
		/* query

	$sql = "UPDATE books 
	        SET title=?, author=?
	        WHERE id=?";
	$q = $conn->prepare($sql);
	$q->execute(array($title,$author,$id));
	 */
	public function editMenu($arrValues) {
	 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $chef_id = $arrValues['chef_id'];
	 $week = $arrValues['week'];
	 $day = $arrValues['day'];
	 $approved = $arrValues['approved'];
	 $sql = "UPDATE Menu SET ";
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
	 $sql = substr($sql,0,-1);
	 $sql = $sql . " WHERE id=?";
	 $data[$index] = $id;
	try {
		 $stm = $dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	return $arrResult;
	}
	
	public function deleteMenu($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM Menu WHERE id=:id";
		try {
			$stm = $dbo->prepare($sql);
			$stm->bindParam(":id", $id);
			$arrResult['db_result1'] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error1'] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_success'] = $success;
		// now we will try to delete every menu item that is on this menu
		$success = false;
		$sql = "DELETE FROM Menu_Item WHERE menu_id=:menu_id";
		try{
			$stm = $dbo->prepare($sql);
			$stm->bindParam(":menu_id", $id);
			$arrResult['db_result2'] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error2'] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_item_success'] = $success;
		
		// now we need to delete all the feedback for that menu
		$success = false;
		$sql = "DELETE FROM Menu_Feedback WHERE menu_id=:menu_id";
		try{
			$stm = $dbo->prepare($sql);
			$stm->bindParam(":menu_id", $id);
			$arrResult['db_result3'] = $stm->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error3'] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_feedback_success'] = $success;
		return $arrResult;
	}
	
	public function createMenuItem($arrValues) {
		$arrResult = array();
		$success = false;
		$menu_id = $arrValues['menu_id'];
		$item_name = $arrValues['item_name'];
		$meal = $arrValues['meal'];
		 try {
			$data = array( 'menu_id' => $menu_id, 'item_name' => $item_name, 'meal' => $meal);
			$STH = $dbo->prepare("INSERT INTO Menu_Item VALUES (NULL, :menu_id, :item_name, :meal)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
	public function deleteMenuItem($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM Menu_Item WHERE id=:id";
		try{
			$stm = $dbo->prepare($sql);
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
	 // --each menu_item has id | menu_id | item_name | meal (0 for lunch, 1 for dinner)
	public function editMenuItem($arrValues) {
	 $arrResult = array();
	 $success = false;	
	 $id = $arrValues['id'];
	 $menu_id = $arrValues['menu_id'];
	 $item_name = $arrValues['item_name'];
	 $meal = $arrValues['meal'];
	 $sql = "UPDATE Menu_Item SET ";
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
		 $stm = $dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	return $arrResult;
	}
}
/*
--we need a menu_feedback table with id, feedback_type (enum, ["lateplate","noshow","thumbs"],
*  and feedback_value (1 for lateplate/noshow -- any entries in here mean "i do have a 
* lateplate/noshow", there is no entry in this table for other cases, 1 for thumbs up and 0 for thumbs down)
* we also need to have a field for menu_item_id
* can we also have a field for menu_id?? => will make deletes easier for deleting a menu
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
			$STH = $dbo->prepare("INSERT INTO Menu_Feedback VALUES (NULL, :feedback_type, :feedback_value, :menu_item_id, :menu_id)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
	public function editFeedback($arrValues) {
	 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $feedback_type = $arrValues['feedback_type'];
	 $feedback_value = $arrValues['feedback_value'];
	 $menu_item_id = $arrValues['menu_item_id'];
	 $sql = "UPDATE Menu_Feedback SET ";
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
	 // get rid of the last two characters 
	 $sql = substr($sql,0,-1);
	 $sql = $sql . " WHERE id=?";
	 $data[$index] = $id;
	try {
		 $stm = $dbo->prepare($sql);
		 $arrResult['db_result'] = $stm->execute($data);
		 $success = true;
     } catch (Exception $e) {
		 $arrResult['error'] = $e->getMessage();
		 $success = false;
	 }	
	$arrResult['success'] = $success;
	return $arrResult;
	}
	
	public function deleteFeedback($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM Menu_Feedback WHERE id=:id";
		try {
			$stm = $dbo->prepare($sql);
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
?>
