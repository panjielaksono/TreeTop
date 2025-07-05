<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MembershipModel; // Pastikan Anda memiliki model ini
use CodeIgniter\I18n\Time; // Untuk manipulasi tanggal

class Membership extends BaseController
{
    public function index()
    {
        $membershipModel = new MembershipModel();

        // Ambil data membership + username dari tabel user
        $data['memberships'] = $membershipModel
            ->select('memberships.*, user.username')
            ->join('user', 'user.id = memberships.user_id') // Sesuaikan nama tabel
            ->findAll();

        return view('v_adminMember', $data);
    }


    public function save()
    {
        $membershipModel = new MembershipModel();

        // Validasi input
    $rules = [
        'user_id' => 'required|integer',
        'subscription_type' => 'required|in_list[daily,monthly,yearly]',
        'start_date' => 'required|valid_date',
        'expiry_date' => 'required|valid_date',
        'phone_number' => 'required|min_length[8]|max_length[20]',
    ];


        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Silakan periksa kembali.');
        }

        $userId = $this->request->getPost('user_id');
        $subscriptionType = $this->request->getPost('subscription_type');
        $startDate = $this->request->getPost('start_date');
        $expiryDate = $this->request->getPost('expiry_date'); // Data expiry_date dari form
        $phoneNumber = $this->request->getPost('phone_number');

        // Jika Anda ingin menghitung expiry_date di backend untuk keamanan
        // Anda bisa mengabaikan $expiryDate dari POST dan menghitungnya ulang
        $calculatedExpiryDate = $this->calculateExpiryDate($startDate, $subscriptionType);

        $data = [
        'user_id' => $userId,
        'subscription_type' => $subscriptionType,
        'start_date' => $startDate,
        'expiry_date' => $calculatedExpiryDate,
        'phone_number' => $phoneNumber, // ✅ tambahkan ini
        'created_at' => Time::now(),
        'updated_at' => Time::now(),
    ];


        if ($membershipModel->insert($data)) {
            return redirect()->to(base_url('admin/membership'))->with('success', 'Langganan berhasil ditambahkan!');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan langganan.');
        }
    }

    public function delete($id = null)
    {
        $membershipModel = new MembershipModel();
        if ($membershipModel->delete($id)) {
            return redirect()->to(base_url('admin/membership'))->with('success', 'Langganan berhasil dihapus!');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus langganan.');
        }
    }

    // Fungsi helper untuk menghitung tanggal kadaluarsa (bisa juga di model)
    private function calculateExpiryDate($startDate, $subscriptionType)
    {
        $date = Time::parse($startDate);

        switch ($subscriptionType) {
            case 'daily':
                $date = $date->addDays(1);
                break;
            case 'monthly':
                $date = $date->addMonths(1);
                break;
            case 'yearly':
                $date = $date->addYears(1);
                break;
        }
        return $date->toDateString();
    }

    public function deactivate($id = null)
    {
    $membershipModel = new MembershipModel();
    $membership = $membershipModel->find($id);

    if ($membership) {
        $membershipModel->update($id, ['status' => 'nonaktif']);
        return redirect()->to(base_url('admin/membership'))->with('success', 'Membership berhasil dinonaktifkan.');
    } else {
        return redirect()->back()->with('error', 'Data membership tidak ditemukan.');
    }
    }

}