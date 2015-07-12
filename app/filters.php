$startTime;
$router->filter('statsStart', function(){    
    $startTime = microtime(true);
});

$router->filter('basicAuth', function(){    
    //TODO:implement basic auth
});

$router->filter('statsComplete', function(){    
    var_dump('Page load time: ' . (microtime(true) - $startTime));
    //$page_load_time = (microtime(true) - $startTime);
});