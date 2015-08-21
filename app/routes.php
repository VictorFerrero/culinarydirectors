<?php
/*Test Functions*/
$routePrefix = 'culinarydirectors/index.php/';
$router->get($routePrefix.'hello/{name}', function($name){
    return 'Hello ' . $name;
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->get($routePrefix.'helloworld', function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));
/*Main Reflective Functions*/
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