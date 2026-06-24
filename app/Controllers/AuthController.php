<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        // Jika sudah login, langsung arahkan ke dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/web/dashboard');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        $model = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                // Set Session
                $sessionData = [
                    'user_id'   => $user['id'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'role'      => $user['role'],
                    'logged_in' => TRUE
                ];
                $session->set($sessionData);

                // Arahkan berdasarkan role
                if ($user['role'] == 'admin') {
                    return redirect()->to('/web/dashboard')->with('success', 'Selamat Datang Admin!');
                } else {
                    return redirect()->to('/web/dashboard')->with('success', 'Selamat Datang User!');
                }
            } else {
                return redirect()->back()->with('error', 'Password Salah');
            }
        } else {
            return redirect()->back()->with('error', 'Email tidak terdaftar');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
