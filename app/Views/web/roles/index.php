<?= $this->extend('web/layout') ?>

<?= $this->section('content') ?>
<!-- CSS KHUSUS HALAMAN INI -->
<style>
    .badge-role {
        color: #198754;
        background-color: #e8f5e9;
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        display: inline-block;
    }

    .table thead th {
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
        padding: 15px;
    }

    .table tbody td {
        padding: 15px;
        border-bottom: 1px solid #f8f9fa;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* Gaya Modal Header */
    .modal-title-custom {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
    }

    .role-highlight {
        color: #198754;
        text-transform: uppercase;
    }

    /* Grid 2 Kolom untuk Permission */
    #permissionContainer {
        display: grid;
        grid-template-columns: repeat(2, 1);
        gap: 15px;
    }

    /* Custom Checkbox Hijau */
    .form-check-input:checked {
        background-color: #198754 !important;
        border-color: #198754 !important;
        box-shadow: none;
    }

    .form-check-label {
        font-weight: 500;
        color: #444;
        cursor: pointer;
    }

    .modal-content {
        border-radius: 15px;
    }

    .modal-footer {
        border-top: none;
        justify-content: center;
        padding-bottom: 25px;
    }

    /* Tombol Custom */
    .btn-simpan {
        background-color: #00a3ff;
        color: white;
        border: none;
        padding: 10px 30px;
        border-radius: 8px;
    }

    .btn-batal {
        background-color: #f8f9fa;
        color: #333;
        border: none;
        padding: 10px 30px;
        border-radius: 8px;
    }
</style>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4">Manajemen Hak Akses Menu Per Role</h5>

        <div class="table-responsive">
            <table id="tableRoles" class="table align-middle w-100">
                <thead class="bg-light">
                    <tr>
                        <th width="50">NO</th>
                        <th width="150">NAMA ROLE</th>
                        <th>MENU DIIZINKAN</th>
                        <th width="80" class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL EDIT HAK AKSES -->
<div class="modal fade" id="modalEditRole" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title-custom">Level Akses: <span id="roleNameLabel" class="role-highlight"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUpdateRole">
                <div class="modal-body p-4">
                    <input type="hidden" name="role_id" id="edit_role_id">
                    <h6 class="fw-bold mb-4">Pilih Menu yang Diizinkan:</h6>

                    <!-- Grid Checkbox 2 Kolom -->
                    <div class="row" id="permissionContainer">
                        <!-- Checkbox akan dimuat di sini oleh AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSimpanRole" class="btn btn-simpan">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let table;

    $(document).ready(function() {
        // 1. Inisialisasi DataTables
        table = $('#tableRoles').DataTable({
            "processing": true,
            "ajax": "/web/roles/get_json",
            "columns": [{
                    "data": null,
                    "render": (data, type, row, meta) => meta.row + 1
                },
                {
                    "data": "name",
                    "render": (data) => `<span class="badge-role">${data}</span>`
                },
                {
                    "data": "menu_diizinkan",
                    "render": (data) => data ? `<span class="text-muted small">${data}</span>` : `<span class="text-danger small"><i>Belum ada akses</i></span>`
                },
                {
                    "data": null,
                    "className": "text-center",
                    "render": function(row) {
                        return `
                        <button class="btn btn-light btn-sm text-muted shadow-sm border" onclick="editRole(${row.id}, '${row.name}')">
                            <i class="fas fa-pencil-alt"></i>
                        </button>`;
                    }
                }
            ],
            "dom": 'rtp',
        });

        // 2. Proses Simpan Perubahan via AJAX
        $('#formUpdateRole').on('submit', function(e) {
            e.preventDefault();
            $('#btnSimpanRole').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menunggu...');

            $.ajax({
                url: "/web/roles/update",
                method: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        $('#modalEditRole').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Berhasil!', res.message, 'success');
                    }
                },
                error: () => Swal.fire('Error', 'Gagal menyambung ke server', 'error'),
                complete: () => $('#btnSimpanRole').prop('disabled', false).text('Simpan Perubahan')
            });
        });
    });

    // 3. Fungsi Membuka Modal & Load Checkbox
    function editRole(id, name) {
        $('#edit_role_id').val(id);
        $('#roleNameLabel').text(name);
        $('#permissionContainer').html('<div class="col-12 text-center py-4"><div class="spinner-border text-primary"></div></div>');
        $('#modalEditRole').modal('show');

        // Ambil data izin
        $.get('/web/roles/get_permissions/' + id, function(res) {
            let html = '';

            // Pastikan res.active adalah array angka
            let activeIds = res.active.map(Number);

            res.all.forEach(perm => {
                // Cek apakah ID permission ini ada di dalam daftar yang aktif
                let isChecked = activeIds.includes(parseInt(perm.id)) ? 'checked' : '';

                html += `
            <div class="col-md-6 mb-3">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" 
                           name="permissions[]" 
                           value="${perm.id}" 
                           id="perm_${perm.id}" 
                           style="width: 24px; height: 24px;"
                           ${isChecked}>
                    <label class="form-check-label" for="perm_${perm.id}">
                        ${perm.description}
                    </label>
                </div>
            </div>`;
            });

            $('#permissionContainer').html(html);
        });
    }
</script>
<?= $this->endSection() ?>