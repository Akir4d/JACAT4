<?php
//Workaround to make AutoRoute Improved works! 
$routes->options('emergency/(:any)', 'Emergency\Options::optionsHandler');

$routes->get('emergency', 'Emergency\Aop::getIndex');
$routes->get('emergency/aop', 'Emergency\Aop::getIndex');
$routes->get('emergency/aop/(:any)', 'Emergency\Aop::getIndex');
$routes->get('emergency/checks/dbConfig', '\App\Controllers\Emergency\Checks::getDbConfig'); 

$routes->post('emergency/login', 'Emergency\Login::postLogin');
$routes->post('emergency/login/checks', 'Emergency\Login::postChecks');
$routes->post('emergency/users/adminUser', 'Emergency\Users::postAdminUser');
$routes->post('emergency/envedit/dbTest', 'Emergency\Envedit::postDbTest'); 
$routes->post('emergency/envedit/saveDbDefault', 'Emergency\Envedit::postSaveDbDefault');