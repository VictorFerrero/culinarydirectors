<?php

class AdminPanelModel
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
		$var
		output:
		$res
	*/
	
	//login, lists, upsert, delete
	public function login($email, $pw){
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
					switch($userRole){
						case 0: //member
							//query member_info table
							$arrResult['error_message'][] = "invalid user role";
							$success = false;
							break;
						case 1: //chef
							//query chef_info table 
							$sql = "SELECT u.id, u.email, u.orgId, u.userRole, concat(u.fname , ' ' , u.lname) AS name, m.profileJSON ";
							$sql = $sql . "FROM user AS u INNER JOIN chef_info as m ON m.userId = u.id WHERE u.id=:userId";
							break;
						case 2: //admin
							//query admin_info table
							$sql = "SELECT u.id, u.email, u.orgId, u.userRole, concat(u.fname , ' ' , u.lname) AS name, m.profileJSON ";
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
					$fetch = $STH->fetch(PDO::FETCH_ASSOC);
					$arrResult['user'] = $fetch;
					//grab users, orgs, menus for admin or sadmin
					$lists = getListsForAdmin($fetch['userRole'],$fetch['orgId']);
					$arrResult['users'] = $lists['users'];
					$arrResult['orgs'] = $lists['orgs'];
					$arrResult['menus'] = $lists['menus'];
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
	public function getListsForAdmin($role_id, $org_id){
		$toReturn = array();
		switch($role_id){
			case 1: //chef or org admin - grab info for one org
				$sql = "SELECT * FROM org WHERE id=".$org_id;
				$STH = $this->dbo->prepare($sql);
				$STH->execute();
				$org = $STH->fetch(PDO::FETCH_OBJ);
				//copy all columns to obj
				$toReturn[0] = $org;
				//fetch menus and users for org, attach to ->users and ->menus
				$toReturn[0]->users
				$toReturn[0]->menus
				break;
			case 2: //superadmin - grab info for all orgs
				$sql = "SELECT * FROM org";
				$STH = $this->dbo->prepare($sql);
				$STH->execute();
				$orgs = $STH->fetchAll();
				for($i=0;$i<count($orgs);$i++){
					$org = $orgs[$i];
					//copy all columns to obj
					$toReturn[$i] = new stdClass();
					$toReturn[$i]->id = $org->id;
					//fetch menus and users for org, attach to ->users and ->menus
				}
				break;
			default:
				die("nope.");
				break;
		}
		return $toReturn;
	}
}

?>