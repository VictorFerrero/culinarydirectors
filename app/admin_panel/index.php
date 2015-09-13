<!DOCTYPE html>
<html>
	<head>
		<!--our stuff - Angular logic and styles-->
		<script src="angular.min.js"></script>
		<script src="main.js"></script>
		<link rel="stylesheet" type="text/css" href="styles.css" />
		<!--Bootstrap-->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<!--page metadata and attributes-->
		<title>Culinarydirectors / FMC Admin Panel</title>
	</head>
	<body ng-ctrl="Main">
		<div id="login" class="panel panel-green" ng-show="loggedIn==false">
			<input type="text" id="loginEmail" class="form-control" />
			<input type="text" id="loginPw" class="form-control" />
			<input type="button" id="loginBtn" ng-click="tryLogin()" />
		</div>
		
	</body>
</html>