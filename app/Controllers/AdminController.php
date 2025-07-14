<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\TransactionModel;

class AdminController extends BaseController
{
    protected $userModel;
    protected $transactionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->transactionModel = new TransactionModel();
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
    
        return view('admin/v_dashboard_admin', $adminData);
    }

    public function users()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/guest');
        }

        $users = $this->userModel->findAll();

        return view('admin/v_users', ['users' => $users]); 
    }

    public function create()
    {
        return view('admin/v_create_user');
    }

    public function store()
    {
        $data = [
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'       => $this->request->getPost('role'),
            'phone_number' => $this->request->getPost('phone_number'), // Menambahkan phone_number
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

        return view('admin/v_edit_user', ['user' => $user]);
    }

    public function update($id)
    {
        $data = [
            'username'   => $this->request->getPost('username'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'       => $this->request->getPost('role'),
            'phone_number' => $this->request->getPost('phone_number'), // Menambahkan phone_number
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

        return view('admin/v_adminMember', $userData);
    }

    public function pendingTransactions()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/guest');
        }

        $pendingTransactions = $this->transactionModel->where('status', 0)->findAll(); 
        return view('admin/v_pending', ['transactions' => $pendingTransactions]);
    }

    // Method untuk transaksi yang selesai
    public function completedTransactions()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/guest');
        }

        $completedTransactions = $this->transactionModel->where('status', 1)->findAll();
        return view('admin/v_completed', ['transactions' => $completedTransactions]);
    }

    // Method untuk transaksi yang dibatalkan
    public function canceledTransactions()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/guest');
        }

        $canceledTransactions = $this->transactionModel->where('status', 2)->findAll(); 
        return view('admin/v_canceled', ['transactions' => $canceledTransactions]);
    }

    public function cancelTransaction($id = null)
    {
        // Pastikan hanya admin yang bisa mengakses
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/guest')->with('error', 'Akses ditolak.');
        }

        if ($id === null) {
            return redirect()->to(base_url('admin/transaksi/pending'))->with('error', 'ID transaksi tidak ditemukan.');
        }

        $transaction = $this->transactionModel->find($id);

        if (!$transaction) {
            return redirect()->to(base_url('admin/transaksi/pending'))->with('error', 'Transaksi tidak ditemukan.');
        }

        // Pastikan hanya transaksi dengan status pending (0) yang bisa dibatalkan oleh admin
        // Jika sudah status 1 (paid) atau 2 (canceled/failed), tidak bisa dibatalkan lagi
        if ($transaction['status'] != 0) {
            return redirect()->to(base_url('admin/transaksi/pending'))->with('error', 'Transaksi ini tidak bisa dibatalkan karena sudah diproses atau dibatalkan sebelumnya.');
        }

        $this->transactionModel->update($id, [
            'status' => 2,
            'payment_status' => 'CANCELED',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Order berhasil dibatalkan oleh Admin.');
        return redirect()->to(base_url('admin/transaksi/pending'));
    }

}
