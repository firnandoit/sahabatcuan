<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;

    public function login()
    {
        $model = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Berikan data JSON ke Flutter
            return $this->respond([
                'status'   => 'success',
                'message'  => 'Login Berhasil',
                'user'     => [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'role'  => $user['role'],
                    'email' => $user['email']
                ]
            ]);
        }

        return $this->failUnauthorized('Email atau Password salah');
    }
}
