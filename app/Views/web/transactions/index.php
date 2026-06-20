<?= $this->extend('web/layout') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Riwayat Transaksi</h4>
    <a href="/web/transactions/add" class="btn btn-primary">+ Tambah Transaksi</a>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Ticker</th>
                    <th>Tipe</th>
                    <th>Qty (Lembar)</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t) : ?>
                    <tr>
                        <td><?= $t['transaction_date'] ?></td>
                        <td><strong><?= $t['ticker'] ?></strong></td>
                        <td>
                            <span class="badge <?= $t['type'] == 'BUY' ? 'bg-success' : 'bg-danger' ?>">
                                <?= $t['type'] ?>
                            </span>
                        </td>
                        <td><?= number_format($t['quantity']) ?></td>
                        <td>Rp <?= number_format($t['price_per_unit']) ?></td>
                        <td>Rp <?= number_format($t['total_amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>