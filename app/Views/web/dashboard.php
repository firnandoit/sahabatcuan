<?= $this->extend('web/layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white font-weight-bold">Portofolio Saya</div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ticker</th>
                            <th>Lot</th>
                            <th>Avg Price</th>
                            <th>Last Price</th>
                            <th>Floating P/L</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($portfolio as $p):
                            $lot = $p['total_quantity'] / 100;
                            $pl_nominal = ($p['last_price'] - $p['average_price']) * $p['total_quantity'];
                            $pl_percent = ($p['average_price'] > 0) ? ($pl_nominal / $p['total_investment']) * 100 : 0;
                            $color = ($pl_nominal >= 0) ? 'text-success' : 'text-danger';
                        ?>
                            <tr>
                                <td><strong><?= $p['ticker'] ?></strong><br><small><?= $p['company_name'] ?></small></td>
                                <td><?= $lot ?></td>
                                <td>Rp <?= number_format($p['average_price']) ?></td>
                                <td>Rp <?= number_format($p['last_price']) ?></td>
                                <td class="<?= $color ?>">
                                    <?= number_format($pl_percent, 2) ?>% <br>
                                    <small>(Rp <?= number_format($pl_nominal) ?>)</small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white font-weight-bold">Alokasi Aset</div>
            <div class="card-body">
                <canvas id="portfolioChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('portfolioChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [<?php foreach ($portfolio as $p) echo "'" . $p['ticker'] . "',"; ?>],
            datasets: [{
                data: [<?php foreach ($portfolio as $p) echo $p['total_investment'] . ","; ?>],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
            }]
        }
    });
</script>
<?= $this->endSection() ?>