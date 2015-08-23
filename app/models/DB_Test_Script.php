<?php
require_once "FeedModel.php";
require_once "OrgModel.php";
// BEGIN testing of FeedModel
$feedModel = new FeedModel();

// add message
$arrInsert = array("to" => "Victor", "from" => "Sreenath", "message" => "hello, world");
$arrResult = $feedModel->addMessage($arrInsert);
print_r($arrResult);
//get message
$arrGet = array("id" => 0, "where_clause" => "id=:id");
$arrResult = $feedModel->getMessages($arrGet);
print_r($arrResult);
//delete message
$arrDelete = array("id" => 0, "where_clause" => "id=:id");
$arrResult = $feedModel->deleteMessage
print_r($arrResult);
// END testing of FeedModel

// START testing OrgModel
$orgModel = new OrgModel();
// add Org
$arrTest= array("name" => "Victor", "address" => "The Hub", "city" => "Madison", 
			"state" => "Wisconsin", "zip" => "53703", "phone" => "8605752115", 
			"email" => "vferrero14@gmail.com", "phone2" => "253535", "profileJSON" => "JSON"); 
$arrResult = $orgModel->createOrg($arrTest);
print_r($arrResult);

// get OrgById
$arrResult = $orgModel->getOrgById(0);
print_r($arrResult);

// edit org
$arrTest= array("name" => "Sreenath", "address" => "", "city" => "Chicago", 
			"state" => "", "zip" => "", "phone" => "", 
			"email" => "", "phone2" => "", "profileJSON" => "");
$arrResult = $orgModel->editOrg($arrTest);
print_r($arrResult);

// get OrgById to see that the edit worked
$arrResult = $orgModel->getOrgById(0);
print_r($arrResult);
 
//delete org
$arrResult = $orgModel->deleteOrg(0);
print_r($arrResult);
// END testing OrgModel


?>
