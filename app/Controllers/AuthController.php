<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

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
                'password' => 'required|min_length[7]|numeric',  
            ];

            
            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');

                
                $dataUser = $this->user->getUserByUsername($username);

                if ($dataUser) {
                   
                    if (password_verify($password, $dataUser['password'])) {
                        
                        session()->set([
                            'username' => $dataUser['username'],
                            'role' => $dataUser['role'],
                            'isLoggedIn' => TRUE
                        ]);

                       
                        return redirect()->to('/home');  
                    } else {
                    
                        session()->setFlashdata('failed', 'Kombinasi Username & Password Salah');
                        return redirect()->back();
                    }
                } else {
                 
                    session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                    return redirect()->back();
                }
            } else {
              
                session()->setFlashdata('failed', $this->validator->listErrors());
                return redirect()->back();
            }
        }

 
        return view('v_login');
    }

    public function register()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required|min_length[4]|is_unique[user.username]',
                'email' => 'required|valid_email|is_unique[user.email]',
                'password' => 'required|min_length[7]|numeric',
                'pass_confirm' => 'matches[password]'
            ];

            if (!$this->validate($rules)) {
                session()->setFlashdata('failed', $this->validator->listErrors());
                return redirect()->back()->withInput();
            }

            // Data untuk disimpan ke database
            $data = [
                'username' => $this->request->getPost('username'),
                'email'    => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'), // Password akan di-hash oleh Model
                'role'     => 'guest' // Default role untuk user baru
            ];

            // Mencoba menyimpan data ke database
            // Jika callback hashPassword di model aktif, password akan di-hash otomatis
            $inserted = $this->userModel->insert($data);         

            return redirect()->to('/login')->with('success', 'Registrasi berhasil, silakan login.');
        }

        return view('v_register');
    }

}
