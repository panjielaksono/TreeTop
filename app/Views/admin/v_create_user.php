<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<form action="<?= base_url('admin/users/store') ?>" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label> <!-- Tambahkan input email -->
        <input type="email" class="form-control" id="email" name="email" required>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <div class="form-group">
        <label for="phone_number">Phone Number</label> <!-- Tambahkan input phone_number -->
        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
    </div>

    <div class="form-group">
        <label for="role">Role</label>
        <select class="form-control" id="role" name="role">
            <option value="guest">Guest</option>
            <option value="admin">Admin</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Tambah User</button>
</form>
<?= $this->endSection() ?>
