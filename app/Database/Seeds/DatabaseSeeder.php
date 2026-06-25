<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan Foreign Key Check agar bisa menghapus data dengan aman
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');

        // 2. Bersihkan (Empty) semua tabel sebelum diisi ulang
        $this->db->table('role_permissions')->truncate();
        $this->db->table('permissions')->truncate();
        $this->db->table('roles')->truncate();
        $this->db->table('users')->truncate();

        // 3. Isi Tabel Roles
        $roles = [
            ['id' => 1, 'name' => 'admin', 'description' => 'Administrator System'],
            ['id' => 2, 'name' => 'user', 'description' => 'Nasabah/Investor']
        ];
        $this->db->table('roles')->insertBatch($roles);

        // 4. Isi Tabel Permissions
        $permissions = [
            ['id' => 1, 'name' => 'manage_stocks', 'description' => 'Akses Master Saham'],
            ['id' => 2, 'name' => 'manage_transactions', 'description' => 'Akses Transaksi']
        ];
        $this->db->table('permissions')->insertBatch($permissions);

        // 5. Hubungkan Role ke Permission (Tabel role_permissions)
        $role_permissions = [
            ['role_id' => 1, 'permission_id' => 1], // Admin -> Manage Stocks
            ['role_id' => 1, 'permission_id' => 2], // Admin -> Manage Transactions
            ['role_id' => 2, 'permission_id' => 2], // User -> Manage Transactions Only
        ];
        $this->db->table('role_permissions')->insertBatch($role_permissions);

        // 6. Isi Tabel Users (Hubungkan ke role_id)
        $users = [
            [
                'name'     => 'Admin SahabatCuan',
                'email'    => 'admin@gmail.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role_id'  => 1 // Admin
            ],
            [
                'name'     => 'User Biasa',
                'email'    => 'user@gmail.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role_id'  => 2 // User
            ],
        ];
        $this->db->table('users')->insertBatch($users);

        // 7. Hidupkan kembali Foreign Key Check
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        echo "Database Berhasil Di-seed!\n";
    }
}
