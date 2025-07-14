<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use App\Models\MembershipModel; // Pastikan model ini di-import
use App\Models\UserModel; // Pastikan model ini di-import

class GuestController extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'guest') {
            // Redirect ke halaman login atau dashboard lain jika bukan guest
            return redirect()->to('/login'); // Sesuaikan dengan route login Anda
        }

        $userData = [
            'username' => session()->get('username'),
            'role' => session()->get('role')
        ];

        return view('guest/v_dashboard_user', $userData);
    }

        // Profile Page
        public function profile()
        {
            $userId = session()->get('id'); // Get current user's ID
            $userModel = new UserModel();
            $user = $userModel->find($userId); // Fetch user data
    
            if (!$user) {
                return redirect()->to('/guest')->with('error', 'User not found.');
            }
    
            $data = [
                'user' => $user // Pass user data to the view
            ];
    
            return view('guest/v_profile', $data); // Show profile page
        }
    
        public function updateProfile()
    {
        $userId = session()->get('id'); // Get current user's ID
        $userModel = new UserModel();
        
        // Validate form data
        $rules = [
            'username' => 'required|min_length[3]',
            'email'    => 'required|valid_email',
            'phone_number' => 'required|min_length[10]|max_length[20]', // Validate phone number
            'password' => 'permit_empty|min_length[6]', // Optional password change
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check your input.');
        }

        // Collect the form data
        $data = [
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'), // Add phone number
        ];

        // If the password is not empty, update it as well
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update the user data in the database
        if ($userModel->update($userId, $data)) {
            session()->setFlashdata('success', 'Profile updated successfully.');
            return redirect()->to('/guest/profile');
        } else {
            session()->setFlashdata('error', 'Failed to update profile.');
            return redirect()->to('/guest/profile');
        }
    }


    public function userMember()
    {
        if (session()->get('role') !== 'guest') {
            return redirect()->to('/login'); 
        }

        $currentUserId = session()->get('id'); 

        $membershipModel = new \App\Models\MembershipModel();

        $memberships = $membershipModel
            ->select('memberships.*, user.username')  
            ->join('user', 'user.id = memberships.user_id')  
            ->where('memberships.user_id', $currentUserId)  
            ->findAll();

        $data = [
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'memberships' => $memberships 
        ];

        return view('guest/v_userMember', $data); 
}

    public function saveMembership()
    {
        $model = new MembershipModel(); 

        $rules = [
            'user_id'           => 'required|integer',
            'subscription_type' => 'required|in_list[daily,monthly,yearly]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Silakan periksa kembali.');
        }

        $userId = $this->request->getPost('user_id');
        $type = $this->request->getPost('subscription_type');
        $start = Time::now();
        $expiry = $this->calculateExpiryDate($start, $type);

        $data = [
            'user_id'           => $userId,
            'subscription_type' => $type,
            'start_date'        => $start->toDateString(),
            'expiry_date'       => $expiry,
            'status'            => 'aktif',
        ];

        if ($model->insert($data)) {
            return redirect()->back()->with('success', 'Berhasil mendaftar langganan.');
        } else {
            return redirect()->back()->with('error', 'Gagal mendaftar langganan. Silakan coba lagi.');
        }
    }

    private function calculateExpiryDate($start, $type)
    {
        $date = Time::parse($start);
        switch ($type) {
            case 'daily':
                return $date->addDays(1)->toDateString();
            case 'monthly':
                return $date->addMonths(1)->toDateString();
            case 'yearly':
                return $date->addYears(1)->toDateString();
            default:
                return $date->toDateString();
        }
    }
}
