<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Daftar Langganan Baru</h5>
       <form action="<?= base_url('guest/membership/saveMembership') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="user_id" value="<?= esc(session()->get('id')) ?>">

    <div class="mb-3">
        <label for="subscriptionType" class="form-label">Jenis Langganan</label>
        <select class="form-select" id="subscriptionType" name="subscription_type" required>
            <option value="">-- Pilih --</option>
            <option value="monthly">Bulanan</option>
            <option value="yearly">Tahunan</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Daftar</button>
</form>

    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Tabel dengan data anggota gym untuk role user -->
 
<h5 class="text-center mb-4">Riwayat Langganan Anda</h5>
<table class="table datatable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nama Pengguna</th>
            <th scope="col">Jenis Langganan</th>
            <th scope="col">Tanggal Langganan</th>
            <th scope="col">Tanggal Kadaluarsa</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
<tbody>
    <?php if (!empty($memberships)) : ?>
        <?php $no = 1; ?>
        <?php foreach ($memberships as $membership) : ?>
            <tr>
                <th scope="row"><?= $no++ ?></th>
                <td><?= esc($membership->username) ?></td>
                <td>
                    <?php
                        if ($membership->subscription_type == 'monthly') {
                            echo 'Bulanan';
                        } elseif ($membership->subscription_type == 'yearly') {
                            echo 'Tahunan';
                        } else {
                            echo esc($membership->subscription_type);
                        }
                    ?>
                </td>
                <td><?= esc(date('d F Y', strtotime($membership->start_date))) ?></td>
                <td><?= esc(date('d F Y', strtotime($membership->expiry_date))) ?></td>
                <td>
                    <?php if ($membership->status == 'Aktif') : ?>
                        <span class="badge bg-success">Aktif</span>
                    <?php else : ?>
                        <span class="badge bg-secondary">Nonaktif</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="6" class="text-center">Belum ada riwayat langganan Anda.</td>
        </tr>
    <?php endif; ?>
</tbody>

</table>

<?= $this->endSection() ?>
