<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container">
    <h2>Riwayat Pembelian</h2>

    <?php if (!empty($transactions)) : ?>
        <div class="row">
            <?php $i = 1; ?>
            <?php foreach ($transactions as $transaction) : ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <!-- Product image and details in a horizontal layout -->
                        <div class="row g-3">
                            <div class="col-4">
                                <img src="<?= base_url('img/' . $transaction['product_image']) ?>" class="img-fluid" alt="Product Image" style="max-height: 150px; object-fit: cover; width: 100%;">
                            </div>
                            <div class="col-8">
                                <div class="card-body p-3">
                                    <h5 class="card-title"><?= $transaction['product_name'] ?></h5>
                                    <p><strong>Jumlah:</strong> <?= $transaction['jumlah'] ?></p>
                                    <p><strong>Harga:</strong> <?= number_to_currency($transaction['subtotal_harga'] / $transaction['jumlah'], 'IDR') ?></p>
                                    <p><strong>Subtotal:</strong> <?= number_to_currency($transaction['subtotal_harga'], 'IDR') ?></p>
                                    <p><strong>Total Harga:</strong> <?= number_to_currency($transaction['total_harga'], 'IDR') ?></p>
                                    <p><strong>Ongkir:</strong> <?= number_to_currency($transaction['ongkir'], 'IDR') ?></p>
                                    <p><strong>Status:</strong> 
                                        <?php 
                                        if ($transaction['status'] == 1) {
                                            echo "Pending";
                                        } elseif ($transaction['status'] == 2) {
                                            echo "Shipped";
                                        } else {
                                            echo "Delivered";
                                        }
                                        ?>
                                    </p>

                                    <!-- Tanggal Pembelian moved here -->
                                    <p><strong>Tanggal Pembelian:</strong> <?= date('d-m-Y H:i:s', strtotime($transaction['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Button moved to the bottom, with full width -->
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-danger btn-sm w-100 delete-btn" data-transaction-id="<?= $transaction['transaction_id'] ?>">Hapus</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-info text-center">Belum ada riwayat pembelian</div>
    <?php endif; ?>
</div>

<!-- Modal for confirmation -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Penghapusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus riwayat ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    // When the delete button is clicked
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var transactionId = button.getAttribute('data-transaction-id');
            
            // Set the form action to the delete URL with transaction ID
            document.getElementById('deleteForm').action = '<?= base_url('transaction/delete/') ?>' + transactionId;
            
            // Show the confirmation modal
            var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            confirmationModal.show();
        });
    });
</script>
<?= $this->endSection() ?>
