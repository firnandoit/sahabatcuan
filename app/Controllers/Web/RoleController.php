<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class RoleController extends BaseController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        return view('web/roles/index', ['title' => 'Manajemen Hak Akses']);
    }

    /**
     * Endpoint API untuk DataTables (JSON)
     */
    public function getRoleJson()
    {
        $data = $this->roleModel->getRolesWithPermissions();
        return $this->response->setJSON(['data' => $data]);
    }

    public function getPermissions($roleId)
    {
        $db = \Config\Database::connect();

        // Ambil semua daftar menu yang tersedia
        $all = $db->table('permissions')->get()->getResultArray();

        // Ambil daftar menu yang SUDAH dimiliki oleh role ini
        $active = $db->table('role_permissions')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        // Kita hanya butuh array ID saja: [1, 2, 4]
        $activeIds = array_column($active, 'permission_id');

        return $this->response->setJSON([
            'all'    => $all,
            'active' => $activeIds
        ]);
    }

    public function updatePermissions()
    {
        $db = \Config\Database::connect();
        $roleId = $this->request->getPost('role_id');
        $perms = $this->request->getPost('permissions'); // Array ID

        $db->transStart();
        // Hapus dulu semua izin lama
        $db->table('role_permissions')->where('role_id', $roleId)->delete();

        // Masukkan izin baru jika ada yang dicentang
        if (!empty($perms)) {
            $data = [];
            foreach ($perms as $pId) {
                $data[] = [
                    'role_id' => $roleId,
                    'permission_id' => $pId
                ];
            }
            $db->table('role_permissions')->insertBatch($data);
        }
        $db->transComplete();

        return $this->response->setJSON(['status' => 'success', 'message' => 'Hak akses berhasil diperbarui']);
    }
}
