<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::login');

// --- AUTHENTICATION ROUTES (WEB) ---
$routes->get('login', 'AuthController::login');          // Tampilan Form Login
$routes->post('auth/login', 'AuthController::attemptLogin'); // Proses Login
$routes->get('logout', 'AuthController::logout');        // Logout

// 2. WEB ROUTES (Diproteksi dengan Filter 'auth')
// Hanya user yang sudah login (session logged_in = true) yang bisa akses group ini
$routes->group('web', ['filter' => 'auth'], function ($routes) {

    // Dashboard Utama
    $routes->get('dashboard', 'Web\DashboardController::index');

    // Manajemen Transaksi
    $routes->group('transactions', function ($routes) {
        $routes->get('/', 'Web\TransactionController::index');
        $routes->get('add', 'Web\TransactionController::add');
        $routes->post('store', 'Web\TransactionController::store');
        $routes->get('delete/(:num)', 'Web\TransactionController::delete/$1');

        // AJAX DATATABLES & MODAL
        $routes->get('get_data', 'Web\TransactionController::getTransactions');
        $routes->post('store_ajax', 'Web\TransactionController::storeAjax');
    });
});

// 3. API ROUTES (Untuk Flutter)
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // Auth API - Arahkan ke Controller Api\Auth
    $routes->post('login', 'Auth::login');
    $routes->post('register', 'Auth::register');

    // Data Portfolio
    $routes->get('portfolio', 'PortfolioController::index');

    // Input Transaksi dari HP
    $routes->post('transaction/store', 'TransactionController::store');
});

// 4. AUTOMATION ROUTES
$routes->group('market', function ($routes) {
    $routes->get('update', 'MarketController::updatePrices');
});
