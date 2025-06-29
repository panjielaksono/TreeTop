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
}
