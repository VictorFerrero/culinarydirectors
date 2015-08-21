<?php

class OrgModel{
		private $dbo;

	public function __construct() {
		$db = new DB_Connections()->getNewDBO();
		$this->dbo = $db;
	 }
	 
	 
	 /**
		expected input: the orgs id
		output:
		$arrResult = array (
		'data' => assoc array containing this orgs record from db
		'error' => exception object for db query
		'success' => true if org was successfuly retrieved from the db
		);
	*/
	 public function getOrgById($id) {
		 $success = false;
		 $arrResult = array();
		 
		 try {
			$stmt = $db->prepare("SELECT * FROM table WHERE id=:id");
			$stmt->bindValue(':id', $id);
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
