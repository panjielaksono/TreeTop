<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<form action="<?= base_url('users/update/' . esc($user['username'])) ?>" method="post">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?= esc($user['username']) ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password" value="<?= esc($user['password']) ?>" required>
    </div>
    <div class="form-group">
        <label for="role">Role</label>
        <select class="form-control" id="role" name="role">
            <option value="user" <?= ($user['role'] == 'user') ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-warning">Update User</button>
</form>
<?= $this->endSection() ?>
