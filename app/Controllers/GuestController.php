<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use App\Models\MembershipModel; // Pastikan model ini di-import

class GuestController extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'guest') {
            // Redirect ke halaman login atau dashboard lain jika bukan guest
            return redirect()->to('/login'); // Sesuaikan dengan route login Anda
        }

        $userData = [
            'username' => session()->get('username'),
            'role' => session()->get('role')
        ];

        return view('v_dashboard_user', $userData);
    }

    public function userMember()
    {
        if (session()->get('role') !== 'guest') {
            return redirect()->to('/login'); // Sesuaikan dengan route login Anda
        }

        $currentUserId = session()->get('id'); // Ambil ID pengguna yang login

        // Panggil model membership
        $membershipModel = new \App\Models\MembershipModel();

        // Ambil data membership user
        // Pastikan nama tabel 'user' dan kolom 'id' serta 'user_id' sudah benar di database Anda.
        $memberships = $membershipModel
            ->select('memberships.*, user.username') // atau 'users.username' jika nama tabelnya 'users'
            ->join('user', 'user.id = memberships.user_id') // Sesuaikan nama tabel
            ->where('memberships.user_id', $currentUserId)
            ->findAll();

        $data = [
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'memberships' => $memberships // Kirim data ke view
        ];

        // --- DEBUGGING START ---
        // Uncomment baris di bawah ini untuk melihat data membership yang diambil untuk user
        // dd($data['memberships']);
        // --- DEBUGGING END ---

        return view('v_userMember', $data);
    }

    public function saveMembership()
    {
        // Baris debugging session('id') telah dihapus karena sudah mengkonfirmasi masalah.
        // dd(session()->get('id'));

        $model = new MembershipModel(); // Menggunakan use App\Models\MembershipModel;

        $rules = [
            'user_id'           => 'required|integer',
            'subscription_type' => 'required|in_list[daily,monthly,yearly]',
        ];

        if (!$this->validate($rules)) {
            // --- DEBUGGING VALIDATION ERROR ---
            // AKTIFKAN BARIS DI BAWAH INI UNTUK MELIHAT PESAN ERROR VALIDASI
            dd($this->validator->getErrors()); // Tampilkan error validasi
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Silakan periksa kembali.');
        }

        $userId = $this->request->getPost('user_id');
        $type = $this->request->getPost('subscription_type');
        $start = Time::now();
        $expiry = $this->calculateExpiryDate($start, $type);

        $data = [
            'user_id'           => $userId,
            'subscription_type' => $type,
            'start_date'        => $start->toDateString(),
            'expiry_date'       => $expiry,
            'status'            => 'aktif',
        ];


        // --- DEBUGGING DATA BEFORE INSERT ---
        // Uncomment baris di bawah ini untuk melihat data yang akan di-insert
        // dd($data);

        if ($model->insert($data)) {
            return redirect()->back()->with('success', 'Berhasil mendaftar langganan.');
        } else {
            // --- DEBUGGING INSERT ERROR ---
            // dd($model->errors()); // Menampilkan error dari model (jika ada)
            // dd($model->db->error()); // Menampilkan error dari database (jika ada)
            return redirect()->back()->with('error', 'Gagal mendaftar langganan. Silakan coba lagi.');
        }
    }

    private function calculateExpiryDate($start, $type)
    {
        $date = Time::parse($start);
        switch ($type) {
            case 'daily':
                return $date->addDays(1)->toDateString();
            case 'monthly':
                return $date->addMonths(1)->toDateString();
            case 'yearly':
                return $date->addYears(1)->toDateString();
            default:
                return $date->toDateString();
        }
    }
}
