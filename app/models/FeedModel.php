<?php
require_once('DB_Connection.php');
class FeedModel
{
	private $dbo;
		
	 public function __construct() {
			$db = new DB_Connections()->getNewDBO();
			$this->dbo = $db;
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
			$STH = $dbo->prepare("INSERT INTO Feed VALUES (NULL, :to, :from, :message)");
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
		$arrValues = array( 
		'id' => the id of the message to delete
		'whereClause' => sql for where clause. specify which id the one being passed in is
		)
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	public function deleteMessage($arrValues) {
		$id = $arrValues['id'];
		$whereClause = $arrValues['where_clause']; // id=:id
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM Feed WHERE " . $whereClause;
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
	
	//   set where clause like this to=:id, now we only need one function for getting messages
	
	/**
		expected input: 
		$arrValues = array( 
		'id' => the id of the message to delete
		'whereClause' => sql for where clause. specify which id the one being passed in is, 
						  should be 1 of following: id=:id, to=:id, from=:id
		)
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	public function getMessages($arrValues) {
		$id = $arrValues['id'];
		$whereClause = $arrValues['where_clause'];
		$arrResult = array();
		$success = false;
		 try {
			$STH = $dbo->prepare("SELECT * FROM Feed WHERE " . $whereClause);
			$STH->bindParam(":id", $id);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
	    return $arrResult;
	}
}

?>
