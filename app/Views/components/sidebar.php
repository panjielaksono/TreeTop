<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'home') ? "active" : "collapsed" ?>" href="<?= base_url('home') ?>">
                    <i class="bi bi-house-door"></i>
                    <span>Home</span>
                </a>
            </li><!-- End Dashboard User Nav -->

        <!-- Dashboard for admin -->
        <?php if (session()->get('role') == 'admin') { ?>
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'product') ? 'active' : 'collapsed' ?>" href="<?= base_url('product') ?>">
                    <i class="bi bi-box"></i>
                    <span>Product</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'admin') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin') ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'admin/users') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin/users') ?>">
                    <i class="bi bi-person"></i>
                    <span>User Management</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'admin/membership') ? 'active' : 'collapsed' ?>" href="<?= base_url('admin/membership') ?>">
                    <i class="bi bi-card-list"></i>
                    <span>Membership</span>
                </a>
            </li>
        <?php } ?>

        <!-- Menu for user -->
        <?php if (session()->get('role') == 'guest') { ?>
            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'guest') ? 'active' : 'collapsed' ?>" href="<?= base_url('guest') ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (uri_string() == 'guest/member') ? 'active' : 'collapsed' ?>" href="<?= base_url('guest/member') ?>">
                    <i class="bi bi-card-list"></i>
                    <span>Member</span>
                </a>
            </li>
        <?php } ?>

    </ul>

</aside><!-- End Sidebar-->
