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

?>