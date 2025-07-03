<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h5 class="text-center mb-4">Data Langganan</h5>


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
            <th>Aksi</th>
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
                                case 'daily':
                                    echo 'Harian';
                                    break;
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
                        <span class="badge bg-<?= $membership->status === 'aktif' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($membership->status) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($membership->status === 'aktif') : ?>
                            <a href="<?= base_url('admin/membership/deactivate/' . $membership->id) ?>"
                               class="btn btn-warning btn-sm"
                               onclick="return confirm('Apakah Anda yakin ingin menonaktifkan langganan ini?')">
                                <i class="bi bi-x-circle"></i> Nonaktifkan
                            </a>
                        <?php else : ?>
                            <button class="btn btn-secondary btn-sm" disabled>Nonaktif</button>
                        <?php endif; ?>
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

<?= $this->endSection() ?>
