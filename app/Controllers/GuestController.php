<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class GuestController extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'guest') {
            return redirect()->to('/guest');
        }

        $userData = [
            'username' => session()->get('username'), 
            'role' => session()->get('role')          
        ];

        return view('v_dashboard_user', $userData);
    }

    public function userMember(){

        if (session()->get('role') !== 'guest') {
            return redirect()->to('/guest');
        }
        

        $userData = [
            'username' => session()->get('username'), 
            'role' => session()->get('role')          
        ];

        return view('v_userMember', $userData);
    }
}
