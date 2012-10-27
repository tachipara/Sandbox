<?php
return array(
	'_root_'  => 'welcome/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route
	
	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),

    'user/:user_id' => array(
        array('GET', new Route('user/user')),
        array('POST', new Route('user/user')),
        array('PUT', new Route('user/user')),
        array('DELETE', new Route('user/user')),
    ),
);
