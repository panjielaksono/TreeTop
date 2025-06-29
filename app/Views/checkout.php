<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <form id="payment-form" class="row g-3" method="POST">
            <?= csrf_field() ?>
            
            <input type="hidden" name="username" value="<?= session()->get('username') ?>">
            <input type="hidden" name="total_harga" id="total_harga" value="">
            
            <div class="col-12">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" value="<?= session()->get('username'); ?>" readonly>
            </div>
            <div class="col-12">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="alamat" name="alamat" required>
            </div>
            <div class="col-12">
                <label for="kelurahan" class="form-label">Kelurahan</label>
                <select class="form-control" name="kelurahan" id="kelurahan" required></select>
            </div>
            <div class="col-12">
                <label for="layanan" class="form-label">Layanan</label>
                <select class="form-control" name="layanan" id="layanan" required>
                    <option value="" selected>-- Pilih Jenis Layanan --</option>
                </select>
            </div>
            <div class="col-12">
                <label for="ongkir" class="form-label">Ongkir</label>
                <input type="text" class="form-control" id="ongkir" name="ongkir" readonly>
            </div>
        
    </div>
    <div class="col-lg-6">
        <div class="col-12">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Berat</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Menghitung total berat dari semua item di keranjang
                    $totalWeight = 0;
                    if (!empty($items)) {
                        foreach ($items as $item) {
                            $totalWeight += ($item['options']['weight'] ?? 0) * $item['qty'];
                        }
                    }
                ?>
                <?php if (!empty($items)) : ?>
                    <?php foreach ($items as $index => $item) : ?>
                        <tr>
                            <td><?= $item['name'] ?></td>
                            <td><?= $item['options']['weight'] ?> gram</td>
                            <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                            <td><?= $item['qty'] ?></td>
                            <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                    <tr>
                        <td colspan="3"></td>
                        <td><strong>Total Berat</strong></td>
                        <td><strong><?= $totalWeight ?> gram</strong></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td>Subtotal</td>
                        <td><?= number_to_currency($total, 'IDR') ?></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td>Total</td>
                        <td><span id="total"><?= number_to_currency($total, 'IDR') ?></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <button type="button" class="btn btn-primary" id="pay-button" disabled>Buat Pesanan</button>
        </div>
        </form> </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    $(document).ready(function() {
        var ongkir = 0;
        var total = 0; 
        hitungTotal();

        // =======================================================
        //      BAGIAN 1: KODE UNTUK ONGKIR (TETAP SAMA)
        // =======================================================
        $('#kelurahan').select2({
            placeholder: 'Ketik nama kelurahan...',
            ajax: {
                url: '<?= base_url('get-location') ?>',
                dataType: 'json',
                delay: 1500,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.subdistrict_name + ", " + item.district_name + ", " + item.city_name + ", " + item.province_name + ", " + item.zip_code
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });

        $("#kelurahan").on('change', function() {
            var id_kelurahan = $(this).val(); 
            $("#layanan").empty().append('<option value="" selected>-- Pilih Jenis Layanan --</option>');
            ongkir = 0;
            $.ajax({
                url: "<?= site_url('get-cost') ?>",
                type: 'GET',
                data: { 'destination': id_kelurahan, },
                dataType: 'json',
                success: function(data) { 
                    if (data.length > 0) {
                        data.forEach(function(item) {
                            var text = item["description"] + " (" + item["service"] + ") : estimasi " + item["etd"];
                            $("#layanan").append($('<option>', { value: item["cost"], text: text }));
                        });
                    } else {
                        $("#layanan").append('<option value="" disabled>No layanan available</option>');
                    }
                    hitungTotal();
                },
                error: function() { alert('Failed to load available services.'); }
            });
        });

        $("#layanan").on('change', function() {
            ongkir = parseInt($(this).val()) || 0;
            hitungTotal();
        });

        function hitungTotal() {
            total = ongkir + <?= $total ?>;
            $("#ongkir").val(ongkir);
            $("#total").html("IDR " + total.toLocaleString('id-ID'));
            $("#total_harga").val(total);
            
            // Logika untuk mengaktifkan/menonaktifkan tombol pembayaran
            if (ongkir > 0) {
                $('#pay-button').prop('disabled', false);
            } else {
                $('#pay-button').prop('disabled', true);
            }
        }

        // ================================================================
        //      BAGIAN 2: KODE BARU UNTUK PEMBAYARAN MIDTRANS
        // ================================================================
        $('#pay-button').on('click', function(e) {
            e.preventDefault();
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...');

            $.ajax({
                url: '<?= base_url('coba-proses-pembayaran') ?>',
                type: 'POST',
                data: $('#payment-form').serialize(), // Pastikan form Anda punya id="payment-form"
                dataType: 'json',
                success: function(response) {
                    console.log('Response dari server:', response);
                    
                    if (response.status === 'success' && response.snapToken) {
                        snap.pay(response.snapToken, {
                            onSuccess: function(result) {
                                alert("Pembayaran sukses!");
                                window.location.href = "<?= base_url('history') ?>";
                            },
                            onPending: function(result) {
                                alert("Pembayaran Anda tertunda.");
                                window.location.href = "<?= base_url('history') ?>";
                            },
                            onError: function(result) {
                                alert("Pembayaran gagal!");
                                $('#pay-button').prop('disabled', false).html('Buat Pesanan');
                            },
                            onClose: function() {
                                alert('Anda menutup jendela pembayaran.');
                                $('#pay-button').prop('disabled', false).html('Buat Pesanan');
                            }
                        });
                    } else {
                        alert('Gagal membuat transaksi: ' + (response.message || 'Error tidak diketahui.'));
                        $('#pay-button').prop('disabled', false).html('Buat Pesanan');
                    }
                },
                error: function(xhr) {
                    console.error('Error AJAX:', xhr.responseText);
                    alert('Tidak dapat terhubung ke server. Cek console (F12) untuk detail.');
                    $('#pay-button').prop('disabled', false).html('Buat Pesanan');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>