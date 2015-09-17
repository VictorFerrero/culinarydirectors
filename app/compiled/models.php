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
	public function login($email, $password){
		$userId = -1;
		$userRole = -1;
		$sql = "";
		$success = false;
		$arrResult = array();	
		$arrResult['error_message'] = array();

		 try {
			$STH = $this->dbo->prepare("SELECT password, id, userRole FROM user WHERE email=:email");
			$STH->bindParam(":email", $email);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC); // should we use fetch or fetchAll? should only be 1 record
			//die(var_dump($fetch));
			$debug = array();
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
					//die("GOT HERE");
					$lists = self::getListsForAdmin($fetch['userRole'],$fetch['orgId']);

					$arrResult['orgs'] = $lists;
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
	public function getLists($admin_id){
		$sql = "SELECT userRole, orgId FROM user WHERE id=".$admin_id;
		$STH = $this->dbo->prepare($sql);
		$STH->execute();
		$user = $STH->fetch(PDO::FETCH_OBJ);
		$arr = array(); $arr['lists'] = self::getListsForAdmin($user->userRole, $user->orgId);
		return $arr;
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
				//fetch chefs for org, attach to ->chefs
				$sql = "SELECT * FROM user WHERE userRole=1 AND orgId=".$org_id;
				$STH = $this->dbo->prepare($sql);
				$STH->execute();
				$chefs = $STH->fetchAll(PDO::FETCH_ASSOC);
				/*$chefs_clean = array();
				for($i=0;$i<count($chefs);$i++){
					$chef = $chefs[$i];
					$toAdd = new stdClass();
					$toAdd->
				}*/
				$toReturn[0]->chefs = $chefs;
				//fetch users for org, attach to ->users
				//IF chef, only get members
				$sql = "SELECT u.*,"
						."IF(u.userRole = 0,m_i.meal_plan,'') meal_plan,"
						."IF(u.userRole = 0,m_i.dietary_restrictions,'') dietary_restrictions,"
						."IF(u.userRole = 0,m_i.profileJSON,'') profileJSON_u,"
						."IF(u.userRole = 1,m_i.profileJSON,'') profileJSON_c,"
						."IF(u.userRole = 2,m_i.profileJSON,'') profileJSON_a "
						."FROM user AS u "
						."LEFT JOIN member_info AS m_i on m_i.userId = u.id "
						."LEFT JOIN admin_info AS a_i on a_i.userId = u.id "
						."LEFT JOIN chef_info AS c_i on c_i.userId = u.id "
						."WHERE u.orgId=".$org_id;
				$STH = $this->dbo->prepare($sql);
				$STH->execute();
				$users = $STH->fetchAll(PDO::FETCH_ASSOC);
				$toReturn[0]->users = $users;
				//fetch menus for org, attach to ->menus
				//IF chef, only get menus with their chef_id
				$sql = "SELECT menu.* FROM menu INNER JOIN user ON menu.chef_id = user.id
						WHERE user.orgId=".$org_id;
				$STH = $this->dbo->prepare($sql);
				$STH->execute();
				$menus = $STH->fetchAll(PDO::FETCH_ASSOC);
				$menus_with_items = array();
				for($i=0; $i<count($menus); $i++){
					$menu = $menus[$i];
					$sql = "SELECT * FROM menu_item WHERE menu_id=".$menu["id"];
					$STH = $this->dbo->prepare($sql);
					$STH->execute();
					$items = $STH->fetchAll(PDO::FETCH_ASSOC);
					$menu['items'] = $items;
					$menus_with_items[] = $menu;
				}
				$toReturn[0]->menus = $menus_with_items;
				break;
			case 2: //superadmin - grab info for all orgs
				$sql = "SELECT * FROM org";
				$STH = $this->dbo->prepare($sql);
				$STH->execute();
				$orgs = $STH->fetchAll(PDO::FETCH_OBJ);
				for($i=0;$i<count($orgs);$i++){
					//copy all columns to obj
					$toReturn[$i] = $orgs[$i];
					$org_id = $orgs[$i]->id;
					//fetch chefs for org, attach to ->chefs
					$sql = "SELECT * FROM user WHERE userRole=1 AND orgId=".$org_id;
					$STH = $this->dbo->prepare($sql);
					$STH->execute();
					$chefs = $STH->fetchAll(PDO::FETCH_ASSOC);
					$toReturn[$i]->chefs = $chefs;
					//fetch users for org, attach to ->users
					$sql = "SELECT u.*,"
							."IF(u.userRole = 0,m_i.meal_plan,'') meal_plan,"
							."IF(u.userRole = 0,m_i.dietary_restrictions,'') dietary_restrictions,"
							."IF(u.userRole = 0,m_i.profileJSON,'') profileJSON_u,"
							."IF(u.userRole = 1,m_i.profileJSON,'') profileJSON_c,"
							."IF(u.userRole = 2,m_i.profileJSON,'') profileJSON_a "
							."FROM user AS u "
							."LEFT JOIN member_info AS m_i on m_i.userId = u.id "
							."LEFT JOIN admin_info AS a_i on a_i.userId = u.id "
							."LEFT JOIN chef_info AS c_i on c_i.userId = u.id "
							."WHERE u.orgId=".$org_id;
					$STH = $this->dbo->prepare($sql);
					$STH->execute();
					$users = $STH->fetchAll(PDO::FETCH_ASSOC);
					$toReturn[$i]->users = $users;
					//fetch menus for org, attach to ->menus
					$sql = "SELECT menu.* FROM menu INNER JOIN user ON menu.chef_id = user.id
							WHERE user.orgId=".$org_id;
					$STH = $this->dbo->prepare($sql);
					$STH->execute();
					$menus = $STH->fetchAll(PDO::FETCH_ASSOC);
					$menus_with_items = array();
					for($j=0; $j<count($menus); $j++){
						$menu = $menus[$j];
						$sql = "SELECT * FROM menu_item WHERE menu_id=".$menu["id"];
						$STH = $this->dbo->prepare($sql);
						$STH->execute();
						$items = $STH->fetchAll(PDO::FETCH_ASSOC);
						$menu['items'] = $items;
						$menus_with_items[] = $menu;
					}
					$toReturn[$i]->menus = $menus_with_items;
				}
				break;
			default:
				die("nope.");
				break;
		}
		return $toReturn;
	}
	public function upsert($type, $action){
		switch($type){
			case "org":
				$org = new stdClass();
				if($action == "update"){
					$org->id = $_REQUEST['id'];
				}
				$org->name = $_REQUEST['name'];
				$org->address = $_REQUEST['address'];
				$org->city = $_REQUEST['city'];
				$org->state = $_REQUEST['state'];
				$org->zip = $_REQUEST['zip'];
				$org->phone = $_REQUEST['phone'];
				$org->email = $_REQUEST['email'];
				$org->phone2 = $_REQUEST['phone2'];
				$org->profileJSON = $_REQUEST['profileJSON'];
				$res = self::upsertOrg($org);
			case "user":
				$user = new stdClass();
				if($action == "update"){
					$user->id = $_REQUEST['id'];
				}
				$user->fname = $_REQUEST['fname'];
				$user->lname = $_REQUEST['lname'];
				$user->email = $_REQUEST['email'];
				$user->orgId = $_REQUEST['orgId'];
				$user->userRole = $_REQUEST['userRole'];
				$linked_obj = new stdClass();
				$linked_obj->profileJSON = $_REQUEST['profileJSON'];
				switch($user->userRole){
					case 0: //handle member-specific fields
						$linked_obj->meal_plan = $_REQUEST['meal_plan'];
						$linked_obj->dietary_restrictions = $_REQUEST['dietary_restrictions'];
						break;
					case 1: break;
					case 2: break;
					default: break;
				}
				
				$res = self::upsertUser($user, $linked_obj);
			case "menu":
				$menu = new stdClass();
				if($action == "update"){
					$menu->id = $_REQUEST['id'];
				}
				$menu->name = $_REQUEST['name'];
				$menu->approved = $_REQUEST['approved'];
				$menu->datestamp = $_REQUEST['datestamp'];
				$menu->chef_id = $_REQUEST['chef_id'];
				$itemsArr = (array)$_REQUEST['items'];
				$res = self::upsertMenu($menu, $itemsArr);
			default: die("error");
		}
		return $res;
	}
	public function upsertOrg($org){
		if(isset($org->id)){ //update
			$sql = "UPDATE org SET name=:name, address=:address, city=:city, state=:state, zip=:zip, phone=:phone, email=:email, phone2=:phone2, profileJSON=:profileJSON WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":id", $org->id);
			$STH->bindParam(":name", $org->name);
			$STH->bindParam(":address", $org->address);
			$STH->bindParam(":city", $org->city);
			$STH->bindParam(":state", $org->state);
			$STH->bindParam(":zip", $org->zip);
			$STH->bindParam(":phone", $org->phone);
			$STH->bindParam(":email", $org->email);
			$STH->bindParam(":phone2", $org->phone2);
			$STH->bindParam(":profileJSON", $org->profileJSON);
			$res = $STH->execute();
		} else { //insert
			$sql = "INSERT INTO org VALUES(NULL, :name, :address, :city, :state, :zip, :phone, :email, :phone2, :profileJSON)";
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":name", $org->name);
			$STH->bindParam(":address", $org->address);
			$STH->bindParam(":city", $org->city);
			$STH->bindParam(":state", $org->state);
			$STH->bindParam(":zip", $org->zip);
			$STH->bindParam(":phone", $org->phone);
			$STH->bindParam(":email", $org->email);
			$STH->bindParam(":phone2", $org->phone2);
			$STH->bindParam(":profileJSON", $org->profileJSON);
			$res = $STH->execute();
		}
		return $res;
	}
	public function upsertUser($user, $linked_obj){
		$res = array();
		if(isset($user->id)){ //update
			$sql = "UPDATE user SET fname=:fname, lname=:lname, email=:email, orgId=:orgId, userRole=:userRole WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":id", $user->id);
			$STH->bindParam(":fname", $user->fname);
			$STH->bindParam(":lname", $user->lname);
			$STH->bindParam(":email", $user->email);
			$STH->bindParam(":orgId", $user->orgId);
			$STH->bindParam(":userRole", $user->userRole);
			$res[] = $STH->execute();
			switch($user->userRole){
				case 0:
					$sql = "UPDATE member_info SET meal_plan=:meal_plan, dietary_restrictions=:dietary_restrictions, profileJSON=:profileJSON WHERE userId=:id";
					$STH = $this->dbo->prepare($sql);
					$STH->bindParam(":id", $user->id);
					$STH->bindParam(":meal_plan", $linked_obj->meal_plan);
					$STH->bindParam(":dietary_restrictions", $linked_obj->dietary_restrictions);
					$STH->bindParam(":profileJSON", $linked_obj->profileJSON);
					$res[] = $STH->execute(); break;
				case 1:
					$sql = "UPDATE chef_info SET profileJSON=:profileJSON WHERE userId=:id";
					$STH = $this->dbo->prepare($sql);
					$STH->bindParam(":id", $user->id);
					$STH->bindParam(":profileJSON", $linked_obj->profileJSON);
					$res[] = $STH->execute(); break;
				case 2:
					$sql = "UPDATE admin_info SET profileJSON=:profileJSON WHERE userId=:id";
					$STH = $this->dbo->prepare($sql);
					$STH->bindParam(":id", $user->id);
					$STH->bindParam(":profileJSON", $linked_obj->profileJSON);
					$res[] = $STH->execute(); break;
				default: die("error");
			}
		} else { //insert
			$sql = "INSERT INTO user VALUES(NULL, :name, :address, :city, :state, :zip, :phone, :email, :phone2, :profileJSON)";
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":name", $user->name);
			$STH->bindParam(":address", $user->address);
			$STH->bindParam(":city", $user->city);
			$STH->bindParam(":state", $user->state);
			$STH->bindParam(":zip", $user->zip);
			$STH->bindParam(":phone", $user->phone);
			$STH->bindParam(":email", $user->email);
			$STH->bindParam(":phone2", $user->phone2);
			$STH->bindParam(":profileJSON", $user->profileJSON);
			$res[] = $STH->execute();
			switch($user->userRole){
				case 0:
					$sql = "INSERT INTO member_info VALUES(NULL, :userId, :meal_plan, :dietary_restrictions, :profileJSON)";
					$STH = $this->dbo->prepare($sql);
					$STH->bindParam(":userId", $this->dbo->lastInsertId());
					$STH->bindParam(":meal_plan", $linked_obj->meal_plan);
					$STH->bindParam(":dietary_restrictions", $linked_obj->dietary_restrictions);
					$STH->bindParam(":profileJSON", $linked_obj->profileJSON);
					$res[] = $STH->execute(); break;
				case 1:
					$sql = "INSERT INTO chef_info VALUES(NULL, :userId, :profileJSON)";
					$STH = $this->dbo->prepare($sql);
					$STH->bindParam(":userId", $this->dbo->lastInsertId());
					$STH->bindParam(":profileJSON", $linked_obj->profileJSON);
					$res[] = $STH->execute(); break;
				case 2:
					$sql = "INSERT INTO admin_info VALUES(NULL, :userId, :profileJSON)";
					$STH = $this->dbo->prepare($sql);
					$STH->bindParam(":userId", $this->dbo->lastInsertId());
					$STH->bindParam(":profileJSON", $linked_obj->profileJSON);
					$res[] = $STH->execute(); break;
				default: die("error");
			}
		}
		return $res;
	}
	public function upsertMenu($menu, $items){
		//die(var_dump($items)."ASLDJALSKJDLASKJDLAJDAL".var_dump($items[0]['item_name'])); //to make sure array is being passsed in via JSON POST properly
		$res = array();
		if(isset($menu->id)){ //update
			$sql = "UPDATE menu SET chef_id=:chef_id, datestamp=:datestamp, approved=:approved, name=:name WHERE id=:id";
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":id", $menu->id);
			$STH->bindParam(":chef_id", $menu->chef_id);
			$STH->bindParam(":datestamp", $menu->datestamp);
			$STH->bindParam(":approved", $menu->approved);
			$STH->bindParam(":name", $menu->name);
			$res[] = $STH->execute();
			//delete existing items
			$sql = "DELETE FROM menu_item WHERE menu_id=:menu_id";
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":menu_id", $menu->id);
			$res[] = $STH->execute();
			$menu_id = $menu->id;
		} else { //insert
			$sql = "INSERT INTO menu VALUES (NULL, :chef_id, 0, 0, :datestamp, :approved, :name)";
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":chef_id", $menu->chef_id);
			$STH->bindParam(":datestamp", $menu->datestamp);
			$STH->bindParam(":approved", $menu->approved);
			$STH->bindParam(":name", $menu->name);
			$res[] = $STH->execute();
			$menu_id = $this->dbo->lastInsertId();
		}
		//re-insert menu items in all cases
		$MENU_ITEM_INSERT = $this->dbo->prepare("INSERT INTO menu_item VALUES(NULL, :menu_id, :item_name, :meal)");
		for($i=0; $i<count($items); $i++){
			$MENU_ITEM_INSERT->bindParam(":menu_id", $menu_id);
			$MENU_ITEM_INSERT->bindParam(":item_name", $items[$i]['item_name']);
			$MENU_ITEM_INSERT->bindParam(":meal", $items[$i]['meal']);
			$res[] = $MENU_ITEM_INSERT->execute();
		}
		return $res;
	}
}

?><?php

class DB_Connections
{
		
	 public function __construct() {
		
	 }
		
	public function getNewDBO() {
		$arrReturn = array();
		$success = false;
		$db = null;
		$arrCredentials = DatabaseConnectionStrings::getDBCredentials("local");
		
		$dsn = $arrCredentials['dsn'];
		$user = $arrCredentials['username'];
		$password = $arrCredentials['password'];
		$options = $arrCredentials['options'];
		
		try {
			 $db = new PDO($dsn, $user, $password);
			 $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // make PDO throw exceptions
			 $success = true;
		 } catch(Exception $e) {
			 $success = false;
		//	 $arrReturn['error'] = $e->getMessage();
		 }
	//	 $arrReturn['success'] = $success;
	//	 $arrReturn['DBO'] = $db;
		 return $db;
	}	
}

?>
<?php

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
		$creation = $arrValues['creation'];
		$reply_to = $arrValues['reply_to'];
	    $message = $arrValues['message'];
		 try {
			$data = array( 'sender' => $sender, 'receiver' => $receiver, 'creation' => $creation,
							'reply_to' => $reply_to, 'message' => $message);
			$STH = $this->dbo->prepare("INSERT INTO feed VALUES (NULL, :sender, :receiver, :creation, :reply_to, :message)");
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
		$arrValues['id'] => field identifying what is to be deleted
		$arrValues['where_clause'] => condition for where clause
		
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
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":id", $id);
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
<?php
class MenuModel
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
		'id' => id for where clause
		'where_clause' => must be of the form 'column'=:id
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly selected, false otherwise
		'data' => the array of menus which satisfied the where clause
		);
	*/
	public function getMenu($arrValues) {
		$id = $arrValues['id'];
		$whereClause = $arrValues['where_clause'];
		$arrResult = array();
		$success = false;
		$sql = "SELECT * FROM menu WHERE " . $whereClause;
		 try {
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":id", $id);
			$STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
			$success = true;
		} catch (Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false; 
		}
		$arrResult['success'] = $success;
	    return $arrResult;
	}
	/**
		expected input: 
		$arrValues = array( 
		'chef_id' => the id of the chef who is making the menu,
		'week' => the week of the year (0-52)
		'day' => day of the week (0-6)
		'approve' => 1 if menu is approved, 0 otherwise
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	public function createMenu($arrValues) {
		$arrResult = array();
		$success = false;		
		$name = $arrValues['name'];
		$chef_id = $arrValues['chef_id'];
		$week = $arrValues['week'];
	    $day = $arrValues['day'];
		$datestamp = $arrValues['datestamp'];
		$approved = $arrValues['approved'];
		 try {
			$data = array( 'name' => $name, 'chef_id' => $chef_id, 'week' => $week, 
			'day' => $day,'datestamp' => $datestamp,'approved' => $approved);
			$STH = $this->dbo->prepare("INSERT INTO menu VALUES (NULL,:name, :chef_id, :week, :day, :datestamp, :approved)");
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
	   for fields that are not being edditted, the associative array must still be set with the 
	   value of the empty string
		expected input: 
		$arrValues = array( 
		'id' => id of the menu being editted
		'chef_id' => the new id of the chef, "" otherwise
		'week' => the new week for this menu, (0-52)
		'day' => the new day of the week for this menu(0-6)
		'approve' => 1 if menu is approved, 0 otherwise
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
*/
	public function editMenu($arrValues) {
	 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $name = $arrValues['name']; 
	 $chef_id = $arrValues['chef_id'];
	 $week = $arrValues['week'];
	 $day = $arrValues['day'];
	 $datestamp = $arrValues['datestamp'];
	 $approved = $arrValues['approved'];
	 $sql = "UPDATE menu SET ";
	 $data = array();
	 $index = 0;
	  if(strcmp($name, "") != 0) {
		 $sql = $sql . "name=?, ";
		 $data[$index] = $name;
		 $index = $index + 1;
	 }
	 if(strcmp($chef_id, "") != 0) {
		 $sql = $sql . "chef_id=?, ";
		 $data[$index] = $chef_id;
		 $index = $index + 1;
	 }
	 if(strcmp($week, "") != 0) {
		 $sql = $sql . "week=?, ";
		 $data[$index] = $week;
		 $index = $index + 1;
	 }
	 if(strcmp($day, "") != 0) {
		 $sql = $sql . "day=?, ";
		 $data[$index] = $day;
		 $index = $index + 1;
	 }
	 if(strcmp($datestamp, "") != 0) {
		 $sql = $sql . "datestamp=?, ";
		 $data[$index] = $datestamp;
		 $index = $index + 1;
	 }
	 if(strcmp($approved, "") != 0) {
		 $sql = $sql . "approved=?, ";
		 $data[$index] = $approved;
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
	return $arrResult;
	}
	
		/**
		expected input: id of the menu being deleted
		
		output:
		$arrResult = array (
		'db_result1' => result of running delete query
		'error1' => exception object for the first db query (DELETE FROM Menu)
		'menu_success' => true if menu was successfuly removed, false otherwise
		'db_result2' => result of running second delete query (DELETE FROM Menu_Item)
		'error2' => exception object for the second db query
		'menu_item_success' => true if the menu items on this menu were successfully removed
		'db_result3' => result of running the third delete query (DELETE FROM Menu_Feedback)
		'error3' => the error message from running the 3 delete query
		'menu_feedback_success' => true if the feedback for this menu was removed
		);
	*/
	public function deleteMenu($id) {
		$arrResult = array();
		$arrResult['error'] = array();
		$arrResult['db_result'] = array();
		$success = false;
		$sql = "DELETE FROM menu WHERE id=:id";
		try {
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":id", $id);
			$arrResult['db_result'][] = $STH->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_success'] = $success;
		// now we will try to delete every menu item that is on this menu
		$success = false;
		$sql = "DELETE FROM menu_item WHERE menu_id=:menu_id";
		try{
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":menu_id", $id);
			$arrResult['db_result'][] = $STH->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_item_success'] = $success;
		
		// set menu_id = 0
		$success = false;
		$sql = "UPDATE menu_feedback SET menu_id=0, menu_item_id=0 WHERE menu_id=" . $id;
		try{
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":menu_id", $id);
			$arrResult['db_result'][] = $STH->execute();
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'][] = $e->getMessage();
			$success = false;
		}
		$arrResult['menu_feedback_success'] = $success;
		return $arrResult;
	}
	
	
	/**
		expected input: 
		$arrValues = array( 
		'menu_id' => the id of the menu that this item belongs on
		'item_name' => the name of the item
		'meal' => lunch or dinner?
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);

		//TODO: batch create function that uses a transaction instead of repeating inserts
	*/
	public function createMenuItem($arrValues) {
		$arrResult = array();
		$success = false;
		$menu_id = $arrValues['menu_id'];
		$item_name = $arrValues['item_name'];
		$meal = $arrValues['meal'];
		 try {
			$data = array( 'menu_id' => $menu_id, 'item_name' => $item_name, 'meal' => $meal);
			$STH = $this->dbo->prepare("INSERT INTO menu_item VALUES (NULL, :menu_id, :item_name, :meal)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
		 // --each menu_item has id | menu_id | item_name | meal (0 for lunch, 1 for dinner)
	 	/**
		// must use empty string for fields not being updated.
		expected input: 
		$arrValues = array( 
		'id' => id of the menu item being editted
		'menu_id' => the new id of the menu this item is on, "" otherwise
		'item_name' => the new item name, "" otherwise
		'meal' => lunch or dinner, "" otherwise
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
*/
	public function editMenuItem($arrValues) {
	 $arrResult = array();
	 $success = false;	
	 $id = $arrValues['id'];
	 $menu_id = $arrValues['menu_id'];
	 $item_name = $arrValues['item_name'];
	 $meal = $arrValues['meal'];
	 $sql = "UPDATE menu_item SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($menu_id, "") != 0) {
		 $sql = $sql . "menu_id=?, ";
		 $data[$index] = $menu_id;
		 $index = $index + 1;
	 }
	 if(strcmp($item_name, "") != 0) {
		 $sql = $sql . "item_name=?, ";
		 $data[$index] = $item_name;
		 $index = $index + 1;
	 }
	 if(strcmp($meal, "") != 0) {
		 $sql = $sql . "meal=?, ";
		 $data[$index] = $meal;
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
	return $arrResult;
	}
	
			/**
		expected input: id of the menu item being deleted
		
		output:
		$arrResult = array (
		'db_result' => result of running delete query
		'error' => exception object for the first db query 
		'success' => true if menu was successfuly removed, false otherwise
		);
	*/
	public function deleteMenuItem($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM menu_item WHERE id=:id";
		try{
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
	
	
/*	expected input: id of the menu to get all menu items on that menu
		
		output:
		$arrResult = array (
		'db_result' => result of running delete query
		'error' => exception object for the first db query 
		'success' => true if menu was successfuly removed, false otherwise
		);
	*/
	public function getMenuItemsForMenu($menuId) {
		$arrResult = array();
		$success = false;
		$sql = "SELECT * FROM menu_item WHERE menu_id=:menu_id";
		try {
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":menu_id", $menuId);
			$arrResult['db_result'] = $STH->execute();
			$fetch = $STH->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
/*
--we need a menu_feedback table with id, feedback_type (enum, ["lateplate","noshow","thumbs"],
*  and feedback_value (1 for lateplate/noshow -- any entries in here mean "i do have a 
* lateplate/noshow", there is no entry in this table for other cases, 1 for thumbs up and 0 for thumbs down)
* we also need to have a field for menu_item_id
* can we also have a field for menu_id?? => will make deletes easier for deleting a menu
	*/
	
	/**
		expected input: 
		$arrValues = array( 
		'feedback_type' => enum, ["lateplate","noshow","thumbs"]
		'feedback_value' => 1 for lateplate/noshow -- any entries in here mean "i do have a lateplate/noshow",
							there is no entry in this table for other cases, 1 for thumbs up and 0 for thumbs down
		'menu_item_id' => the id of the menu item that this feedback corresponds to
		'menu_id' => the id of the menu that this feedback is for (same menu item could be on a different menu, but done better?)
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
	*/
	public function createFeedback($arrValues) {
		$arrResult = array();
		$success = false;
		$feedback_type = $arrValues['feedback_type'];
		$feedback_value = $arrValues['feedback_value'];
		$menu_item_id = $arrValues['menu_item_id'];
		$menu_id = $arrValues['menu_id'];
		 try {
			$data = array( 'feedback_type' => $feedback_type, 'feedback_value' => $feedback_value, 'menu_item_id' => $menu_item_id, 'menu_id' => $menu_id);
			$STH = $this->dbo->prepare("INSERT INTO menu_feedback VALUES (NULL, :feedback_type, :feedback_value, :menu_item_id, :menu_id)");
			$STH->execute($data);
			$success = true;
		} catch (Exception $e) {
			$success = false;
			$arrResult['error'] = $e->getMessage();
		}
		$arrResult['success'] = $success;
		return $arrResult;
	}
	
 // --each menu_item has id | menu_id | item_name | meal (0 for lunch, 1 for dinner)
	 	/**
		// must use empty string for fields not being updated.
		expected input: 
		$arrValues = array( 
		'id' => id of the feedback being editted
		'feedback_type' => the new id of the menu this item is on, "" otherwise
		'feedback_value' => the new item name, "" otherwise
		'menu_item_id' => the new id of the menu item this feedback is for
		'menu_id' => the new id of the menu this feedback is for
		
		output:
		$arrResult = array (
		'error' => exception object for db query
		'success' => true if menu was successfuly created, false otherwise
		);
*/
	public function editFeedback($arrValues) {
	 $arrResult = array();
	 $success = false;
	 $id = $arrValues['id'];
	 $feedback_type = $arrValues['feedback_type'];
	 $feedback_value = $arrValues['feedback_value'];
	 $menu_item_id = $arrValues['menu_item_id'];
	 $menu_id = $arrValues['menu_id'];
	 $sql = "UPDATE menu_feedback SET ";
	 $data = array();
	 $index = 0;
	 if(strcmp($feedback_type, "") != 0) {
		 $sql = $sql . "feedback_type=?, ";
		 $data[$index] = $feedback_type;
		 $index = $index + 1;
	 }
	 if(strcmp($feedback_value, "") != 0) {
		 $sql = $sql . "feedback_value=?, ";
		 $data[$index] = $feedback_value;
		 $index = $index + 1;
	 }
	 if(strcmp($menu_item_id, "") != 0) {
		 $sql = $sql . "menu_item_id=?, ";
		 $data[$index] = $menu_item_id;
		 $index = $index + 1;
	 }
	  if(strcmp($menu_id, "") != 0) {
		 $sql = $sql . "menu_id=?, ";
		 $data[$index] = $menu_id;
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
	return $arrResult;
	}
	
			/**
		expected input: id of the feedback being deleted
		
		output:
		$arrResult = array (
		'db_result' => result of running delete query
		'error' => exception object for the first db query 
		'success' => true if menu was successfuly removed, false otherwise
		);
	*/
	public function deleteFeedback($id) {
		$arrResult = array();
		$success = false;
		$sql = "DELETE FROM menu_feedback WHERE id=:id";
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
/*	
	expected input: id of the menu to get feedback for
		
		output:
		$arrResult = array (
		'db_result' => result of running the query
		'error' => exception object for the first db query 
		'success' => true if menu was successfuly removed, false otherwise
		'data' => array containing all feedback for the given menu id
		);
	*/
	public function getFeedbackForMenu($menuId) {
		$arrResult = array();
		$success = false;
		$sql = "SELECT * FROM menu_feedback WHERE menu_id=:menu_id";
		try {
			$STH = $this->dbo->prepare($sql);
			$STH->bindParam(":menu_id", $menuId);
			$arrResult['db_result'] = $STH->execute();
			$fetch = $stm->fetchAll(PDO::FETCH_ASSOC);
			$arrResult['data'] = $fetch;
			$success = true;
		} catch(Exception $e) {
			$arrResult['error'] = $e->getMessage();
			$success = false;
		}
		$arrResult['success'] = $success;
		return $arrResult;	
	}
	
		public function getMenuForOrg($orgId) {
		$sql = "SELECT menu.* from menu INNER JOIN user ON menu.chef_id = user.id WHERE user.orgId=:orgId AND menu.approved=1 ORDER BY datestamp ASC";
		$arrResult = array();
		$success = false;
		$arrResult['data'] = array();
		 try {
			$STH = $this->dbo->prepare($sql);
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
}
?>
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
<?php
class TestModel{
	public static function getIndexText(){return "Hello World";}
}
?><?php
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
		// START function variables and objects declarations
		$userId = -1;
		$sql = "";
		$data = array();
		$arrResult = array();
		$arrResult['error'] = array();
		$success = false;
		// need these here for scoping reasons
		$email = $arrValues[0]['email']; 
		$hashedPassword = password_hash($arrValues[0]['password'], PASSWORD_BCRYPT);
		$userRole = $arrValues[0]['userRole'];
		$orgId = $arrValues[0]['orgId'];
		$fname = $arrValues[0]['fname'];
		$lname = $arrValues[0]['lname'];
		// prepare whatever statements we can, and bind parameters
		$INSERT_STH = $this->dbo->prepare("INSERT INTO user VALUES (NULL, :password, :email, :userRole, :orgId, :fname, :lname)");
		$INSERT_STH->bindParam(":password", $hashedPassword);
		$INSERT_STH->bindParam(":email", $email);
		$INSERT_STH->bindParam(":userRole", $userRole);
		$INSERT_STH->bindParam(":orgId", $orgId);
		$INSERT_STH->bindParam(":fname", $fname);
		$INSERT_STH->bindParam(":lname", $lname);
		$SELECT_ID_STH = $this->dbo->prepare("SELECT id FROM user WHERE email=:email"); 
		$SELECT_ID_STH->bindParam(":email", $email); // bind param binds by REFERENCE
		// END 
		// check to see if all emails are valid
		foreach($arrValues as $intIndex => $arrAssoc) {
			$email = $arrAssoc['email']; // this variable is binded to two prepared PDO statements
			// see if emails have been used already
			 try {
				$SELECT_ID_STH->execute(); // see if the email is already in the database
				$fetch = $SELECT_ID_STH->fetch(PDO::FETCH_ASSOC);
				if(is_array($fetch)) {
					// email exists in the db
					$arrValues[$intIndex]['validEmail'] = false; // mark this user record as not having valid email
					$arrResult['error'][] = "the email " . $email . " already exists";
				}
				else {
					// email is available. so we mark the record, and then add it to the database
					$arrValues[$intIndex]['validEmail'] = true;
					// get the other parameters necessary to add the user
					$hashedPassword = password_hash($arrAssoc['password'], PASSWORD_BCRYPT);
					$userRole = $arrAssoc['userRole'];
					$orgId = $arrAssoc['orgId'];
					$fname = $arrAssoc['fname'];
					$lname = $arrAssoc['lname'];
					 try {
						$INSERT_STH->execute(); // above params are already binded to the prepared statement
						// we need to get the id of the user we just added, so we can link the user table with the user_info tables
						$SELECT_ID_STH->execute();
						$fetch = $SELECT_ID_STH->fetch(PDO::FETCH_ASSOC);
						$userId = $fetch['id'];
						switch($userRole) {
							case 0: // frat member
								$sql = "INSERT INTO member_info VALUES (NULL, :userId, :meal_plan, :dietary_restrictions, :profileJSON)";
								$data = array('userId' => $userId, 'meal_plan' => $arrAssoc['meal_plan'],
										'dietary_restrictions' => $arrAssoc['dietary_restrictions'], 'profileJSON' => $arrAssoc['profileJSON']);
							break;
							case 1: // chef
								$sql = "INSERT INTO chef_info VALUES (NULL, :userId, :profileJSON)";
								$data = array('userId' => $userId, 'profileJSON' => $arrAssoc['profileJSON']);
							break;
							case 2: // admin
								$sql = "INSERT INTO admin_info VALUES (NULL, :userId, :profileJSON)";
								$data = array('userId' => $userId, 'profileJSON' => $arrAssoc['profileJSON']);
							break;
							default: 
							$arrResult['error'][] = "invalid user role. Can be 0, 1, 2";
							break;
						}
						// this statement depends on sql from switch statement, so lets keep it here
						$USER_INFO_STH = $this->dbo->prepare($sql);
						$USER_INFO_STH->execute($data);
						$success = true;
					} catch (Exception $e) {
						$success = false;
						$arrResult['error'][] = $e->getMessage();
					}
				}
			} catch (Exception $e) {
				$arrResult['error'][] = $e->getMessage();
			}
		}
		// just send some stuff back to caller for debug
		$arrResult['success'] = $success;
		$arrResult['values'] = $arrValues; // return so client can see if there were any invalid emails
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
	public function deleteUser($arrValues) {
		$email = $arrValues['email'];
		$password = $arrValues['password'];
		$userRole = $arrValues['userRole'];
		$arrResult = array();
		$arrResult['error'] = array();
		$success = false;
		 try {
			$STH = $this->dbo->prepare("SELECT * FROM user WHERE email=:email");
			$STH->bindParam(":email", $email);
			$STH->execute();
			$fetch = $STH->fetch(PDO::FETCH_ASSOC);
			if(is_array($fetch)) {
				$userId = $fetch['userId'];
				if(password_verify($password,$fetch['password']) || $userRole == 2){ //TODO: or if admin is deleting a user
					$STH = $this->dbo->prepare("DELETE FROM user WHERE email=:email");
					$STH->bindParam(":email", $email);
					$STH->execute();
					switch($userRole) {
						case 0:
							$sql = "DELETE FROM member_info WHERE userId=:userId";
						break;
					
						case 1:
							$sql = "DELETE FROM chef_info WHERE userId=:userId";
						break;
						
						case 2:
							$sql = "DELETE FROM admin_info WHERE userId=:userId";
						break;
						default:
							$arrResult['error'][] = "invalid user role";
						break;
					}
					$STH = $this->dbo->prepare($sql);
					$STH->bindParam(":userId", $userId);
					$STH->execute();
					$success = true;
				} else {
					$success = false;
					$arrResult['error'][] = "not authorized to delete this acct";
				}
			}
			else {
				$arrResult['error'][] = "email not found in the datbase";
			}
		} catch (Exception $e) {
			$arrResult['error'][] = $e->getMessage();
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
						$sql = "SELECT u.id, u.orgId, u.email, u.userRole, concat(u.fname , ' ' , u.lname) AS name, m.profileJSON ";
						$sql = $sql . "FROM user AS u INNER JOIN chef_info as m ON m.userId = u.id WHERE u.id=:userId";
						break;
					case 2: //admin
						//query admin_info table
						$sql = "SELECT u.id, u.orgId, u.email, u.userRole, concat(u.fname , ' ' , u.lname) AS name, m.profileJSON ";
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