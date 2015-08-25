<?php
require_once('DB_Connection.php');

// TODO: getMessagesByFromId and getMessagesByToId are not working. MySQL is throwing a syntax error.
//		i have no idea why. Something about that field/column is being weird in PDO?
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
		$to = $arrValues['to'];
		$from = $arrValues['from'];
	    $message = $arrValues['message'];
		 try {
			$data = array( 'to' => $to, 'from' => $from, 'message' => $message);
			$STH = $this->dbo->prepare("INSERT INTO feed VALUES (NULL, :to, :from, :message)");
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
	public function deleteMessageById($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM feed WHERE id=:id";
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
	
	//   set where clause like this to=:id, now we only need one function for getting messages/
	//		where_clause is only working with id=:id. 
	/**
		expected input: 
		$id
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	public function getMessagesById($id) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM feed WHERE id=:id");
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
		$toId
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	// not working. causes mysql syntax error
	public function getMessagesByToId($toId) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM feed WHERE to=:id");
			$STH->bindParam(":id", $toId);
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
		$fromId
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	// not working. causes mysql syntax error
	public function getMessagesByFromId($fromId) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM feed WHERE from=:fromId");
			$STH->bindParam(":fromId", $fromId);
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
