<!DOCTYPE html>
<html ng-app="myApp">
	<head>
		<!--our stuff - Angular logic and styles-->
		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="angular.min.js"></script>
		<script src="main.js"></script>
		<link rel="stylesheet" type="text/css" href="styles.css" />
		<!--Bootstrap-->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script-->
		<!--page metadata and attributes-->
		<title>Culinarydirectors / FMC Admin Panel</title>
		<style>
			.panel-heading span {cursor:pointer;}
			.panel-heading span.selected{ font-size:28px; }
		</style>
	</head>
	<body ng-controller="Main">
		<div id="login" class="panel panel-primary" ng-show="loggedIn==false">
			<div class="panel-heading">
				<h3>Login</h3>
			</div>
			<div class="panel-body">
				<center>
					<input type="text" id="loginEmail" class="form-control" />
					<input type="password" id="loginPw" class="form-control" />
					<input type="button" value="Login!" id="loginBtn" ng-click="tryLogin()" />
				</center>
			</div>
		</div>
		<div id="orgs_panel" class="panel panel-primary" ng-show="loggedIn==true">
			<div class="panel-heading">
				<h3><span>Orgs</span> |<span>Users</span> | <span>Menus</span></h3>
				<span ng-click="logOut()" style="float:right;position:relative;bottom:40px;">LogOut</span>
			</div>
			<div class="panel-body">
				<!--Orgs-->
				<span ng-show="subView=='Orgs'">
					<select id="orgs" ng-model="currentOrgId" ng-change="changeOrg()">
						<option value="-1">New</option>
						<option ng-repeat="org in orgs track by $index" value="{{$index}}">
							{{org.name}}
						</option>
					</select>
					<table>
						<tr><td>ID:</td><td><input type="text" class="form-control" ng-model="currentOrg.id" ng-show="currentOrg.id!=-1" readonly /></td></tr>
						<tr><td>Name:</td><td><input type="text" class="form-control" ng-model="currentOrg.name" /></td></tr>
						<tr><td>Address:</td><td><input type="text" class="form-control" ng-model="currentOrg.address" /></td></tr>
						<tr><td>City:</td><td><input type="text" class="form-control" ng-model="currentOrg.city" /></td></tr>
						<tr><td>State:</td><td><input type="text" class="form-control" ng-model="currentOrg.state" /></td></tr>
						<tr><td>Zip:</td><td><input type="text" class="form-control" ng-model="currentOrg.zip" /></td></tr>
						<tr><td>Phone:</td><td><input type="text" class="form-control" ng-model="currentOrg.phone" /></td></tr>
						<tr><td>Email:</td><td><input type="text" class="form-control" ng-model="currentOrg.email" /></td></tr>
						<tr><td>Phone2:</td><td><input type="text" class="form-control" ng-model="currentOrg.phone2" /></td></tr>
						<tr><td>Profile (JSON):</td><td><input type="text" class="form-control" ng-model="currentOrg.profileJSON" /></td></tr>
						<tr><td>Actions</td><td>
							<input type="button" value="Save!" ng-click="upsert('org')" />
							<input type="button" value="Delete!" ng-click="remove('org')" />
						</td></tr>
					</table>
				</span>
				<!--Users-->
				<span ng-show="subView=='Users'">
					<select id="users" ng-model="currentUserId" ng-change="changeUser()">
						<option value="-1">New</option>
						<option ng-repeat="user in currentOrg.users track by $index" value="{{$index}}">
							{{user.lname}},&nbsp;{{user.fname}}
						</option>
					</select>
					<table>
						<tr><td>ID:</td><td><input type="text" class="form-control" ng-model="currentUser.id" ng-show="currentUser.id!=-1" readonly /></td></tr>
						<tr><td>First Name:</td><td><input type="text" class="form-control" ng-model="currentUser.fname" readonly /></td></tr>
						<tr><td>Last Name:</td><td><input type="text" class="form-control" ng-model="currentUser.lname" readonly /></td></tr>
						<tr><td>Email:</td><td><input type="text" class="form-control" ng-model="currentUser.email" readonly /></td></tr>
						<tr><td>Org Id:</td><td><input type="text" class="form-control" ng-model="currentUser.orgId" readonly /></td></tr>
						<tr><td>User Role (0=member, 1=chef, 2=superadmin):</td><td><input type="text" class="form-control" ng-model="currentUser.userRole" readonly /></td></tr>
					</table>
						<div ng-switch on="currentUser.userRole">
							<div ng-switch-when="0"><!--member specific fields-->
							<table>
								<tr><td>Meal Plan:</td><td><input type="text" class="form-control" ng-model="currentUser.meal_plan" /></td></tr>
								<tr><td>Dietary Restrictions:</td><td><input type="text" class="form-control" ng-model="currentUser.dietary_restrictions" /></td></tr>
								<tr><td>profileJSON:</td><td><input type="text" class="form-control" ng-model="currentUser.profileJSON_u" /></td></tr>
							</table>
							</div>
							<div ng-switch-when="1"><!--chef specific fields-->
							<table>
								<tr><td>profileJSON:</td><td><input type="text" class="form-control" ng-model="currentUser.profileJSON_c" /></td></tr>
							</table>
							</div>
							<div ng-switch-when="2"><!--superadmin specific fields-->
							<table>
								<tr><td>profileJSON:</td><td><input type="text" class="form-control" ng-model="currentUser.profileJSON_a" /></td></tr>
							</table>
							</div>
							<div ng-switch-default></div>
						</div>
						<table>
							<tr><td>Actions</td><td>
								<input type="button" value="Save!" ng-click="upsert('user')" />
								<input type="button" value="Delete!" ng-click="remove('user')" />
							</td></tr>
						</table>
					</table>
				</span>
				<!--Menus-->
				<span ng-show="subView=='Menus'">
					<select id="menus" ng-model="currentMenuId" ng-change="changeMenu()">
						<option value="-1">New</option>
						<option ng-repeat="menu in currentOrg.menus track by $index" value="{{$index}}">
							{{menu.name}}
						</option>
					</select>
					<table>
						<tr><td>ID:</td><td><input type="text" class="form-control" ng-model="currentMenu.id" ng-show="currentMenu.id!=-1" readonly /></td></tr>
						<tr ng-if="user.userRole==2"><td>Approved?</td><td>Yes:<input type="radio" class="" ng-model="currentMenu.approved" value="1" /> <span style="float:right;">Not Yet:<input type="radio" class="" value="0" ng-model="currentMenu.approved" /></span></td></tr>
						<tr ng-if="user.userRole==1"><td>Approved By Admin?</td><td>{{currentMenu.approved}}<input type="hidden" ng-model="currentMenu.approved" /></td></tr>
						<tr><td>Name:</td><td><input type="text" class="form-control" ng-model="currentMenu.name" /></td></tr>
						<tr><td>Chef:</td><td>
							<select type="text" id="currentMenuChef" class="form-control" ng-model="currentMenu.chef_id">
								<option ng-repeat="chef in allChefs track by $index" value="{{chef.id}}">{{chef.fname}} &nbsp; {{chef.lname}}</option>
							</select>
						</td></tr>
						<tr><td>For Date:</td><td><input type="text" class="form-control" ng-model="currentMenu.datestamp" /></td></tr>
						<tr><td>Items:</td><td></td></tr>
						<!--list all current items for menu-->
						<tr class="itemrow" id="itemrow{{$index}}" ng-repeat="item in currentMenu.items track by $index">
							<td>Item {{$index}} Name: <input type="text" ng-model="item.item_name" /></td>
							<td>Meal: Lunch<input type="radio" ng-model="item.meal" value=0 />
							Dinner<input type="radio" ng-model="item.meal" value=1 />
							</td>
						</tr>
						<!--include 'blank' item, start typing and it:
							--adds new item to array of items for menu
							--focuses new item box instead of 'blank' item box
						-->
						<tr>
							<td colspan="2"><input type="text" ng-click="addItem()" value="" placeholder="click to add new item" /></td>
						</tr>
						<tr><td>Actions</td><td>
							<input type="button" value="Save!" ng-click="upsert('menu')" />
							<input type="button" value="Delete!" ng-click="remove('menu')" />
						</td></tr>
					</table>
				</span>
			</div>
		</div>
	</body>
</html>