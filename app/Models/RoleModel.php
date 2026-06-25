<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description'];

    /**
     * Query SQL untuk mengambil Role dan daftar Permission-nya
     */
    public function getRolesWithPermissions()
    {
        return $this->db->table('roles r')
            ->select('r.id, r.name, GROUP_CONCAT(p.description SEPARATOR ", ") as menu_diizinkan')
            ->join('role_permissions rp', 'rp.role_id = r.id', 'left')
            ->join('permissions p', 'p.id = rp.permission_id', 'left')
            ->groupBy('r.id')
            ->get()
            ->getResultArray();
    }
}
