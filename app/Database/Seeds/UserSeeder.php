<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan pengecekan Foreign Key
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 2. Sekarang baru bisa truncate tabel users
        $this->db->table('users')->truncate();

        $data = [
            [
                'name'       => 'Admin SahabatCuan',
                'email'      => 'admin@gmail.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'User Biasa',
                'email'      => 'user@gmail.com',
                'password'   => password_hash('user123', PASSWORD_DEFAULT),
                'role'       => 'user',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);

        // 3. Hidupkan kembali pengecekan Foreign Key
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        echo "UserSeeder berhasil dijalankan (Data lama dibersihkan)!\n";
    }
}
