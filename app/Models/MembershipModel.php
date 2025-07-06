<?php namespace App\Models;

use CodeIgniter\Model;

class MembershipModel extends Model
{
    protected $table      = 'memberships';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'subscription_type',
        'start_date',
        'expiry_date',
        'status',
        'phone_number', 
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; 

public function getExpiringWithUser($tanggal)
{
    return $this->select('memberships.*, user.username as user_name, user.phone_number as phone_number')
                ->join('user', 'user.id = memberships.user_id')
                ->where('DATE(expiry_date)', $tanggal)
                ->where('memberships.status', 'aktif')
                ->findAll();
}

}
