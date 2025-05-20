<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row">
    <?php foreach ($product as $key => $item) : ?>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <img src="<?php echo base_url() . "img/" . $item['foto'] ?>" alt="..." width="300px">
                    <h5 class="card-title"><?php echo $item['nama'] ?><br>Rp <?php echo number_format($item['harga'], 0, ',', '.') ?></h5>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>
 <?= $this->endSection() ?>
