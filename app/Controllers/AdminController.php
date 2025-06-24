<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/guest');
        }
    
        $userModel = new \App\Models\UserModel();
    
        $userCount = $userModel->countAll(); 
        $adminCount = $userModel->where('role', 'admin')->countAllResults();
        $guestCount = $userModel->where('role', 'guest')->countAllResults();
    
        $adminData = [
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'userCount' => $userCount,
            'adminCount' => $adminCount,
            'guestCount' => $guestCount
        ];
    
        return view('v_dashboard_admin', $adminData);
    }

    public function users()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/guest');
        }

        $users = $this->userModel->findAll();

        return view('v_users', ['users' => $users]); 
    }

    public function create()
    {
        return view('v_create_user');
    }

    public function store()
    {
        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
    
        $userModel = new \App\Models\UserModel();
        $userModel->insert($data);
    
        return redirect()->to('/admin/users');
    }

    
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users');
        }

        return view('v_edit_user', ['user' => $user]);
    }

    public function update($id)
    {
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role')
        ];

        $this->userModel->update($id, $data);

        return redirect()->to('/admin/users');
    }


    public function delete($id)
    {
        $this->userModel->delete($id);

        return redirect()->to('/admin/users');
    }

    public function adminMember()
    {
    
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/user');
        }
        
        $userData = [
            'username' => session()->get('username'),
            'role' => session()->get('role')          
        ];

        return view('v_adminMember', $userData);
    }
}
