<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::login', ['filter' => 'auth']);

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->post('logout', 'AuthController::logout');

// product
$routes->group('product', ['filter' => 'auth'], function ($routes) { 
    $routes->get('', 'ProductController::index');
    $routes->post('', 'ProductController::create');
    $routes->post('edit/(:any)', 'ProductController::edit/$1');
    $routes->get('delete/(:any)', 'ProductController::delete/$1');
});

// user
$routes->get('/user', 'DashboardController::userDashboard', ['filter' => 'auth']);
$routes->get('/member', 'DashboardController::userMember', ['filter' => 'auth']);

//admin
$routes->get('/admin', 'DashboardController::adminDashboard', ['filter' => 'auth']);
$routes->get('/users', 'DashboardController::users', ['filter' => 'auth']);       
$routes->get('/membership', 'DashboardController::adminMember', ['filter' => 'auth']);       

$routes->get('/users/create', 'DashboardController::create', ['filter' => 'auth']); 
$routes->post('/users/store', 'DashboardController::store');                        
$routes->get('/users/edit/(:any)', 'DashboardController::edit/$1');                
$routes->post('/users/update/(:any)', 'DashboardController::update/$1');            
$routes->get('/users/delete/(:any)', 'DashboardController::delete/$1');            

$routes->get('/home', 'Home::index', ['filter' => 'auth']);  
