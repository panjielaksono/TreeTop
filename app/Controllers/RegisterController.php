<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class RegisterController extends Controller
{
    function __construct()
    {
        helper('form');
        $this->user = new UserModel();
    }

    public function index()
    {
        return view('register/v_register'); 
    }

    public function store()
    {
       
        $rules = [
            'username' => 'required|min_length[3]|is_unique[user.username]',
            'email'    => 'required|valid_email|is_unique[user.email]',
            'phone_number' => 'required|min_length[8]|max_length[20]',
            'password' => 'required|min_length[6]',
            'role'     => 'permit_empty', 
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check your input.');
        }

        // Mengambil data dari form
        $userModel = new UserModel();
        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role') ?: 'guest',  // Default ke 'guest' jika tidak ada pilihan
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Menyimpan data user ke dalam database
        if ($userModel->insert($data)) {
            return redirect()->to('/login')->with('success', 'Registration successful! You can now log in.');
        } else {
            return redirect()->back()->with('error', 'Registration failed.');
        }
    }

}
