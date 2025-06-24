<?php
namespace App\Models;

use CodeIgniter\Model;

class TransactionHistoryModel extends Model
{
    protected $table = 'transaction_detail';  // The main table for transaction details
    protected $primaryKey = 'id';

    // Method to get the transaction history by user ID
    public function getTransactionHistory($userId)
    {
        return $this->select('
                transaction_detail.transaction_id,
                transaction_detail.product_id,
                transaction_detail.jumlah,
                transaction_detail.diskon,
                transaction_detail.subtotal_harga,
                transaction_detail.created_at,
                transaction.total_harga,
                transaction.alamat,
                transaction.ongkir,
                transaction.status,
                product.nama AS product_name,
                product.foto AS product_image
            ')
            ->join('transaction', 'transaction.id = transaction_detail.transaction_id')  // Join with transaction table
            ->join('product', 'product.id = transaction_detail.product_id')  // Join with products table
            ->where('transaction.username', $userId)  // Filter by logged-in user
            ->orderBy('transaction_detail.created_at', 'desc')  // Order by most recent transactions
            ->findAll();
    }
    public function deleteTransaction($transactionId)
    {
        // Delete from transaction_details table
        $this->where('transaction_id', $transactionId)->delete();

        // Optionally delete from the transaction table as well
        // If the transaction table also needs to be cleared
        // $this->db->table('transaction')->where('id', $transactionId)->delete();
    }
}
