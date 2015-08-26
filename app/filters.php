<?php
$startTime;
$router = new Phroute\Phroute\RouteCollector();
$router->filter('statsStart', function(){    
    global $startTime;
	$startTime = microtime(true);
});

$router->filter('basicAuth', function(){    
    //TODO:implement basic auth
});

$router->filter('statsComplete', function(){    
	global $startTime;
	//TODO: log Page load time in return json, maybe...
    //var_dump('Page load time: ' . (microtime(true) - $startTime));
    //$page_load_time = (microtime(true) - $startTime);
});
?>