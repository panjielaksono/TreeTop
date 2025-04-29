<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<!-- Button to Add User -->
<a href="<?= base_url('users/create') ?>" class="btn btn-primary mb-3">Tambah User</a>

<!-- Table with users -->
<table class="table datatable">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Username</th>
            <th scope="col">Role</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $index => $user): ?>
                <tr>
                    <th scope="row"><?= $index + 1 ?></th>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['role']) ?></td>
                    <td>
                        <!-- Edit Button -->
                        <a href="<?= base_url('users/edit/' . esc($user['username'])) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <!-- Delete Button -->
                        <a href="<?= base_url('users/delete/' . esc($user['username'])) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No users found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<!-- End Table -->
<?= $this->endSection() ?>
