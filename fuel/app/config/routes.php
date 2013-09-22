<?php
return array(
	'_root_'                        => 'index/index', // The default route
	'(signup|signin|signout|about)' => 'index/$1',
	'(404)'                         => 'index/$1',
	'api/(:segment)/(:any)'         => '$1/api/$2',
	'docs/:connector/:type/:id'     => 'connector/docs',
	'_404_'                         => 'index/404', // The main 404 route
);