<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel; // Using UserModel for accessing database

class AuthController extends BaseController
{
    protected $user;

    function __construct()
    {
        helper('form');
        $this->user = new UserModel();  // Initialize UserModel to interact with the users table
    }

    // Logout function to destroy the session
    public function logout()
    {
        session()->destroy();  // Destroy all session data
        return redirect()->to('/login');  // Redirect to login page
    }

    // Login function
    public function login()
    {
        if ($this->request->getPost()) {
            $rules = [
                'username' => 'required|min_length[6]',
                'password' => 'required|min_length[7]|numeric',  // Make sure password is numeric or adjust based on your needs
            ];

            // Validate form inputs
            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');

                // Fetch user data from the database using the username
                $dataUser = $this->user->getUserByUsername($username);

                if ($dataUser) {
                    // Verify the password using password_verify() against the hashed password in the database
                    if (password_verify($password, $dataUser['password'])) {
                        // Set session data after successful login
                        session()->set([
                            'username' => $dataUser['username'],
                            'role' => $dataUser['role'],
                            'isLoggedIn' => TRUE
                        ]);

                        // Redirect to home/dashboard after successful login
                        return redirect()->to('/home');  // Modify this to redirect to the appropriate page
                    } else {
                        // If password doesn't match
                        session()->setFlashdata('failed', 'Kombinasi Username & Password Salah');
                        return redirect()->back();
                    }
                } else {
                    // If username doesn't exist
                    session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                    return redirect()->back();
                }
            } else {
                // If validation failed (e.g., username or password didn't pass validation rules)
                session()->setFlashdata('failed', $this->validator->listErrors());
                return redirect()->back();
            }
        }

        // Show login view if no post data is found
        return view('v_login');
    }
}
