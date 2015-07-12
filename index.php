<?php

	require 'vendor/autoload.php';
	require 'app/compiled/models.php';
	require 'app/compiled/controllers.php';
	require 'app/filters.php';
	require 'app/routes.php';
	
	# NB. You can cache the return value from $router->getData() so you don't have to create the routes each request - massive speed gains
	$routerData = (apc_fetch('routerData') === false)? $router->getData() : apc_fetch('routerData');
	$dispatcher = new Phroute\Phroute\Dispatcher($routerData);

	$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

	// Print out the value returned from the dispatched function
	echo $response;

?>