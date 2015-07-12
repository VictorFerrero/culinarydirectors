$router->get('/hello/{name}', function($name){
    return 'Hello ' . $name;
}, array('before' => 'statsStart', 'after' => 'statsComplete'));

$router->get('/helloworld'), function(){
	return Test::getIndex();
}, array('before' => 'statsStart', 'after' => 'statsComplete'));