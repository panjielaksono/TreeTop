<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h5 class="text-center mb-4">Transaksi Pending (Admin)</h5>
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
            <th scope="col">Aksi</th> 
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($transactions)): ?>
            <?php $counter = 1; ?>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <th scope="row"><?= $counter++ ?></th>
                    <td><?= esc($transaction['username']) ?></td>
                    <td><?= esc($transaction['total_harga']) ?></td>
                    <td><?= esc($transaction['alamat']) ?></td>
                    <td><?= esc($transaction['kelurahan']) ?></td>
                    <td><?= esc($transaction['ongkir']) ?></td>
                    <td><span class="badge bg-warning">Pending</span></td>
                    <td>
                        <a href="<?= base_url('admin/transactions/cancel/' . $transaction['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin membatalkan order ini? Aksi ini tidak dapat dibatalkan.')">Batal Order</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">Tidak ada transaksi pending.</td> 
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>