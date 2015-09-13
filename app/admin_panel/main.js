var myApp = angular.module('myApp',[]);

myApp.controller('Main', ['$scope', function($scope,$http) {
  $scope.loggedIn=false;
  $scope.user = {};
  $scope.currentOrg = {};
  $scope.currentUser = {};
  $scope.currentMenu = {};
  $scope.orgs = [];
  
  $scope.tryLogin = function(em,pw){
	console.log("INFO: logging in...");
	$http.post('../../index.php/adminpanel/login', {email:em, pw:pw}).
	  then(function(response) {
		console.log("INFO : login result");
		console.log(response);
		$scope.user = response.user;
		$scope.orgs = response.orgs;
		$scope.currentOrg = (response.orgs.length > 0)? response.orgs[0] : {};
		$scope.currentUser = (response.orgs[0].users.length > 0)? response.orgs[0].users[0] : {};
		$scope.currentMenu = (response.orgs[0].menus.length > 0)? response.orgs[0].menus[0] : {};
	  }, function(response) {
		console.log("ERROR: in adminpanel/login call-- "+response);
	  });
  };
  
  $scope.updateLists = function(){
	console.log("INFO: updating org/user/menu lists");
	$http.post('../../index.php/adminpanel/lists', {email:$scope.user.email}).
	  then(function(response) {
		$scope.orgs = response.orgs;
		$scope.currentOrg = (response.orgs.length > 0)? response.orgs[0] : {};
		$scope.currentUser = (response.orgs[0].users.length > 0)? response.orgs[0].users[0] : {};
		$scope.currentMenu = (response.orgs[0].menus.length > 0)? response.orgs[0].menus[0] : {};
	  }, function(response) {
		console.log("ERROR: in adminpanel/lists call-- "+response);
	  });
  };
  
  $scope.changeOrg = function(){
	var index = $("#orgs option:selected").val();
	$scope.currentOrg = $scope.orgs[index];
	$scope.currentUser = ($scope.orgs[index].users.length > 0)? $scope.orgs[index].users[0] : {};
	$scope.currentMenu = ($scope.orgs[index].menus.length > 0)? $scope.orgs[index].menus[0] : {};
  };
  
  $scope.changeUser = function(){
	var index = $("#users option:selected").val();
	$scope.currentUser = $scope.currentOrg.users[index];
  };
  
  $scope.changeMenu = function(){
	var index = $("#menus option:selected").val();
	$scope.currentMenu = $scope.currentOrg.menus[index];
  };
  
  $scope.upsert = function(type){
	switch(type){
		case "org":
			var toCopy = $scope.currentOrg; break;
		case "user":
			var toCopy = $scope.currentUser; break;
		case "menu":
			var toCopy = $scope.currentMenu; break;
		default: 
			var toCopy = {}; break;
	}
	var obj = angular.copy(toCopy);
	obj.action = (typeof obj.id === "undefined")? "insert" : "update";
	obj.type = type;
	$http.post('../../index.php/adminpanel/upsert', obj).
	  then(function(response) {
		alert("saved object successfully!");
		console.log("INFO: saved org/user/menu successfully");
		$scope.updateLists();
	  }, function(response) {
		console.log("ERROR: in adminpanel/upsert call-- "+response);
	  });
  };
  
  $scope.remove = function(id, type){
	var data = new Object();
	data.id = id; data.type = type;
	$http.post('../../index.php/adminpanel/delete', obj).
	  then(function(response) {
		alert("deleted object successfully!");
		console.log("INFO: deleted org/user/menu successfully");
		$scope.updateLists();
	  }, function(response) {
		console.log("ERROR: in adminpanel/delete call-- "+response);
	  });
  }
  
}]);