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
$routes->group('guest', ['filter' => 'auth'], function ($routes) { 
    $routes->get('', 'GuestController::index');
    $routes->get('member', 'GuestController::userMember');
});

//admin
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'AdminController::index');
    $routes->get('users', 'AdminController::users');
    $routes->get('users/create', 'AdminController::create');
    $routes->post('users/store', 'AdminController::store');
    $routes->get('users/edit/(:num)', 'AdminController::edit/$1');
    $routes->post('users/update/(:num)', 'AdminController::update/$1');
    $routes->get('users/delete/(:num)', 'AdminController::delete/$1');
    $routes->get('membership', 'AdminController::adminMember');
});

         

$routes->get('/home', 'Home::index', ['filter' => 'auth']);  
