<?= $this->extend('web/layout') ?>

<?= $this->section('content') ?>
<!-- 1. KARTU RINGKASAN (SUMMARY) -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <small>Total Investasi</small>
                <h3>Rp <?= number_format($summary['total_modal']) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Nilai Portofolio Saat Ini</small>
                <h3>Rp <?= number_format($summary['total_value']) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Total Profit/Loss</small>
                <h3 class="<?= $summary['total_pl'] >= 0 ? 'text-success' : 'text-danger' ?>">
                    Rp <?= number_format($summary['total_pl']) ?>
                    <small style="font-size: 14px;">(<?= number_format($summary['pl_percent'], 2) ?>%)</small>
                </h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 2. TABEL PORTFOLIO -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white"><strong>Portofolio Saya</strong></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ticker</th>
                            <th>Lot</th>
                            <th>Avg Price</th>
                            <th>Last Price</th>
                            <th>Floating P/L</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($portfolio)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada data. Silakan <a href="/web/transactions">tambah transaksi</a>.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($portfolio as $p):
                            $pl_nominal = (($p['last_price'] ?? 0) - $p['average_price']) * $p['total_quantity'];
                            $color = ($pl_nominal >= 0) ? 'text-success' : 'text-danger';
                        ?>
                            <tr>
                                <td><strong><?= $p['ticker'] ?></strong></td>
                                <td><?= $p['total_quantity'] / 100 ?></td>
                                <td><?= number_format($p['average_price']) ?></td>
                                <td><?= number_format($p['last_price'] ?? 0) ?></td>
                                <td class="<?= $color ?> font-weight-bold">
                                    Rp <?= number_format($pl_nominal) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. PIE CHART -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white text-center"><strong>Alokasi Aset</strong></div>
            <div class="card-body">
                <?php if (!empty($portfolio)): ?>
                    <canvas id="portfolioChart"></canvas>
                <?php else: ?>
                    <p class="text-center text-muted">Data tidak tersedia</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if (!empty($portfolio)): ?>
        const ctx = document.getElementById('portfolioChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut', // Doughnut lebih modern daripada Pie
            data: {
                labels: [<?php foreach ($portfolio as $p) echo "'" . $p['ticker'] . "',"; ?>],
                datasets: [{
                    data: [<?php foreach ($portfolio as $p) echo $p['total_investment'] . ","; ?>],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                }]
            },
            options: {
                cutout: '70%'
            }
        });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>