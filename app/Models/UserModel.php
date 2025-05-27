<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'user';  
    protected $primaryKey = 'id';    
    protected $allowedFields = ['username', 'email', 'password', 'role'];
    protected $useTimestamps = true;  
    protected $dateFormat = 'datetime'; 

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first(); 
    }
}
