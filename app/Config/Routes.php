<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Routes
$routes->get('/', 'Home::index', ['filter' => 'auth']);

// Auth Routes
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->post('logout', 'AuthController::logout');

// Product Routes
$routes->group('product', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'ProductController::index');
    $routes->post('', 'ProductController::create');
    $routes->post('edit/(:any)', 'ProductController::edit/$1');
    $routes->get('delete/(:any)', 'ProductController::delete/$1');
    $routes->get('download', 'ProductController::download');
});

// Guest Routes (User)
$routes->group('guest', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'GuestController::index');
    $routes->get('member', 'GuestController::userMember');
    $routes->post('membership/saveMembership', 'GuestController::saveMembership'); // Perbaikan rute
    $routes->get('profile', 'GuestController::profile'); 
    $routes->post('updateProfile', 'GuestController::updateProfile'); 
});

// Admin Routes
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'AdminController::index');

    // User Management Routes
    $routes->get('users', 'AdminController::users');
    $routes->get('users/create', 'AdminController::create');
    $routes->post('users/store', 'AdminController::store');
    $routes->get('users/edit/(:num)', 'AdminController::edit/$1');
    $routes->post('users/update/(:num)', 'AdminController::update/$1');
    $routes->get('users/delete/(:num)', 'AdminController::delete/$1');

    // Membership Management Routes
    $routes->get('membership', 'Membership::index');
    $routes->post('membership/save', 'Membership::save');
    $routes->get('membership/delete/(:num)', 'Membership::delete/$1');
    $routes->get('membership/deactivate/(:num)', 'Membership::deactivate/$1');

    // Transaction Management Routes
    $routes->get('transaksi/pending', 'AdminController::pendingTransactions');   
    $routes->get('transaksi/selesai', 'AdminController::completedTransactions'); 
    $routes->get('transaksi/dibatalkan', 'AdminController::canceledTransactions'); 
    $routes->get('transactions/cancel/(:num)', 'AdminController::cancelTransaction/$1');
});

// Midtrans Callback Routes
$routes->post('transaction/callback', 'TransactionController::callback');

// Cart Routes
$routes->group('keranjang', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'TransactionController::index');
    $routes->post('', 'TransactionController::cart_add');
    $routes->post('edit', 'TransactionController::cart_edit');
    $routes->get('delete/(:any)', 'TransactionController::cart_delete/$1');
    $routes->get('clear', 'TransactionController::cart_clear');
    
});

// Checkout and Transaction Routes
$routes->get('checkout', 'TransactionController::checkout', ['filter' => 'auth']);
$routes->post('buy', 'TransactionController::buy');
$routes->get('get-location', 'TransactionController::getLocation', ['filter' => 'auth']);
$routes->get('get-cost', 'TransactionController::getCost', ['filter' => 'auth']);
$routes->get('history', 'TransactionController::history');
$routes->post('transaction/delete/(:num)', 'TransactionController::delete/$1');


// Default Home Route
$routes->get('/home', 'Home::index', ['filter' => 'auth']);

// Cron Routes (Additional)
$routes->get('/send-membership-reminder', 'Cron::sendReminderManual');
$routes->get('/deactivate-expired', 'Cron::deactivateExpiredMembers');

$routes->get('register', 'RegisterController::index');
$routes->post('register/store', 'RegisterController::store');

?>
