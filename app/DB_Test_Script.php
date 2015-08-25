<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
require_once ('FeedModel.php');
require_once('OrgModel.php');
require_once('MenuModel.php');
require_once('UserModel.php');
// BEGIN testing of FeedModel
$feedModel = new FeedModel();

// add message
/*
$arrInsert = array("sender" => "10", "receiver" => "50", "message" => "second example");
$arrResult = $feedModel->addMessage($arrInsert);
echo print_r($arrResult);
*/
//get message

$arrResult = $feedModel->getMessages(array("id"=>50, "where_clause"=>"receiver=:id"));
print_r($arrResult);

//delete message
//$arrResult = $feedModel->deleteMessageById(50);
//print_r($arrResult);

// END testing of FeedModel

// START testing OrgModel
//$orgModel = new OrgModel();
// add Org
/*
$arrTest= array("name" => "Victor", "address" => "The Hub", "city" => "Madison", 
			"state" => "Wisconsin", "zip" => "53703", "phone" => "8605752115", 
			"email" => "vferrero14@gmail.com", "phone2" => "253535", "profileJSON" => "JSON"); 
$arrResult = $orgModel->createOrg($arrTest);
print_r($arrResult);
*/
// get OrgById
//$arrResult = $orgModel->getOrgById(1);
//print_r($arrResult);

// edit org
/*
$arrTest= array("id" => 1, "name" => "Sreenath", "address" => "", "city" => "Chicago", 
			"state" => "", "zip" => "", "phone" => "", 
			"email" => "", "phone2" => "", "profileJSON" => "");
$arrResult = $orgModel->editOrg($arrTest);
print_r($arrResult);
*/

//delete org
//$arrResult = $orgModel->deleteOrg(2);
//print_r($arrResult);
// END testing OrgModel

//START testing MenuModel
//$menuModel = new MenuModel();
/*
$arrCreateMenu = array('chef_id' => 1, 'week' => 1, 'day' => 2, 'approved' => 1);
$arrResult = $menuModel->createMenu($arrCreateMenu);
print_r($arrResult);
*/
/*
$arrResult = $menuModel->getMenu(array("id"=>1, "where_clause" => "approved=:id"));
print_r($arrResult);
*/
/*
$arrEdit = array('id' => 1, 'chef_id' => 15, 'week' => 5, 'day' => 3, 'approved' => 0);
$arrResult = $menuModel->editMenu($arrEdit);
print_r($arrResult);
*/ 
/*
$arrResult = $menuModel->deleteMenu(1);
print_r($arrResult);
*/ 
/*
$arrMenuItem = array("menu_id" => 1, "item_name" => "pasta", "meal" => 1);
$arrResult = $menuModel->createMenuItem($arrMenuItem);
print_r($arrResult);
*/
/*
$arrMenuItem = array("id" => 2, "menu_id" => 1, "item_name" => "lasagna", "meal" => 1);
$arrResult = $menuModel->editMenuItem($arrMenuItem);
print_r($arrResult);

$arrResult = $menuModel->deleteMenuItem(1);
print_r($arrResult);
 */
 /*
 $arrFeedback = array("feedback_type" => 2, "feedback_value" => 1, 
 "menu_item_id" => 2, "menu_id" => 1);
 $arrResult = $menuModel->createFeedback($arrFeedback);
 print_r($arrResult);
 */
 /*
 $arrFeedback = array("id"=>1, "feedback_type" => 2, "feedback_value" => "", 
 "menu_item_id" => "", "menu_id" => "");
 $arrResult = $menuModel->editFeedback($arrFeedback);
 print_r($arrResult);
 */
 /*
 $arrResult = $menuModel->deleteFeedback(1);
 print_r($arrResult);
 */
// END testing MenuModel
 
 // START testing UserModel
//  $userModel = new UserModel();
/*
 $arrUser = array("username" => "vf", "password" => "vf", 
		"email" => "vferrero14@gmail.com", "userRole" => 0, "orgId" => 1);
 $arrResult = $userModel->addUser($arrUser);
 print_r($arrResult);
 */
 //print_r($userModel->deleteUser("vferrero14", "11235813"));
 /*
 $arrUser = array("id" => 2, "username" => "", "password" => "", 
		"email" => "vferrero14@.com", "userRole" => 1, "orgId" => 2);
 $arrResult = $userModel->editUser($arrUser);
 print_r($arrResult);
 */ 
 /*
 $arrResult = $userModel->getUsersByOrgId(2);
 print_r($arrResult);
*/

//$arrResult = $userModel->login("vf", "vf");
//print_r($arrResult);

 // END testing UserModel
?>




