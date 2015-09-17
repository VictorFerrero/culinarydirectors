var myApp = angular.module('myApp',[]);

myApp.controller('Main', ['$scope', '$http', function($scope,$http) {
  $scope.loggedIn=false;
  $scope.user = {};
  $scope.currentOrg = {id:-1};
  $scope.currentUser = {id:-1};
  $scope.currentMenu = {id:-1};
  $scope.currentMenu.items = [];
  $scope.currentMenu.approved = 0;
  $scope.allChefs = [];
  $scope.orgs = [];
  $scope.allChefs = [];
  $scope.subView = "Orgs";

  jQuery("#orgs_panel .panel-heading h3 span").click(function(){
  	console.log(jQuery(this).text() + " selected");
  	jQuery("#orgs_panel .panel-heading h3 span").removeClass("selected");
  	jQuery(this).addClass("selected");
  	$scope.subView = jQuery(this).text();
  	console.log($scope.subView);
  	$scope.$apply();
  });

  $scope.logOut = function(){
  	window.location.reload();
  };
  
  $scope.tryLogin = function(){
  	var em = jQuery("#loginEmail").val();
  	var pw = jQuery("#loginPw").val();
	console.log("INFO: logging in...");
	$http.post('../../index.php/adminpanel/login', {email:em, pw:pw}).
	  then(function(response) {
		console.log("INFO : login result");
		console.log(response);
		jQuery("#orgs")[0].disabled = false;
		$scope.user = response.data.user;
		$scope.orgs = response.data.orgs;
		$scope.loggedIn=true;
		console.log($scope);
		setTimeout(function(){
			jQuery("#orgs option[value='0']")[0].selected = true;
			jQuery("#orgs").change();
		},1000);
	  }, function(response) {
		console.log("ERROR: in adminpanel/login call-- "+response);
	  });
  };
  
  $scope.updateLists = function(){
	console.log("INFO: updating org/user/menu lists");
	$http.post('../../index.php/adminpanel/lists', {email:$scope.user.email}).
	  then(function(response) {
		//temporarily enable to trigger .change() after udpate 
		jQuery("#orgs")[0].disabled = false;
		var index = jQuery("#orgs option:selected").val();
		//$scope.user = response.user;
		$scope.orgs = response.data.orgs;
		setTimeout(function(){
			jQuery("#orgs option[value='0']")[0].selected = true;
			jQuery("#orgs").change();
			/*if($scope.user.userRole == 1){
				jQuery("#orgs")[0].disabled = true;
			}
			else {
				jQuery("#orgs")[0].disabled = false;
			}*/
		},1000);
	  }, function(response) {
		console.log("ERROR: in adminpanel/lists call-- "+response);
	  });
  };

  $scope.addItem = function(){
  	var value = (jQuery(".itemrow").length == 0)? "" : jQuery(jQuery(".itemrow").last().children()[0]).children("input")[0].value;
  	if (jQuery(".itemrow").length == 0 || value != ""){
	  	$scope.currentMenu.items.push({name:"",meal:0});
	  	setTimeout(function(){
	  		jQuery(jQuery(jQuery(".itemrow").last().children()[0]).children("input")[0]).focus();
	  	}, 500);
	}
  };
  
  $scope.changeOrg = function(){
	console.log("changeOrg called...");
	var index = jQuery("#orgs option:selected").val();
	if(index != -1){
		$scope.currentOrg = $scope.orgs[index];
		$scope.allChefs = ($scope.orgs[index].chefs.length > 0)? $scope.orgs[index].chefs : {};
		$scope.currentUser = ($scope.orgs[index].users.length > 0)? $scope.orgs[index].users[0] : {};
		$scope.currentMenu = ($scope.orgs[index].menus.length > 0)? $scope.orgs[index].menus[0] : {};
		setTimeout(function(){
			if($scope.orgs[index].users.length > 0){
				jQuery("#users option[value='0']")[0].selected = true;
			}
			if($scope.orgs[index].menus.length > 0){
				jQuery("#menus option[value='0']")[0].selected = true;
				jQuery("#currentMenuChef option[value="+$scope.currentMenu.chef_id+"]")[0].selected = true;
			}
			//if chef, not admin, set orgs to readonly
			if($scope.user.userRole == 1){
				jQuery("#orgs")[0].disabled = true;
			} else {
				jQuery("#orgs")[0].disabled = false;
			}
		},1500);
	} else {
		$scope.allChefs = [];
		$scope.currentOrg = {id:-1};
		$scope.currentUser = {id:-1};
		$scope.currentMenu = {id:-1};
		$scope.currentMenu.items = [];
		$scope.currentMenu.approved = 0;
	}
  };
  
  $scope.changeUser = function(){
	var index = jQuery("#users option:selected").val();
	if(index != -1) {
		$scope.currentUser = $scope.currentOrg.users[index];
	} else {
		$scope.currentUser = {id:-1};
	}
  };
  
  $scope.changeMenu = function(){
	var index = jQuery("#menus option:selected").val();
	if(index != -1) {
		$scope.currentMenu = $scope.currentOrg.menus[index];
	} else {
		$scope.currentMenu = {id:-1};
	    $scope.currentMenu.items = [];
	    $scope.currentMenu.approved = 0;
	}
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
	obj.action = (obj.id == -1)? "insert" : "update";
	obj.type = type;
	console.log("attempting upsert of:");
	console.log(obj);
	$http.post('../../index.php/adminpanel/upsert', obj).
	  then(function(response) {
		alert("saved object successfully!");
		console.log("INFO: saved org/user/menu successfully");
		$scope.updateLists();
	  }, function(response) {
		console.log("ERROR: in adminpanel/upsert call-- "+response);
	  });
  };
  
  $scope.remove = function(type){
	var data = new Object();
	var id = -1;
	switch(type){
		case "org": id = $scope.currentOrg.id; break;
		case "user": id = $scope.currentUser.id; break;
		case "menu": id = $scope.currentMenu.id; break;
		default: return false; break;
	}
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