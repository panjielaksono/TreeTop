<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h5 class="text-center mb-4">Transaksi Dibatalkan</h5>
<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nama Pengguna</th>
            <th scope="col">Total Harga</th>
            <th scope="col">Alamat</th>
            <th scope="col">Kelurahan</th>
            <th scope="col">Ongkir</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($transactions)): ?>
            <?php $counter = 1; ?> <!-- Inisialisasi counter untuk reset ID -->
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <th scope="row"><?= $counter++ ?></th> <!-- Menampilkan ID transaksi yang dimulai dari 1 -->
                    <td><?= esc($transaction['username']) ?></td>
                    <td><?= esc($transaction['total_harga']) ?></td>
                    <td><?= esc($transaction['alamat']) ?></td>
                    <td><?= esc($transaction['kelurahan']) ?></td>
                    <td><?= esc($transaction['ongkir']) ?></td>
                    <td><span class="badge bg-danger">Dibatalkan</span></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">Tidak ada transaksi dibatalkan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>