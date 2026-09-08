<h3>Users</h3>

<a href="<?= URLROOT ?>/admin/createUser" class="btn btn-primary mb-2">
    Add User
</a>

<table class="table table-bordered">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>

    <?php foreach ($users as $user) : ?>
        <tr>
            <td><?= $user->name ?></td>
            <td><?= $user->email ?></td>
            <td><?= $user->role_name ?></td>

            <td>
                <a href="<?= URLROOT ?>/admin/editUser/<?= $user->id ?>" class="btn btn-sm btn-warning">
                    Edit
                </a>

                <?php
                // HIDE DELETE BUTTON for last admin
                $is_self = ($user->id == $_SESSION['user_id']);
                $is_admin = (strtoupper($user->role_name) === 'ADMIN');
                ?>
                
                <?php if (!$is_self) : ?>
                
                    <a href="<?= URLROOT ?>/users/delete/<?= $user->id ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Delete this user?')">
                
                        Delete
                    </a>
                
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>