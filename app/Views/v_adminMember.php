<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h5 class="text-center mb-4">Data Langganan</h5>


<table class="table datatable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nama Pengguna</th>
            <th scope="col">Jenis Langganan</th>
            <th scope="col">Tanggal Langganan</th>
            <th scope="col">Tanggal Kadaluarsa</th>
            <th scope="col">Hentikan</th>
        </tr>
    </thead>
    <tbody>
   
        <tr>
            <th scope="row">1</th>
            <td> <?= esc($username) ?></td>
            <td>Bulanan</td>
            <td>12 April 2025</td>
            <td>12 Mei 2025</td>
            <td>
              
                <button class="btn btn-danger btn-sm" style="font-size: 16px; padding: 0.3rem 0.6rem; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-x text-white" style="font-size: 14px;"></i>
                </button>
            </td>
        </tr>
    </tbody>
</table>

<?= $this->endSection() ?>
