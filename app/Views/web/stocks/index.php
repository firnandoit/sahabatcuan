<?= $this->extend('web/layout') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Master Data Saham & Reksadana</h5>
            <button class="btn btn-primary px-4 shadow-sm" onclick="addStock()">
                <i class="fas fa-plus me-1"></i> Tambah Emiten
            </button>
        </div>

        <div class="table-responsive">
            <table id="tableStocks" class="table align-middle w-100">
                <thead class="bg-light">
                    <tr>
                        <th>TICKER</th>
                        <th>NAMA PERUSAHAAN</th>
                        <th>KATEGORI</th>
                        <th>SEKTOR</th>
                        <th width="100" class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH/EDIT -->
<div class="modal fade" id="modalStock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Tambah Emiten</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formStock">
                <div class="modal-body p-4">
                    <input type="hidden" name="is_update" id="is_update" value="0">
                    <input type="hidden" name="old_ticker" id="old_ticker">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ticker / Kode</label>
                        <input type="text" name="ticker" id="stock_ticker" class="form-control text-uppercase" placeholder="Contoh: BBCA.JK" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Perusahaan</label>
                        <input type="text" name="company_name" id="stock_name" class="form-control" placeholder="PT Bank Central Asia Tbk." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="category" id="stock_category" class="form-select">
                                <option value="Saham">Saham</option>
                                <option value="Reksadana">Reksadana</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sektor</label>
                            <input type="text" name="sector" id="stock_sector" class="form-control" placeholder="Finance">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSimpanStock" class="btn btn-primary px-4">Simpan Emiten</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .badge-ticker {
        font-weight: 700;
        color: #0d6efd;
        background: #e7f1ff;
        padding: 5px 10px;
        border-radius: 4px;
    }

    #tableStocks thead th {
        font-size: 12px;
        color: #888;
        letter-spacing: 1px;
        padding: 15px;
    }

    #tableStocks tbody td {
        padding: 15px;
        border-bottom: 1px solid #f9f9f9;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let table;

    $(document).ready(function() {
        table = $('#tableStocks').DataTable({
            "ajax": "/web/stocks/get_data",
            "columns": [{
                    "data": "ticker",
                    "render": d => `<span class="badge-ticker">${d}</span>`
                },
                {
                    "data": "company_name",
                    "render": d => `<span class="fw-bold text-dark">${d}</span>`
                },
                {
                    "data": "category"
                },
                {
                    "data": "sector"
                },
                // Di dalam setting columns DataTable:
                {
                    "data": null,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        // Kita simpan semua data row ke dalam attribute data-
                        return `
            <button class="btn btn-light btn-sm border shadow-sm me-1" 
                onclick="editStock('${row.ticker}')">
                <i class="fas fa-pencil-alt text-muted"></i>
            </button>
            <button class="btn btn-light btn-sm border shadow-sm text-danger" 
                onclick="deleteStock('${row.ticker}')">
                <i class="fas fa-trash"></i>
            </button>`;
                    }
                }
            ],
            "dom": 'rtp'
        });

        $('#formStock').on('submit', function(e) {
            e.preventDefault();
            $('#btnSimpanStock').prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "/web/stocks/store",
                method: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status == 'success') {
                        $('#modalStock').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', res.message, 'success');
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                },
                complete: () => $('#btnSimpanStock').prop('disabled', false).text('Simpan Emiten')
            });
        });
    });

    function addStock() {
        $('#modalTitle').text('Tambah Emiten');
        $('#formStock')[0].reset();
        $('#is_update').val('0');
        $('#stock_ticker').prop('readonly', false);
        $('#modalStock').modal('show');
    }

    function editStock(ticker) {
        // Ambil data lengkap dari baris tabel berdasarkan ticker
        let rowData = table.rows().data().toArray().find(x => x.ticker === ticker);

        if (rowData) {
            $('#modalTitle').text('Edit Emiten');
            $('#is_update').val('1');
            $('#old_ticker').val(rowData.ticker); // Simpan ticker lama

            // Isi form dengan data yang benar
            $('#stock_ticker').val(rowData.ticker).prop('readonly', false);
            $('#stock_name').val(rowData.company_name);
            $('#stock_category').val(rowData.category);
            $('#stock_sector').val(rowData.sector);

            $('#modalStock').modal('show');
        }
    }

    function deleteStock(ticker) {
        Swal.fire({
            title: 'Hapus Emiten?',
            text: "Data " + ticker + " akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('/web/stocks/delete/' + ticker, function(res) {
                    table.ajax.reload();
                    Swal.fire('Terhapus!', res.message, 'success');
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>