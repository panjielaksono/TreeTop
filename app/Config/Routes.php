<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index', ['filter' => 'auth']);

// Auth
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->post('logout', 'AuthController::logout');

// Product
$routes->group('product', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'ProductController::index');
    $routes->post('', 'ProductController::create');
    $routes->post('edit/(:any)', 'ProductController::edit/$1');
    $routes->get('delete/(:any)', 'ProductController::delete/$1');
    $routes->get('download', 'ProductController::download');
});

// Guest (User)
$routes->group('guest', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'GuestController::index');
    $routes->get('member', 'GuestController::userMember');
    // PERBAIKAN: Mengubah rute dari 'membership/save' menjadi 'membership/saveMembership'
    // agar sesuai dengan action form di v_userMember.php
    $routes->post('membership/saveMembership', 'GuestController::saveMembership');
});

// Admin
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'AdminController::index');

    // User management
    $routes->get('users', 'AdminController::users');
    $routes->get('users/create', 'AdminController::create');
    $routes->post('users/store', 'AdminController::store');
    $routes->get('users/edit/(:num)', 'AdminController::edit/$1');
    $routes->post('users/update/(:num)', 'AdminController::update/$1');
    $routes->get('users/delete/(:num)', 'AdminController::delete/$1');

    // Membership management
    $routes->get('membership', 'Membership::index');
    $routes->post('membership/save', 'Membership::save');
    $routes->get('membership/delete/(:num)', 'Membership::delete/$1');
    $routes->get('membership/deactivate/(:num)', 'Membership::deactivate/$1');
});

// Cart
$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TransactionController::index');
    $routes->post('', 'TransactionController::cart_add');
    $routes->post('edit', 'TransactionController::cart_edit');
    $routes->get('delete/(:any)', 'TransactionController::cart_delete/$1');
    $routes->get('clear', 'TransactionController::cart_clear');
});

// Checkout and transaction
$routes->get('checkout', 'TransactionController::checkout', ['filter' => 'auth']);
$routes->post('buy', 'TransactionController::buy');
$routes->get('get-location', 'TransactionController::getLocation', ['filter' => 'auth']);
$routes->get('get-cost', 'TransactionController::getCost', ['filter' => 'auth']);
$routes->get('history', 'TransactionController::history');
$routes->post('transaction/delete/(:num)', 'TransactionController::delete/$1');
$routes->post('coba-proses-pembayaran', 'TransactionController::cobaProsesPembayaran');

// Default home
$routes->get('/home', 'Home::index', ['filter' => 'auth']);
