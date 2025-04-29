<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
        function __construct()
        {
            helper('form');
        }

        public function generatepassword()
        {
            echo password_hash('123', PASSWORD_DEFAULT);
        }


        public function logout()
        {
            session()->destroy();
            return redirect()->to('/login');
        }


        public function login()
        {
        // Jika ada request POST (form login dikirimkan)
        if ($this->request->getPost()) {
            // Ambil username dan password dari form login
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            // Membaca file JSON yang berisi data pengguna
            $users = json_decode(file_get_contents(WRITEPATH . 'users.json'), true);

            // Mencari user berdasarkan username
            foreach ($users as $user) {
                if ($user['username'] == $username) {
                    // Verifikasi password menggunakan password_verify
                    if (password_verify($password, $user['password'])) {
                        // Jika berhasil login, set session
                        session()->set([
                            'username' => $user['username'],
                            'role' => $user['role'],
                            'isLoggedIn' => true
                        ]);

                        // Redirect ke dashboard sesuai dengan role
                        return redirect()->to($user['role'] == 'admin' ? '/admin' : '/user');
                    } else {
                        // Jika password salah
                        session()->setFlashdata('failed', 'Password Salah');
                        return redirect()->back();
                    }
                }
            }

            // Jika username tidak ditemukan
            session()->setFlashdata('failed', 'Username Tidak Ditemukan');
            return redirect()->back();
        } else {
            // Menampilkan halaman login jika tidak ada request POST
            return view('v_login');
        }
      }

}
