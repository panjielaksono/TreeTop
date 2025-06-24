<?= $this->extend('layout_clear') ?>
<?= $this->section('content') ?>

<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4" style="background-image: url('<?= base_url('NiceAdmin/assets/img/gym.jpg') ?>'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="pt-4 pb-2">
                            <h5 class="card-title text-center pb-0 fs-4">Daftar Akun</h5>
                            <p class="text-center small">Masukkan data lengkap Anda</p>
                        </div>

                        <?php
                        if (session()->getFlashData('failed')) {
                        ?>
                            <div class="col-12 alert alert-danger" role="alert">
                                <hr>
                                <p class="mb-0">
                                    <?= session()->getFlashData('failed') ?>
                                </p>
                            </div>
                        <?php
                        }
                        ?>

                        <?= form_open('register', 'class = "row g-3 needs-validation"') ?>
                        
                        <div class="col-12">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label for="pass_confirm" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="pass_confirm" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">Daftar</button>
                        </div>

                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
