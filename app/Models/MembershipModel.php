<?php namespace App\Models;

use CodeIgniter\Model;

class MembershipModel extends Model
{
    protected $table      = 'memberships'; // Nama tabel di database Anda
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object'; // Atau 'array' jika Anda lebih suka array
    protected $useSoftDeletes = false; // Sesuaikan jika Anda menggunakan soft deletes

    protected $allowedFields = ['user_id', 'subscription_type', 'start_date', 'expiry_date', 'status'];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Jika useSoftDeletes true
}