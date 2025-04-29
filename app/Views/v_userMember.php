<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<!-- Judul -->
<h5 class="text-center mb-4">Riwayat Langganan Anda</h5>

<!-- Tabel dengan data anggota gym untuk role user -->
<table class="table datatable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nama Pengguna</th>
            <th scope="col">Jenis Langganan</th>
            <th scope="col">Tanggal Langganan</th>
            <th scope="col">Tanggal Kadaluarsa</th>
        </tr>
    </thead>
    <tbody>
        <!-- Data Statis untuk Langganan -->
        <tr>
            <th scope="row">1</th>
            <td> <?= esc($username) ?></td>
            <td>Bulanan</td>
            <td>12 April 2025</td>
            <td>12 Mei 2025</td>
        </tr>
    </tbody>
</table>

<?= $this->endSection() ?>
