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
