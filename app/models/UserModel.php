<?php
require_once('DB_Connection.php');

	// id | username | password | email | userRole | orgId
class UserModel{
			
	private $dbo;
	
	 public function __construct() {
			$db = new DB_Connections()->getNewDBO();
			$this->dbo = $db;
	 }

	public function __destruct() {
		$this->dbo = null;
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
			$STH = $dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(password_verify($password,$fetch['password']){ //TODO: or if admin is deleting a user
				$STH = $dbo->prepare("DELETE FROM User WHERE username=:username");
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
	
	//TODO: get users by org ID
	//TODO: get true/false if user_id is in org_id

	/**
		expected input: 
		$arrValues = array( 
		'username' => username,
		'password' => non-hashed user password
		'email' => user email address
		'userRole' => the users role
		
		output:
		$arrResult = array (
		'username_error' => the username already exists
		'error1' => exception object for first query attempt
		'error2' => exception object for second query attempt
		'success' => true if user was successfuly added, false otherwise
		);
	*/
	public function addUser($arrValues) {
		// first we check if username already exists
		$arrResult = array();
		$success = false;
		$username = $arrValues['username'];
		$hashedPassword = password_hash($arrValues['password']);
		$email = $arrValues['email'];
		$userRole = $arrValues['userRole'];
		$orgId = $arrValues['orgId'];
		$arrResult['error'] = array();
		// see if username has been used already
		$boolValidUsername = false;
		 try {
			$STH = $dbo->prepare("SELECT * FROM user WHERE username=:username");
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
			$STH = $dbo->prepare("INSERT INTO user VALUES (NULL, :username, :password, :email, :userRole, :orgId)");
			$STH->execute($data);
			$success = true;
			//now, based on the userRole, insert a new record into: member_info, chef_info, or admin_info
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
	// id | username | password | email | userRole | orgId
	public function editUser($arrValues) {
			 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $username = $arrValues['username'];
	 $hashedPassword = password_hash($arrValues['password']);
	 $email = $arrValues['email'];
	 $userRole = $arrValues['userRole'];
	 $orgId = $arrValues['orgId'];
	 $sql = "UPDATE menu SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($username, "") != 0) {
		 $sql = $sql . "username=?, ";
		 $data[$index] = $username;
		 $index = $index + 1;
	 }
	 if(strcmp($hashedPassword, "") != 0) {
		 $sql = $sql . "password=?, ";
		 $data[$index] = $password;
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
		$success = false;
		 try {
			$STH = $dbo->prepare("SELECT * FROM user WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($fetch) && password_verify($password, $fetch["password"])) {
				// username exists in the database and pw hash compare returned true
				$arrResult['userInfo'] = $fetch; // not sure what to return. just putting this here for now
				// find info specific to this type of user
				switch($fetch['userRole']){
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
			}
			else {
				// invalid username/password combo
				$arrResult['error_message'] = "invalid username and password pair";
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
	
}

?>
