<?php

namespace App\Controllers;

use App\Models\MembershipModel;
use App\Libraries\FonnteAPI;
use CodeIgniter\I18n\Time;

class Cron extends BaseController
{
    public function sendReminderManual()
    {
        $model = new MembershipModel();

        $besok = Time::now()->addDays(1)->toDateString();

        // Jika kolom expiry_date bertipe DATETIME, gunakan DATE(expiry_date)
        $memberships = $model->getExpiringWithUser($besok);

        $fonnte = new FonnteAPI();
        $sent = 0;

        foreach ($memberships as $m) {
            $phone = $m->phone_number ?? null;
            if (!$phone) continue;
            $phone = preg_replace('/[^0-9]/', '', $phone); // Hapus semua karakter non-numerik
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }
            $nama = $m->user_name ?? 'Member';
            $message = "Halo {$nama}! Masa aktif membership Anda berakhir hari ini ({$besok}). Silakan perpanjang untuk tetap menikmati layanan kami 🙌.";

            $res = $fonnte->sendMessage($phone, $message);
            log_message('debug', 'Fonnte response: ' . json_encode($res));

            if (isset($res['status']) && $res['status']) {
                $sent++;
            }
        }

        return redirect()->to('/admin/membership')->with('pesan', "Reminder berhasil dikirim ke $sent member.");
}
    public function deactivateExpiredMembers()
{
    $model = new MembershipModel();
    $kemarin = Time::now()->subDays(1)->toDateString();

    $memberships = $model->getExpiringWithUser($kemarin); // Buat method ini juga ya
    $fonnte = new FonnteAPI();
    $nonaktif = 0;

    foreach ($memberships as $m) {
        $phone = preg_replace('/[^0-9]/', '', $m->phone_number);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $nama = isset($m->user_name) && trim($m->user_name) !== '' ? $m->user_name : 'Member';
        $message = "Hai {$nama}, masa aktif membership kamu telah BERAKHIR kemarin ({$m->expiry_date}) dan telah dinonaktifkan. Yuk aktifkan kembali untuk terus menikmati layanan kami! 💡";

        $res = $fonnte->sendMessage($phone, $message);
        log_message('debug', 'Nonaktif H+1: ' . json_encode($res));

        if (isset($res['status']) && $res['status']) {
            $model->update($m->id, ['status' => 'nonaktif']);
            $nonaktif++;
        }
    }

    return redirect()->to('/admin/membership')->with('pesan', "Reminder penonaktifan berhasil dikirim ke $nonaktif member.");
}
}

