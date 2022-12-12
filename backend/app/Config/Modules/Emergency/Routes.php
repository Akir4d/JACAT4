<?php
namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);

// AOP: This will send all undiscovered pages to Angular framework.
$routes->set404Override('App\Controllers\Home::getIndex');

// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.

// AOP: This is set to true to make implementation easier.
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// AOP: This will modify Angular's base url so that it may be redistributed everywhere.
$routes->get('/', 'Home::getIndex');
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