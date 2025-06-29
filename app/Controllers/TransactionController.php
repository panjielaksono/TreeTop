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
        return view('v_keranjang', $data);
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
    
        return view('checkout', $data);
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
        // Hanya cek method POST, tanpa isAJAX()
        if ($this->request->getMethod() === 'post') {
            $transaction_id = null; // Inisialisasi transaction_id
            try {
                $userModel = new UserModel();
                $user = $userModel->where('username', session()->get('username'))->first();
                if (!$user) {
                    return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan']);
                }

                // Simpan transaksi
                if ($this->transaction->insert([
                    'username' => $this->request->getPost('username'),
                    'total_harga' => $this->request->getPost('total_harga'),
                    'alamat' => $this->request->getPost('alamat'),
                    'ongkir' => $this->request->getPost('ongkir'),
                    'status' => 0,
                ]) === false) {
                     $errors = $this->transaction->errors();
                     return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal DB: ' . implode(', ', $errors)]);
                }
                $transaction_id = $this->transaction->getInsertID();

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
                    'transaction_details' => ['order_id' => 'ORDER-' . $transaction_id, 'gross_amount' => (int)$this->request->getPost('total_harga')],
                    'item_details' => $item_details,
                    'customer_details' => ['first_name' => $user['username'], 'email' => $user['email']]
                ];

                // Dapatkan Snap Token
                $snapToken = Snap::getSnapToken($params);
                
                // Update transaksi dengan token
                $this->transaction->update($transaction_id, ['snap_token' => $snapToken]);
                $this->cart->destroy();

                // Berikan token sebagai response JSON yang sukses
                return $this->response->setJSON(['status' => 'success', 'snapToken' => $snapToken]);

            } catch (\Exception $e) {
                // Jika ada error setelah transaksi dibuat (misal dari Midtrans)
                if ($transaction_id) {
                    $this->transaction->delete($transaction_id); // Hapus transaksi induknya saja
                }
                // Kembalikan pesan error yang asli
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
        return view('v_transaction_history', ['transactions' => $transactions]);
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

    // method buat si sendit
    // app/Controllers/TransactionController.php

    public function callback()
    {
        $requestBody = file_get_contents('php://input');
        $data = json_decode($requestBody, true);

        if (isset($data['external_id']) && isset($data['status'])) {
            // Extract transaction ID from the external ID (e.g., GYM-<transaction_id>)
            $transaction_id = str_replace('GYM-', '', $data['external_id']);

            // Update the status in your database based on Midtrans' callback data
            if ($data['status'] == 'PAID' || $data['status'] == 'SETTLED') {
                $this->transaction->update($transaction_id, ['status' => 1]); // Status 1 = Paid
            } else {
                $this->transaction->update($transaction_id, ['status' => 2]); // Status 2 = Failed
            }

            // Send a 200 OK response to Midtrans
            return $this->response->setStatusCode(200)->setJSON(['status' => 'success']);
        }

        return $this->response->setStatusCode(400)->setJSON(['status' => 'error']);
    }

    public function cobaProsesPembayaran()
    {
        // Hanya cek method POST, tanpa isAJAX()
        if ($this->request->getMethod() === 'post') {
            $transaction_id = null; // Inisialisasi transaction_id
            try {
                $userModel = new UserModel();
                $user = $userModel->where('username', session()->get('username'))->first();
                if (!$user) {
                    return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan']);
                }

                // Simpan transaksi
                if ($this->transaction->insert([
                    'username' => $this->request->getPost('username'),
                    'total_harga' => $this->request->getPost('total_harga'),
                    'alamat' => $this->request->getPost('alamat'),
                    'ongkir' => $this->request->getPost('ongkir'),
                    'status' => 0,
                ]) === false) {
                     $errors = $this->transaction->errors();
                     return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Gagal DB: ' . implode(', ', $errors)]);
                }
                $transaction_id = $this->transaction->getInsertID();

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
                    'transaction_details' => ['order_id' => 'ORDER-' . $transaction_id, 'gross_amount' => (int)$this->request->getPost('total_harga')],
                    'item_details' => $item_details,
                    'customer_details' => ['first_name' => $user['username'], 'email' => $user['email']]
                ];

                // Dapatkan Snap Token
                $snapToken = Snap::getSnapToken($params);
                
                // Update transaksi dengan token
                $this->transaction->update($transaction_id, ['snap_token' => $snapToken]);
                $this->cart->destroy();

                // Berikan token sebagai response JSON yang sukses
                return $this->response->setJSON(['status' => 'success', 'snapToken' => $snapToken]);

            } catch (\Exception $e) {
                // Jika ada error setelah transaksi dibuat (misal dari Midtrans)
                if ($transaction_id) {
                    $this->transaction->delete($transaction_id); // Hapus transaksi induknya saja
                }
                // Kembalikan pesan error yang asli
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Midtrans Error: ' . $e->getMessage()]);
            }
        }
        
        return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Method Not Allowed']);
    }

}

