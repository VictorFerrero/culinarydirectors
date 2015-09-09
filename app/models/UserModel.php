<?php
	// id | username | password | email | userRole | orgId
class UserModel{
			
	private $dbo; 
	
	 public function __construct() {
			$db = new DB_Connections();
			$this->dbo = $db->getNewDBO();
	 }

	public function __destruct() {
		$this->dbo = null;
	}

	/**
	*	True if the user denoted by $userId is also in the organization
	* 	that corresponds to $orgId 
	* */
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
			$success = true;
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
		$sql = "";
		$data = array();
		$userId = -1;
		$arrResult = array();
		$success = false;
		$hashedPassword = password_hash($arrValues['password'], PASSWORD_BCRYPT);
		$email = $arrValues['email'];
		$userRole = $arrValues['userRole'];
		$orgId = $arrValues['orgId'];
		$fname = $arrValues['fname'];
		$lname = $arrValues['lname'];
		$arrResult['error'] = array();
		// see if email has been used already
		$boolValidEmail = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE email=:email");
			$STH->bindParam(":email", $email);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(is_array($fetch)) {
				// email exists in the db
				$boolValidEmail = false;
				$arrResult['error'][] = "the email already exists";
			}
			else {
				// email is available
				$boolValidEmail = true;
			}
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$boolValidEmail = false; // assume email is invalid if we get an exception
		}
		if(!$boolValidEmail) {
			$arrResult['success'] = false;
			return $arrResult;
		}
		// we have a valid email. So lets add it to the db
		 try {
			$data = array('password' => $hashedPassword, 'email' => $email, 'orgId' => $orgId, 'userRole' => $userRole,
							'fname' => $fname, 'lname' => $lname);
			$STH = $this->dbo->prepare("INSERT INTO user VALUES (NULL, :password, :email, :userRole, :orgId, :fname, :lname)");
			$STH->execute($data);
			
			// we need to get the id of the user we just added, so we can link the user table with the user_info tables
			$sql = "SELECT * FROM user WHERE email=:email";
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":email", $email);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			$userId = $fetch['id'];
			switch($userRole) {
				case 0: // frat member
					$sql = "INSERT INTO member_info VALUES (NULL, :userId, :meal_plan, :dietary_restrictions, :profileJSON)";
					$data = array('userId' => $userId, 'meal_plan' => $arrValues['meal_plan'],
							'dietary_restrictions' => $arrValues['dietary_restrictions'], 'profileJSON' => $arrValues['profileJSON']);
				break;
				case 1: // chef
					$sql = "INSERT INTO chef_info VALUES (NULL, :userId, :profileJSON)";
					$data = array('userId' => $userId, 'profileJSON' => $arrValues['profileJSON']);
				break;
				case 2: // admin
					$sql = "INSERT INTO admin_info VALUES (NULL, :userId, :profileJSON)";
					$data = array('userId' => $userId, 'profileJSON' => $arrValues['profileJSON']);
				break;
				default: 
				$arrResult['error'][] = "invalid user role. Can be 0, 1, 2";
				break;
			}
			$STH = $this->dbo->prepare($sql);
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'][] = $e->getMessage();
		}
		// just send some stuff back to caller for debug
		$arrResult['success'] = $success;
		// below is for debug
		/*
		$arrResult['hashed_password'] = $hashedPassword;
		$arrResult['email'] = $email;
		$arrResult['userRole'] = $userRole;
		*/
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
	// should we pass in another variable for userRole??
	public function deleteUser($email,$password) {
		$arrResult = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE email=:email");
			$STH->bindParam(":email", $email);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(password_verify($password,$fetch['password'])){ //TODO: or if admin is deleting a user
				$STH = $this->dbo->prepare("DELETE FROM user WHERE email=:email");
				$STH->bindParam(":email", $email);
				$STH->execute();	
				$success = true;
			} else {
				$success = false;
				$arrResult['error'] = "not authorized to delete this acct";
			}
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
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
	 $password = $arrValues['password'];
	 $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
	 $email = $arrValues['email'];
	 $userRole = $arrValues['userRole'];
	 $orgId = $arrValues['orgId'];
	 $sql = "UPDATE user SET ";
	 $data = array();
	 $index = 0;
	 // go through all possible fields and construct the SET CLAUSE
	 if(strcmp($password, "") != 0) {
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
	 // get rid of the last two characters (", ")
	 $sql = substr($sql,0,-2);
	 $sql = $sql . " WHERE id=?"; // put together the where clause
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
	return $arrResult;
	}
	
	/**
		expected input: email and password pair
		
		output:
		$arrResult = array (
		'error_message' => invalid email and password pair
		'error' => exception object for first query attempt
		'userInfo' => the assoc array representing the users record in the db
		'success' => 
		);
	*/
	public function login($email, $password) {
		$userId = -1;
		$userRole = -1;
		$sql = "";
		$success = false;
		$arrResult = array();	
		$arrResult['error_message'] = array();
		$arrResult['login'] = false;
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT password, id, userRole FROM user WHERE email=:email");
			$STH->bindParam(":email", $email);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); // should we use fetch or fetchAll? should only be 1 record
			if(is_array($fetch)) {
				$hashedPassword = $fetch[0]['password'];
				if(password_verify($password, $hashedPassword)) {
				$userId = $fetch[0]['id']; // get userId for next query
				$userRole = $fetch[0]['userRole']; // used to put together the final query based on the users role
				// email exists in the database and pw hash compare returned true
				// put together sql query to get user profile
				switch($userRole){
					case 0: //member
						//query member_info table
						$sql = "SELECT u.id, u.orgId, u.email, u.userRole, concat(u.fname , ' ' , u.lname) AS name, m.meal_plan, m.dietary_restrictions, m.profileJSON ";
						$sql = $sql . "FROM user AS u INNER JOIN member_info as m ON m.userId = u.id WHERE u.id=:userId";
						break;
					case 1: //chef
						//query chef_info table 
						$sql = "SELECT u.id, u.email, u.userRole, concat(u.fname , ' ' , u.lname) AS name, m.profileJSON ";
						$sql = $sql . "FROM user AS u INNER JOIN chef_info as m ON m.userId = u.id WHERE u.id=:userId";
						break;
					case 2: //admin
						//query admin_info table
						$sql = "SELECT u.id, u.email, u.userRole, concat(u.fname , ' ' , u.lname) AS name, m.profileJSON ";
						$sql = $sql . "FROM user AS u INNER JOIN admin_info as m ON m.userId = u.id WHERE u.id=:userId";
						break;
					default: 
						//throw error, somehow userRole isn't a number
						throw new Exception("user role is not a valid number in the database");
						break;
				}
				$STH = $this->dbo->prepare($sql);
				$STH->bindParam(":userId", $userId);
				$STH->execute();
				$fetch = $STH->fetch(PDO::FETCH_ASSOC); // use fetch or fetchAll? there should only be 1 record
				$arrResult['user_profile'] = $fetch;
				$arrResult['login'] = true; // the login had the correct credentials				
				$success = true;
			}
			else {
					$arrResult['error_message'][] = "invalid password";
					$success = false;
				}
			}
			else {
				$arrResult['error_message'][] = "invalid username";
				$success = false;
			}
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; 
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
	
	// new password for my account in the database is 6928
	public function forgotPassword($email) {
		$arrResult = array();
		$arrResult['error'] = array();
		$success = false;
		$newPassword = rand(1000, 9999);
		 try {
		 // first we look for the record of this email in the user table
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE email=:email");
			$STH->bindParam(":email", $email);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC); // emails should be unique, use fetch instead of fetchAll
			$arrResult['data'] = $fetch;
			if(is_array($fetch)) { // we found a match
				$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
				$STH = $this->dbo->prepare("UPDATE user SET password=? WHERE id=?");
				$arrData = array();
				$arrData[0] = $hashedPassword;
				$arrData[1] = $fetch['id'];
				$arrResult['password_query'] = $STH->execute($arrData);
				// TODO: email formatting
				$msg = "Your new password is " . $newPassword . "\n";
				$msg = $msg . "Please change it to a longer, more secure password after logging in";
				mail($email, "Password Reset for Culinary Directors", $msg);
			}
			else { // no match
				$arrResult['error'][] = "email not found";
			}
			$success = true; // this will only be false if one of the queries caused an exception
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$success = false; 
		}
		$arrResult['success'] = $success;
		$arrResult['newPassword'] = $newPassword;
	    return $arrResult;
	}
}
?>