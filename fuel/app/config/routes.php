<?php
return array(
	'_root_'  => 'index/index',  // The default route
	'signup'  => 'index/signup',
	'signin'  => 'index/signin',
	'signout' => 'index/signout',
	'api/(:segment)/(:any)' => '$1/api/$2',
	'docs/:connector/:type/:id' => 'connector/docs',
//	'_404_'   => 'welcome/404',    // The main 404 route
//	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);