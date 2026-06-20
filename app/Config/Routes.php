<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 1. DEFAULT ROUTE
// Langsung arahkan ke Dashboard Web saat buka localhost:8080
$routes->get('/', 'Web\DashboardController::index');

// 2. WEB ROUTES (Untuk Laptop/Browser)
$routes->group('web', function ($routes) {
    // Dashboard Utama
    $routes->get('dashboard', 'Web\DashboardController::index');

    // Manajemen Transaksi (Beli/Jual/Dividen)
    $routes->group('transactions', function ($routes) {
        $routes->get('/', 'Web\TransactionController::index');       // List transaksi
        $routes->get('add', 'Web\TransactionController::add');       // Form tambah
        $routes->post('store', 'Web\TransactionController::store');   // Proses simpan
        $routes->get('delete/(:num)', 'Web\TransactionController::delete/$1'); // Hapus
    });
});

// 3. API ROUTES (Khusus untuk Flutter - Output JSON)
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // Auth API
    $routes->post('login', 'AuthController::login');
    $routes->post('register', 'AuthController::register');

    // Data Portfolio (Untuk StreamBuilder di Flutter)
    $routes->get('portfolio', 'PortfolioController::index');

    // Input Transaksi dari HP
    $routes->post('transaction/store', 'TransactionController::store');
});

// 4. AUTOMATION ROUTES (Untuk Cron Job/Market Data)
$routes->group('market', function ($routes) {
    // Akses ini untuk update harga semua saham via Yahoo Finance API
    $routes->get('update', 'MarketController::updatePrices');
});
