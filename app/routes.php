<?php
/*Test Functions*/
$routePrefix = 'culinarydirectors/index.php/';
$router->get($routePrefix.'helloworld', function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

/*Webservice Functions*/
// start UserController
$router->post($routePrefix.'user/isUserInOrg', function(){
	$UserController = new UserController();
	return json_encode($UserController->isUserInOrg());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'user/login', function(){
	$UserController = new UserController();
	return json_encode($UserController->login());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'user/logout', function(){
	$UserController = new UserController();
	return json_encode($UserController->logout());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'user/getAllUsers', function(){
	$UserController = new UserController();
	return json_encode($UserController->getAllUsers());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'user/register', function(){
	$UserController = new UserController();
	return json_encode($UserController->register());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'user/deleteUser', function(){
	$UserController = new UserController();
	return json_encode($UserController->deleteUser());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

// Start FeedController
$router->post($routePrefix.'feed/addMessage', function(){
	$FeedController = new FeedController();
	return json_encode($FeedController->addMessage());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'feed/deleteMessageById', function(){
	$FeedController = new FeedController();
	return json_encode($FeedController->deleteMessageById());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'feed/getMessagesBySenderId', function(){
	$FeedController = new FeedController();
	return json_encode($FeedController->getMessagesBySenderId());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'feed/getMessagesByReceiverId', function(){
	$FeedController = new FeedController();
	return json_encode($FeedController->getMessagesByReceiverId());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'feed/getMessagesById', function(){
	$FeedController = new FeedController();
	return json_encode($FeedController->getMessagesById());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

// Start Org Controller
$router->post($routePrefix.'org/createOrg', function(){
	$OrgController = new OrgController();
	return json_encode($OrgController->createOrg());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'org/editOrg', function(){
	$OrgController = new OrgController();
	return json_encode($OrgController->editOrg());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'org/deleteOrg', function(){
	$OrgController = new OrgController();
	return json_encode($OrgController->deleteOrg());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'org/getOrgById', function(){
	$OrgController = new OrgController();
	return json_encode($OrgController->getOrgById());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

// Start Menu Controller
$router->post($routePrefix.'menu/createMenu', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->createMenu());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/editMenu', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->editMenu());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/deleteMenu', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->deleteMenu());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/createMenuItem', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->createMenuItem());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/deleteMenuItem', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->deleteMenuItem());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/editMenuItem', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->editMenuItem());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/createFeedback', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->createFeedback());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/deleteFeedback', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->deleteFeedback());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/editFeedback', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->editFeedback());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->post($routePrefix.'menu/getFeedbackForMenu', function(){
	$MenuController = new MenuController();
	return json_encode($MenuController->getFeedbackForMenu());
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
/*End Webservice Functions*/
/*BaaS - Main Reflective Functions*/
//SELECT and SELECT-like API calls
/*
Simple select
by pname=value, or SELECT ALL if * in pvalue (pname disregarded but required)
	i.e. get/table/field1/value1 retrieves the record(s) in table where field1=value1
	i.e. get/users/id/1 retrieves the user with id=1
	i.e. get/users/id/* retrieves the users regardless of id (as in, all users)
	can specify operand to act on field1 and value1, like in filter, except in request param 'operand' since only one filter
*/
$router->get($routePrefix.'get/{table}/{pname}/{pvalue}', function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
/*
Comprehensive select
each filter in filterArr is pname:pvalue:operand (optional)
	i.e. id:5 for getting a single table row by id, if operand is included (= > < <= >=), applies to id and 5.
	i.e. status:1 for getting all rows with status of 1, same as status:1:=. status:1:> returns all rows with status > 1
	i.e. id:* or anything:* means select and return all rows, operand disregarded
*/
$router->get($routePrefix.'get/{table}/{filterArr}', function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
//INSERT/UPDATE and INSERT/UPDATE-like API calls
/*
Simple insert/update
	inserts or updates row in table with attributes objectArr
	if identifier in objectArr exists in table, update existing row with values in objectArr
	if not, insert new row with values in objectArr and auto-generated identifier
*/
$router->post($routePrefix.'post/{table}/{objectArr}', function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
//DELETE and DELETE-like API calls
/*
Simple Delete
	deletes row in table by id
*/
$router->delete($routePrefix.'delete/{table}/{id}', function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
?>
