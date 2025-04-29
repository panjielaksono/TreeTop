<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('v_login');
    }
    public function userDashboard()
    {
     
        if (session()->get('role') !== 'user') {
            return redirect()->to('/user');
        }
        

        $userData = [
            'username' => session()->get('username'), 
            'role' => session()->get('role')          
        ];

        return view('v_dashboard_user', $userData);
    }
    
    public function userMember(){

        if (session()->get('role') !== 'user') {
            return redirect()->to('/user');
        }
        

        $userData = [
            'username' => session()->get('username'), 
            'role' => session()->get('role')          
        ];

        return view('v_userMember', $userData);
    }

    public function adminDashboard()
    {
      
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/user');
        }


        $users = json_decode(file_get_contents(WRITEPATH . 'users.json'), true);
            
      
        $userCount = count($users);

   
        $adminCount = 0;
        $userRoleCount = 0;

  
        foreach ($users as $user) {
            if ($user['role'] === 'admin') {
                $adminCount++;
            } elseif ($user['role'] === 'user') {
                $userRoleCount++;
            }
        }

   
        $adminData = [
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'userCount' => $userCount,      
            'adminCount' => $adminCount,    
            'userRoleCount' => $userRoleCount 
        ];

        return view('v_dashboard_admin', $adminData);
    }



    public function users()
    {
      
        if (session()->get('role') !== 'admin') {
            return redirect()->to('dashboard');
        }
       
        $users = json_decode(file_get_contents(WRITEPATH . 'users.json'), true);

      
        return view('v_users', ['users' => $users]);
    }

        public function create()
    {
        return view('v_create_user');  
    }

    public function store()
    {
       
        $newUser = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role')
        ];

       
        $filePath = WRITEPATH . 'users.json';
        $users = json_decode(file_get_contents($filePath), true);

      
        $users[] = $newUser;

      
        file_put_contents($filePath, json_encode($users, JSON_PRETTY_PRINT));

        return redirect()->to('/users');
    }

    
    public function edit($username)
    {
        $users = json_decode(file_get_contents(WRITEPATH . 'users.json'), true);

    
        $user = null;
        foreach ($users as $u) {
            if ($u['username'] == $username) {
                $user = $u;
                break;
            }
        }

        if ($user === null) {
            return redirect()->to('/users'); 
        }

        return view('v_edit_user', ['user' => $user]);
    }

       public function update($username)
    {
        $users = json_decode(file_get_contents(WRITEPATH . 'users.json'), true);

     
        foreach ($users as $key => $user) {
            if ($user['username'] == $username) {
                $users[$key]['username'] = $this->request->getPost('username');
                $users[$key]['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                $users[$key]['role'] = $this->request->getPost('role');
                break;
            }
        }

   
        file_put_contents(WRITEPATH . 'users.json', json_encode($users, JSON_PRETTY_PRINT));

        return redirect()->to('/users');
    }


    public function delete($username)
    {
        $users = json_decode(file_get_contents(WRITEPATH . 'users.json'), true);

     
        foreach ($users as $key => $user) {
            if ($user['username'] == $username) {
                unset($users[$key]);
                break;
            }
        }

   
        file_put_contents(WRITEPATH . 'users.json', json_encode(array_values($users), JSON_PRETTY_PRINT));

        return redirect()->to('/users');
    }
        public function adminMember(){
    
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
