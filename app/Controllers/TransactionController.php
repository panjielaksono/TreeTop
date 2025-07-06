<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;
use App\Models\TransactionHistoryModel;
use App\Models\UserModel; 
use Midtrans\Snap;
use Midtrans\Config;

class TransactionController extends BaseController
{
    protected $cart;
    protected $client;
    protected $apiKey;
    protected $transaction; 
    protected $transaction_detail;
    protected $transaction_history;

    function __construct()
    {
        helper('number');
        helper('form');
        $this->cart = \Config\Services::cart();
        $this->client = new \GuzzleHttp\Client();
        $this->apiKey = env('COST_KEY');
        $this->transaction = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
        $this->transaction_history = new TransactionHistoryModel();

        // Midtrans configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; 
        Config::$isSanitized = true;
        Config::$is3ds = true;

    }

    public function index()
    {
        $data['items'] = $this->cart->contents();
        $data['total'] = $this->cart->total();
        return view('guest/v_keranjang', $data);
    }

    public function cart_add()
    {
        $this->cart->insert(array(
            'id'        => $this->request->getPost('id'),
            'qty'       => 1,
            'price'     => $this->request->getPost('harga'),
            'name'      => $this->request->getPost('nama'),
            'options'   => array(
                'foto' => $this->request->getPost('foto'),
                'weight' => $this->request->getPost('weight')
            )
        ));
        session()->setflashdata('success', 'Produk berhasil ditambahkan ke keranjang. (<a href="' . base_url() . 'keranjang">Lihat</a>)');
        return redirect()->to(base_url('/'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();
        session()->setflashdata('success', 'Keranjang Berhasil Dikosongkan');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $value) {
            $this->cart->update(array(
                'rowid' => $value['rowid'],
                'qty'   => $this->request->getPost('qty' . $i++)
            ));
        }

        session()->setflashdata('success', 'Keranjang Berhasil Diedit');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);
        session()->setflashdata('success', 'Keranjang Berhasil Dihapus');
        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
    {
        $data['items'] = $this->cart->contents();
        $data['total'] = $this->cart->total();
        $nonce = base64_encode(random_bytes(16));
        $data['csp_nonce'] = $nonce;
        return view('guest/checkout', $data);
    }

    
    public function getLocation()
    {
        $search = $this->request->getGet('search');

        $response = $this->client->request(
            'GET', 
            'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search='.$search.'&limit=50', [
                'headers' => [
                    'accept' => 'application/json',
                    'key' => $this->apiKey,
                ],
            ]
        );

        $body = json_decode($response->getBody(), true); 
        return $this->response->setJSON($body['data']);
    }

    public function getCost()
    { 
        $destination = $this->request->getGet('destination');
        
        $totalWeight = 0;
        foreach ($this->cart->contents() as $item) {
            $totalWeight += $item['options']['weight'] * $item['qty']; 
        }
    
        $response = $this->client->request(
            'POST', 
            'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'multipart' => [
                    [
                        'name' => 'origin',
                        'contents' => '64999' 
                    ],
                    [
                        'name' => 'destination',
                        'contents' => $destination
                    ],
                    [
                        'name' => 'weight',
                        'contents' => $totalWeight 
                    ],
                    [
                        'name' => 'courier',
                        'contents' => 'jne'
                    ]
                ],
                'headers' => [
                    'accept' => 'application/json',
                    'key' => $this->apiKey,
                ],
            ]
        );
    
        $body = json_decode($response->getBody(), true); 
        return $this->response->setJSON($body['data']);
    }

    public function buy()
    {
        // Hanya cek method POST
        if ($this->request->getMethod() === 'POST') {
            $transaction_id = null; // Inisialisasi transaction_id
            try {
                $userModel = new UserModel();
                $user = $userModel->where('username', session()->get('username'))->first();
                if (!$user) {
                    return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan']);
                }
    
                // Ambil label kelurahan dari form
                $kelurahan_label = $this->request->getPost('kelurahan_label');
    
                // Simpan transaksi
                $transactionData = [
                    'username' => $this->request->getPost('username'),
                    'total_harga' => $this->request->getPost('total_harga'),
                    'alamat' => $this->request->getPost('alamat'),
                    'kelurahan' => $kelurahan_label,  // Pastikan kelurahan yang benar disimpan
                    'ongkir' => $this->request->getPost('ongkir'),
                    'status' => 0,  // Default status is pending
                    'created_at' => date('Y-m-d H:i:s'),  // Ensure created_at is saved
                ];
    
                // Insert transaction and check for errors
                if ($this->transaction->insert($transactionData) === false) {
                    $errors = $this->transaction->errors();
                    return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal DB: ' . implode(', ', $errors)]);
                }
    
                $transaction_id = $this->transaction->getInsertID();  // Get the ID of the inserted transaction
    
                // Generate a unique order ID
                $order_id = 'ORDER-' . $transaction_id . '-' . time(); // Use a unique combination
    
                // Siapkan item details
                $item_details = [];
                foreach ($this->cart->contents() as $value) {
                    $this->transaction_detail->insert([
                        'transaction_id' => $transaction_id, 'product_id' => $value['id'], 'jumlah' => $value['qty'], 'subtotal_harga' => $value['qty'] * $value['price'],
                    ]);
                    $item_details[] = ['id' => $value['id'], 'price' => (int)$value['price'], 'quantity' => (int)$value['qty'], 'name' => $value['name']];
                }
    
                if ((int)$this->request->getPost('ongkir') > 0) {
                    $item_details[] = ['id' => 'ONGKIR', 'price' => (int)$this->request->getPost('ongkir'), 'quantity' => 1, 'name' => 'Biaya Pengiriman'];
                }
    
                // Siapkan parameter Midtrans
                $params = [
                    'transaction_details' => ['order_id' => $order_id, 'gross_amount' => (int)$this->request->getPost('total_harga')],
                    'item_details' => $item_details,
                    'customer_details' => ['first_name' => $user['username'], 'email' => $user['email']],
                ];
    
                // Dapatkan Snap Token
                $snapToken = Snap::getSnapToken($params);
    
                // Update transaksi dengan token dan unique order_id
                $this->transaction->update($transaction_id, ['snap_token' => $snapToken, 'order_id' => $order_id]);
    
                $this->cart->destroy();  // Clear the cart after successful transaction creation
    
                // Return the success response with the Snap Token
                return $this->response->setJSON(['status' => 'success', 'snapToken' => $snapToken]);
    
            } catch (\Exception $e) {
                // Handle error during transaction creation
                if ($transaction_id) {
                    $this->transaction->delete($transaction_id); // Delete the transaction if any error occurs
                }
                // Return the error message from Midtrans
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Midtrans Error: ' . $e->getMessage()]);
            }
        }
    
        return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Method Not Allowed']);
    }
    
    public function history()
    {
        // Get the user ID from the session (or username, depending on how your system is designed)
        $userId = session()->get('username');  // or session()->get('user_id') if using ID

        // Fetch transaction history from the new model
        $transactions = $this->transaction_history->getTransactionHistory($userId);

        // Pass the data to the view
        return view('guest/v_transaction_history', ['transactions' => $transactions]);
    }
    public function delete($transactionId)
    {
        // Delete the transaction from the database
        $this->transaction_history->deleteTransaction($transactionId);

        // Set a success flash message
        session()->setFlashdata('success', 'Riwayat pembelian berhasil dihapus.');

        // Redirect back to the history page
        return redirect()->to(base_url('history'));
    }

    public function callback()
    {
        if ($this->request->getMethod(true) === 'POST') {
            // Get the request body
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);
            log_message('info', 'Midtrans POST Callback Data: ' . print_r($data, true));

            // Check if data exists
            if (!$data) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Invalid data sent.']);
            }

            // Midtrans server key
            $serverKey = env('MIDTRANS_SERVER_KEY');

            // Generate signature
            $signature = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey);

            // Log generated signature
            log_message('info', 'Generated Signature: ' . $signature);
            log_message('info', 'Received Signature: ' . $data['signature_key']);

            // Check if the signatures match
            if ($signature != $data['signature_key']) {
                log_message('error', 'Midtrans callback signature invalid.');
                return $this->response->setStatusCode(401)->setJSON(['status' => 'error', 'message' => 'Invalid Signature']);
            }

            // Handle transaction status
            $transaction_id = str_replace('ORDER-', '', $data['order_id']);
            $transaction_status = $data['transaction_status'];
            $fraud_status = $data['fraud_status'];

            // Default values
            $payment_status = 'PENDING'; // Default to pending
            $kelurahan = null; // Default kelurahan is null
            $expired_at = isset($data['expiry_time']) ? $data['expiry_time'] : null; // Midtrans expiry_time

            // Retrieve the transaction from the database
            $transaction = $this->transaction->find($transaction_id);
            if ($transaction) {
                // If status is settlement or capture and fraud_status is accept, mark as paid
                if ($transaction_status == 'capture' && $fraud_status == 'accept') {
                    $payment_status = 'PAID';
                } else if ($transaction_status == 'settlement') {
                    $payment_status = 'PAID';
                } else if (in_array($transaction_status, ['cancel', 'deny', 'expire'])) {
                    $payment_status = 'FAILED';
                }

                // Make sure kelurahan is saved as well
                if (isset($transaction['kelurahan'])) {
                    $kelurahan = $transaction['kelurahan'];  // Use existing kelurahan
                }

                // Update the transaction in the database
                $this->transaction->update($transaction_id, [
                    'status' => $payment_status === 'PAID' ? 1 : 2, // Update status accordingly (1=paid, 2=failed)
                    'payment_status' => $payment_status,
                    'kelurahan' => $kelurahan,  // Ensure kelurahan is saved
                    'expired_at' => $expired_at,  // Set expired_at from response
                    'updated_at' => date('Y-m-d H:i:s') // Set updated_at with the current time
                ]);
            }

            // Respond with success
            return $this->response->setStatusCode(200)->setJSON(['status' => 'success', 'message' => 'Notification Handled']);
        }

        return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Method Not Allowed']);
}

    public function checkExpiredTransactions()
    {
        $current_time = date('Y-m-d H:i:s'); // Mendapatkan waktu saat ini
        $expired_transactions = $this->transaction->where('expired_at <', $current_time)
                                                   ->where('status !=', 1) // Hanya transaksi yang belum selesai
                                                   ->findAll();
    
        foreach ($expired_transactions as $transaction) {
            // Update status menjadi canceled (misalnya status 2 untuk canceled)
            $this->transaction->update($transaction['id'], ['status' => 2]);
        }
    }    

}

