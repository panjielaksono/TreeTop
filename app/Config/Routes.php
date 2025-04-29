<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::login', ['filter' => 'auth']);

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->post('logout', 'AuthController::logout');

// user
$routes->get('/user', 'Home::userDashboard', ['filter' => 'auth']);
$routes->get('/member', 'Home::userMember', ['filter' => 'auth']);

//admin
$routes->get('/admin', 'Home::adminDashboard', ['filter' => 'auth']);
$routes->get('/users', 'Home::users', ['filter' => 'auth']);       
$routes->get('/membership', 'Home::adminMember', ['filter' => 'auth']);       

$routes->get('/users/create', 'Home::create', ['filter' => 'auth']); 
$routes->post('/users/store', 'Home::store');                        
$routes->get('/users/edit/(:any)', 'Home::edit/$1');                
$routes->post('/users/update/(:any)', 'Home::update/$1');            
$routes->get('/users/delete/(:any)', 'Home::delete/$1');            

