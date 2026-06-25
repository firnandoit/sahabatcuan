<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - SahabatCuan</title>

    <!-- CSS (Letakkan di ATAS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .navbar-brand {
            font-weight: 800;
            letter-spacing: 1px;
        }

        .nav-link.active {
            font-weight: bold;
            color: #fff !important;
            border-bottom: 2px solid white;
        }

        .dropdown-item i {
            width: 20px;
        }
    </style>
</head>

<body class="bg-light">
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="/web/dashboard">SAHABATCUAN</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('web/dashboard') ? 'active' : '' ?>" href="/web/dashboard">Dashboard</a>
                    </li>

                    <?php if (can('manage_transactions')) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?= url_is('web/transactions*') ? 'active' : '' ?>" href="/web/transactions">Transaksi</a>
                        </li>
                    <?php endif; ?>

                    <?php if (can('manage_stocks')) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?= url_is('web/stocks*') ? 'active' : '' ?> text-warning" href="/web/stocks">
                                <i class="fas fa-database me-1"></i> Master Saham
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Pastikan permission 'manage_roles' ada di database -->
                    <?php if (can('manage_roles')) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?= url_is('web/roles*') ? 'active' : '' ?> text-info" href="/web/roles">
                                <i class="fas fa-user-shield me-1"></i> Hak Akses
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-bold" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= session()->get('name') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li>
                                <div class="dropdown-header">Role: <span class="badge bg-secondary"><?= ucfirst(session()->get('role_name')) ?></span></div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container">
        <!-- Notifikasi -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Content utama di sini -->
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="text-center mt-5 py-4 text-muted border-top bg-white">
        &copy; <?= date('Y') ?> <span class="text-primary fw-bold">SahabatCuan</span> - Portfolio Tracker
    </footer>

    <!-- JS SCRIPTS (Urutan ini WAJIB) -->
    <!-- 1. JQUERY DULUAN -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- 2. BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 3. DATATABLES -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- 4. LAINNYA -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- 5. TEMPAT UNTUK SCRIPT JAVASCRIPT DARI VIEW (PENTING!) -->
    <?= $this->renderSection('scripts') ?>

</body>

</html>