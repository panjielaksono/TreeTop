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
                <a class="nav-link <?php echo (uri_string() == 'admin') ? "active" : "collapsed" ?>" href="<?= base_url('admin') ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Admin Nav -->
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'users') ? "active" : "collapsed" ?>" href="<?= base_url('users') ?>">
                    <i class="bi bi-person"></i>
                    <span>Users</span>
                </a>
            </li><!-- End Users Nav -->
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'membership') ? "active" : "collapsed" ?>" href="<?= base_url('membership') ?>">
                    <i class="bi bi-card-list"></i>
                    <span>Membership</span>
                </a>
            </li><!-- End Users Nav -->
        <?php } ?>

        <!-- Dashboard for user -->
        <?php if (session()->get('role') == 'guest') { ?>
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'user') ? "active" : "collapsed" ?>" href="<?= base_url('user') ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard User Nav -->
            <li class="nav-item">
                <a class="nav-link <?php echo (uri_string() == 'member') ? "active" : "collapsed" ?>" href="<?= base_url('member') ?>">
                    <i class="bi bi-card-list"></i>
                    <span>Member</span>
                </a>
            </li><!-- End Dashboard User Nav -->
        <?php } ?>
    </ul>

</aside><!-- End Sidebar-->
