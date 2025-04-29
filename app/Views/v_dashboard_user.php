<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h2>Welcome, <?= esc($username) ?></h2>
<p>Role: <?= esc($role) ?></p>

<!-- Card untuk Menampilkan Informasi Langganan Gym -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">Member Gym anda</h5>
                <p><strong>Plan:</strong> 1 Bulan </p>
                <p><strong>Price:</strong> 150.000</p>
                <p><strong>Status:</strong> Aktif </p>
                <p><strong>Expiry Date:</strong> 12 Mei 2025</p>
                
                <!-- Tambahkan tombol untuk mengelola langganan (misalnya, pembaruan) -->
                <a href="<?= base_url('member') ?>" class="btn btn-primary">Riwayat Langganan</a>
            </div>
        </div>
    </div>

        <!-- Card untuk Menampilkan Jumlah Pengunjung Gym -->
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">Total Kunjungan Gym</h5>
                <p><strong>Bulan ini:</strong> 12 kunjungan</p> <!-- Data statis untuk jumlah pengunjung -->
                <p>Ini merupakan total kunjungan anda dalam bulan ini</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
