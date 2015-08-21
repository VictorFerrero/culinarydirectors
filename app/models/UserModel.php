<?php
require_once('DB_Connection.php');

	// assumed user table structure:
	// id | username | password | email | userRole
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
	public function deleteUser($username, $password) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $dbo->prepare("DELETE FROM User WHERE username=:username AND password=:password");
			$STH->bindParam(":username", $username);
			$STH->bindParam(":password", $this->hash($password));
			$STH->execute();	
			$success = true;
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$boolValidUsername = false; // assume username is invalid if we get an exception
		}
		$arrResult['success'] = $success;
		return $arrResult;
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
		$hashedPassword = $this->hash($arrValues['password']);
		$email = $arrValues['email'];
		$userRole = $arrValues['userRole'];
		// see if username has been used already
		$boolValidUsername = false;
		 try {
			$STH = $dbo->prepare("SELECT * FROM User WHERE username=:username");
			$STH->bindParam(":username", $username);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(is_array($fetch)) {
				// username exists in the db
				$boolValidUsername = false;
				$arrResult['username_error'] = "the username already exists";
			}
			else {
				// username is available
				$boolValidUsername = true;
			}
		} catch (Exception $e) {
			$arrResult['error1'] = $e->getMessage();
			$boolValidUsername = false; // assume username is invalid if we get an exception
		}
		if(!$boolValidUsername) {
			$arrResult['success'] = false;
			return $arrResult;
		}
		// we have a valid username. So lets add it to the db
		 try {
			$data = array( 'username' => $username, 'password' => $hashedPassword, 'email' => $email, 'userRole' => $userRole);
			$STH = $dbo->prepare("INSERT INTO User VALUES (NULL, :username, :password, :email, :userRole)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error2'] = $e->getMessage();
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
		'error_message' => invalid username and password pair
		'error' => exception object for first query attempt
		'userInfo' => the assoc array representing the users record in the db
		'success' => true if user was successfuly added, false otherwise
		);
	*/
	public function login($username, $password) {
		$success = false;
		$arrResult = array();	
		$hashedPassword = $this->hash($password);
		$success = false;
		 try {
			$STH = $dbo->prepare("SELECT * FROM User WHERE username=:username AND password=:password");
			$STH->bindParam(":username", $username);
			$STH->bindParam(":password", $hashedPassword);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($fetch)) {
				// username and password combo exist in the database
				$arrResult['userInfo'] = $fetch; // not sure what to return. just putting this here for now
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
//		$arrResult['hashed_password'] = $hashedPassword; 
		return $arrResult;
	}	
	
	// using sha1() hashing function
	private function hash($password) {
		$hashedPassword = sha1($password);
		return $hashedPassword;
	}
}

?>
