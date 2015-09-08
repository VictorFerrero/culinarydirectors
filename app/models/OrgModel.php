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
			$arrResult['name'] = $name;
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
		 $STH = $this->dbo->prepare($sql);
		 $arrResult['db_result'] = $STH->execute($data);
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
	 public function deleteOrg($arrValues) {
		$arrResult = array();
		$success = false;
		$id = $arrValues['id'];
		$whereClause = $arrValues['where_clause'];
		$sql = "DELETE FROM org WHERE " . $whereClause;
		try {
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":id", $id);
			$arrResult['db_result'] = $STH->execute();
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
	 public function getOrg($arrValues) {
		 $success = false;
		 $arrResult = array();
		 $id = $arrValues['id'];
		 $whereClause = $arrValues['where_clause'];
		 $sql = "SELECT * FROM org WHERE " . $whereClause;
		 try {
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(':id', $id);
			$STH->execute();
			$row = $STH->fetch(PDO::FETCH_ASSOC);
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
