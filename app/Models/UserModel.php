<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'user';  // Your database table
    protected $primaryKey = 'id';     // Primary key field in the users table
    protected $allowedFields = ['username', 'email', 'password', 'role'];  // Fields that can be used

    // Function to get user by username
    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();  // Get the first user where username matches
    }
}
