<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Menu untuk Semua Pengguna -->
        <li class="nav-item">
            <a class="nav-link <?= (uri_string() == 'home') ? 'active' : 'collapsed' ?>" href="<?= base_url('home') ?>">
                <i class="bi bi-house-door"></i>
                <span>Home</span>
            </a>
        </li>

        <!-- Menu untuk Admin -->
        <?php if (session()->get('role') == 'admin') : ?>
            <!-- Dashboard (Admin) -->
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'admin') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin') ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Product Management -->
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'product') ? 'active' : 'collapsed' ?>" href="<?= base_url('product') ?>">
                    <i class="bi bi-box"></i>
                    <span>Product</span>
                </a>
            </li>

            <!-- User Management -->
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'admin/users') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin/users') ?>">
                    <i class="bi bi-person"></i>
                    <span>User Management</span>
                </a>
            </li>

            <!-- Membership Management -->
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'admin/membership') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin/membership') ?>">
                    <i class="bi bi-card-list"></i>
                    <span>Membership</span>
                </a>
            </li>

            <!-- Transaction Management -->
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'admin/transaksi' || uri_string() == 'admin/transaksi/pending' || uri_string() == 'admin/transaksi/selesai' || uri_string() == 'admin/transaksi/dibatalkan') ? 'active' : 'collapsed' ?>" href="#" data-bs-toggle="collapse" data-bs-target="#transaksi-submenu" aria-expanded="<?= (uri_string() == 'admin/transaksi' || uri_string() == 'admin/transaksi/pending' || uri_string() == 'admin/transaksi/selesai' || uri_string() == 'admin/transaksi/dibatalkan') ? 'true' : 'false' ?>" aria-controls="transaksi-submenu">
                    <i class="bi bi-wallet"></i>
                    <span>Transaksi</span>
                </a>
                <ul class="collapse <?= (uri_string() == 'admin/transaksi' || uri_string() == 'admin/transaksi/pending' || uri_string() == 'admin/transaksi/selesai' || uri_string() == 'admin/transaksi/dibatalkan') ? 'show' : '' ?>" id="transaksi-submenu">
                    <li class="nav-item">
                        <a class="nav-link <?= (uri_string() == 'admin/transaksi/pending') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin/transaksi/pending') ?>">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (uri_string() == 'admin/transaksi/selesai') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin/transaksi/selesai') ?>">Selesai</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (uri_string() == 'admin/transaksi/dibatalkan') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin/transaksi/dibatalkan') ?>">Dibatalkan</a>
                    </li>
                </ul>
            </li>

        <?php endif; ?>

        <!-- Menu untuk Guest -->
        <?php if (session()->get('role') == 'guest') : ?>
            <!-- Dashboard (Guest) -->
            <!-- <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'guest') ? 'active' : 'collapsed' ?>" href="<?= base_url('guest') ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li> -->
                
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'keranjang') ? 'active' : 'collapsed' ?>" href="<?= base_url('keranjang') ?>">
                    <i class="bi bi-cart"></i>
                    <span>Cart</span>
                </a>
            </li>
            <!-- Member Purchase -->
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'guest/member') ? 'active' : 'collapsed' ?>" href="<?= base_url('guest/member') ?>">
                    <i class="bi bi-card-list"></i>
                    <span>Member</span>
                </a>
            </li>

            <!-- Transaction History -->
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'history') ? 'active' : 'collapsed' ?>" href="<?= base_url('history') ?>">
                    <i class="bi bi-card-list"></i>
                    <span>Riwayat Pembelian</span>
                </a>
            </li>

            <!-- Profile Management -->
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'guest/profile') ? 'active' : 'collapsed' ?>" href="<?= base_url('guest/profile') ?>">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                </a>
            </li>
        <?php endif; ?>

    </ul>

</aside>
