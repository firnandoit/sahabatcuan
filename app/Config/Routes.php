<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. DEFAULT ROUTE (Langsung ke login jika belum ada session)
$routes->get('/', 'AuthController::login');

// --- AUTHENTICATION ROUTES (WEB) ---
$routes->get('login', 'AuthController::login');
$routes->post('auth/login', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

// 2. WEB ROUTES (Diproteksi dengan Filter 'auth')
$routes->group('web', ['filter' => 'auth'], function ($routes) {

    // Dashboard Utama: Bisa diakses semua orang yang sudah login
    $routes->get('dashboard', 'Web\DashboardController::index');

    // Manajemen Transaksi: Diproteksi izin 'manage_transactions'
    $routes->group('transactions', ['filter' => 'perm:manage_transactions'], function ($routes) {
        $routes->get('/', 'Web\TransactionController::index');

        // AJAX DATA & MODAL
        $routes->get('get_data', 'Web\TransactionController::getTransactions');
        $routes->post('store_ajax', 'Web\TransactionController::storeAjax');

        // Operasi lainnya
        $routes->get('delete/(:num)', 'Web\TransactionController::delete/$1');
    });

    // Master Saham: Diproteksi izin 'manage_stocks' (Hanya Admin)
    // Jika User biasa akses ini, akan muncul 404 (Page Not Found)

    $routes->group('stocks', ['filter' => 'perm:manage_stocks'], function ($routes) {
        $routes->get('/', 'Web\StockController::index');
        $routes->get('get_data', 'Web\StockController::getStocks');
        $routes->post('store', 'Web\StockController::store');
        $routes->get('delete/(:any)', 'Web\StockController::delete/$1');
    });
    $routes->group('roles', ['filter' => 'perm:manage_stocks'], function ($routes) {
        $routes->get('/', 'Web\RoleController::index');
        $routes->get('get_json', 'Web\RoleController::getRoleJson'); // Endpoint JSON
        $routes->get('get_permissions/(:num)', 'Web\RoleController::getPermissions/$1'); // Ambil data modal
        $routes->post('update', 'Web\RoleController::updatePermissions'); // Simpan perubahan
    });
});

// 3. API ROUTES (Untuk Flutter)
// Tidak menggunakan filter 'auth' web karena Flutter menggunakan metode lain (seperti Token)
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->post('login', 'Auth::login');
    $routes->post('register', 'Auth::register');

    // Data Portfolio
    $routes->get('portfolio', 'PortfolioController::index');

    // Input Transaksi dari HP
    $routes->post('transaction/store', 'TransactionController::store');
});

// 4. AUTOMATION ROUTES (Cron Job)
$routes->group('market', function ($routes) {
    $routes->get('update', 'MarketController::updatePrices');
});
