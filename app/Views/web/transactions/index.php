<?= $this->extend('web/layout') ?>

<?= $this->section('content') ?>
<!-- HEADER & TOMBOL TAMBAH -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Riwayat Transaksi</h4>
        <p class="text-muted small">Kelola semua histori beli dan jual saham Anda.</p>
    </div>
    <!-- Tombol untuk membuka Modal -->
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTransaksi">
        <i class="fas fa-plus me-1"></i> Tambah Transaksi
    </button>
</div>

<!-- CARD TABEL -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tableTransaksi" class="table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Ticker</th>
                        <th>Tipe</th>
                        <th class="text-end">Lembar (Qty)</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data dimuat via AJAX DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH TRANSAKSI -->
<div class="modal fade" id="modalTransaksi" tabindex="-1" aria-labelledby="modalTransaksiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTransaksiLabel">Catat Transaksi Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTransaksi">
                <div class="modal-body">
                    <!-- Pilih Saham -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Saham</label>
                        <select name="ticker" class="form-select" required>
                            <option value="">-- Pilih Emiten --</option>
                            <?php foreach ($stocks as $s): ?>
                                <option value="<?= $s['ticker'] ?>"><?= $s['ticker'] ?> - <?= $s['company_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <!-- Tipe Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tipe</label>
                            <select name="type" class="form-select">
                                <option value="BUY">BELI (BUY)</option>
                                <option value="SELL">JUAL (SELL)</option>
                            </select>
                        </div>
                        <!-- Tanggal -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Jumlah Lembar -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jumlah Lembar</label>
                            <input type="number" name="quantity" class="form-control" placeholder="Contoh: 100" min="1" required>
                            <div class="form-text">1 Lot = 100 Lembar</div>
                        </div>
                        <!-- Harga per Lembar -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Harga / Lembar</label>
                            <input type="number" name="price_per_unit" class="form-control" placeholder="Contoh: 9500" min="1" required>
                        </div>
                    </div>

                    <!-- Fee Broker -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Broker Fee (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="broker_fee" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-0">
                        <label class="form-label fw-bold">Catatan (Notes)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Beli saat koreksi support"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSimpan" class="btn btn-primary px-4">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>

<!-- SCRIPTS (Pastikan jQuery & DataTables sudah dipanggil di layout) -->
<script>
    $(document).ready(function() {
        // 1. Inisialisasi DataTables
        let table = $('#tableTransaksi').DataTable({
            "processing": true,
            "serverSide": false, // Set true jika data sudah ribuan
            "order": [
                [0, "desc"]
            ], // Urutkan tanggal terbaru
            "ajax": "/web/transactions/get_data",
            "columns": [{
                    "data": "transaction_date"
                },
                {
                    "data": "ticker",
                    "render": function(data) {
                        return `<span class="fw-bold text-primary">${data}</span>`;
                    }
                },
                {
                    "data": "type",
                    "render": function(data) {
                        let color = (data === 'BUY') ? 'bg-success' : 'bg-danger';
                        return `<span class="badge ${color} px-3">${data}</span>`;
                    }
                },
                {
                    "data": "quantity",
                    "className": "text-end",
                    "render": $.fn.dataTable.render.number(',', '.', 0)
                },
                {
                    "data": "price_per_unit",
                    "className": "text-end",
                    "render": $.fn.dataTable.render.number(',', '.', 0, 'Rp ')
                },
                {
                    "data": "total_amount",
                    "className": "text-end fw-bold",
                    "render": $.fn.dataTable.render.number(',', '.', 0, 'Rp ')
                }
            ],
            "language": {
                "emptyTable": "Belum ada riwayat transaksi.",
                "processing": "Memuat data..."
            }
        });

        // 2. Kirim Form via AJAX
        $('#formTransaksi').on('submit', function(e) {
            e.preventDefault();

            // Animasi Tombol
            $('#btnSimpan').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

            $.ajax({
                url: "/web/transactions/store_ajax",
                method: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(response) {
                    if (response.status === 'success') {
                        // Tutup Modal
                        $('#modalTransaksi').modal('hide');
                        // Reset Form
                        $('#formTransaksi')[0].reset();
                        // Reload DataTables tanpa reload halaman
                        table.ajax.reload();
                        // Notifikasi (Bisa diganti SweetAlert2)
                        alert(response.message);
                    } else {
                        // Handle error validasi dari server
                        let errorMsg = "";
                        if (response.errors) {
                            errorMsg = Object.values(response.errors).join("\n");
                        } else {
                            errorMsg = 'Gagal menyimpan data.';
                        }
                        alert(errorMsg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Terjadi kesalahan sistem. Cek konsol browser.');
                },
                complete: function() {
                    $('#btnSimpan').prop('disabled', false).text('Simpan Transaksi');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>