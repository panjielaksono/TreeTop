<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel; // Pastikan Anda memiliki UserModel ini

class AuthController extends BaseController
{
    protected $user;

    function __construct()
    {
        helper('form');
        $this->user = new UserModel();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function login()
    {
        if ($this->request->getPost()) {
            $rules = [
                'username' => 'required|min_length[6]',
                'password' => 'required|min_length[7]|numeric', // Perhatikan: password sebagai numeric mungkin tidak umum, pastikan ini sesuai kebutuhan Anda
            ];

            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');

                $dataUser = $this->user->getUserByUsername($username);

                if ($dataUser) {
                    // Verifikasi password
                    // Pastikan password di database di-hash dengan password_hash()
                    if (password_verify($password, $dataUser['password'])) {
                        // SET ID PENGGUNA KE SESI DI SINI
                        session()->set([
                            'id'         => $dataUser['id'], // <--- BARIS INI DITAMBAHKAN
                            'username'   => $dataUser['username'],
                            'role'       => $dataUser['role'],
                            'isLoggedIn' => TRUE
                        ]);

                        // Redirect berdasarkan role jika diperlukan, atau ke home
                        return redirect()->to('/home');
                    } else {
                        // Password salah
                        session()->setFlashdata('failed', 'Kombinasi Username & Password Salah');
                        return redirect()->back();
                    }
                } else {
                    // Username tidak ditemukan
                    session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                    return redirect()->back();
                }
            } else {
                // Validasi input gagal (misal: username kurang dari 6 karakter)
                session()->setFlashdata('failed', $this->validator->listErrors());
                return redirect()->back();
            }
        }

        // Tampilkan form login
        return view('v_login');
    }
}
