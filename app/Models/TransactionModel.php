<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transaction';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'username', 'total_harga', 'alamat', 'kelurahan', 'ongkir', 'status', 'expired_at', 
        'snap_token', 'created_at', 'updated_at'
    ];
}
