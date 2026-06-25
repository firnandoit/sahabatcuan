<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Database;

class AuthController extends BaseController
{
    /**
     * Menampilkan halaman login
     */
    public function login()
    {
        // Jika sudah login, langsung arahkan ke dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/web/dashboard');
        }

        $data = [
            'title' => 'Login SahabatCuan'
        ];
        return view('auth/login', $data);
    }

    /**
     * Proses Verifikasi Login
     */
    public function attemptLogin()
    {
        $session = session();
        $userModel = new UserModel();
        $db = Database::connect();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // 1. Cari user berdasarkan email
        $user = $userModel->where('email', $email)->first();

        if ($user) {
            // 2. Verifikasi Password
            if (password_verify($password, $user['password'])) {

                // 3. Ambil data ROLE (Nama Role)
                $role = $db->table('roles')
                    ->where('id', $user['role_id'])
                    ->get()
                    ->getRowArray();

                // 4. Ambil semua PERMISSIONS milik role tersebut
                $permissionsData = $db->table('role_permissions rp')
                    ->select('p.name')
                    ->join('permissions p', 'p.id = rp.permission_id')
                    ->where('rp.role_id', $user['role_id'])
                    ->get()
                    ->getResultArray();

                // Ubah hasil query permission menjadi array satu dimensi
                // Contoh: ['manage_stocks', 'manage_transactions']
                $permissionList = array_column($permissionsData, 'name');

                // 5. Masukkan semua data penting ke SESSION
                $sessionData = [
                    'user_id'     => $user['id'],
                    'name'        => $user['name'],
                    'email'       => $user['email'],
                    'role_id'     => $user['role_id'],
                    'role_name'   => $role['name'] ?? 'user', // simpan 'admin' atau 'user'
                    'permissions' => $permissionList,        // simpan daftar izin
                    'logged_in'   => TRUE
                ];
                $session->set($sessionData);

                // 6. Redirect dengan pesan sukses
                $welcomeMsg = ($role['name'] == 'admin') ? 'Selamat Datang Admin!' : 'Selamat Datang User!';
                return redirect()->to('/web/dashboard')->with('success', $welcomeMsg);
            } else {
                // Password salah
                return redirect()->back()->with('error', 'Password yang Anda masukkan salah.');
            }
        } else {
            // Email tidak ditemukan
            return redirect()->back()->with('error', 'Email tidak terdaftar di sistem kami.');
        }
    }

    /**
     * Proses Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}
