<?php
require_once('DB_Connection.php');

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
