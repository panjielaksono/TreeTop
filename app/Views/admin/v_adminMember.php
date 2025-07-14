<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<?php if(session()->getFlashdata('message')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('message') ?>
    </div>
<?php endif; ?>

<h5 class="text-center mb-4">Data Langganan</h5>
<?php if (session()->getFlashdata('pesan')) : ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('pesan'); ?>
    </div>
<?php endif; ?>


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

<table class="table datatable">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Pengguna</th>
            <th>Jenis Langganan</th>
            <th>Tanggal Langganan</th>
            <th>Tanggal Kadaluarsa</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($memberships)) : ?>
            <?php $no = 1; ?>
            <?php foreach ($memberships as $membership) : ?>
                <tr>
                    <th><?= $no++ ?></th>
                    <td><?= esc($membership->username) ?></td>
                    <td>
                        <?php
                            switch ($membership->subscription_type) {
                                case 'monthly':
                                    echo 'Bulanan';
                                    break;
                                case 'yearly':
                                    echo 'Tahunan';
                                    break;
                                default:
                                    echo esc($membership->subscription_type);
                            }
                        ?>
                    </td>
                    <td><?= date('d F Y', strtotime($membership->start_date)) ?></td>
                    <td><?= date('d F Y', strtotime($membership->expiry_date)) ?></td>
                    <td>
                        <span class="badge bg-<?= $membership->status === 'Aktif' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($membership->status) ?>
                        </span>
                    </td>

                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" class="text-center">Belum ada data langganan yang ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<div class="d-flex justify-content-end mt-3 gap-2">
<form action="<?= base_url('/send-membership-reminder') ?>" method="get">
    <button type="submit" class="btn btn-primary">Kirim Notifikasi Expired Membership</button>
</form>
<br>
<form action="<?= base_url('deactivate-expired') ?>" method="get">
<button type="submit" class="btn btn-danger">Nonaktifkan Membership Expired</button>
</form>
</div>

<?= $this->endSection() ?>
