<ul class="list-group mt-3">

<?php foreach ($roles as $role): ?>

<li class="list-group-item d-flex justify-content-between align-items-center">

    <div>
        <strong><?= $role->name ?></strong>

        <!-- ✅ INSERT BADGE HERE -->
        <span class="badge bg-secondary ms-2">
            <?= count($role->permissions ?? []) ?> permissions
        </span>

        <br>

        <small class="text-muted">
            <?= !empty($role->permissions)
                ? implode(', ', array_slice($role->permissions, 0, 3))
                : 'No permissions' ?>
        </small>
    </div>

    <div>
        <a href="<?= URLROOT ?>/admin/assignPermissions/<?= $role->id ?>" 
           class="btn btn-sm btn-info">Permissions</a>

        <a href="<?= URLROOT ?>/admin/editRole/<?= $role->id ?>" 
           class="btn btn-sm btn-warning">Edit</a>

        <a href="<?= URLROOT ?>/admin/deleteRole/<?= $role->id ?>" 
           class="btn btn-sm btn-danger"
           onclick="return confirm('Delete role?')">
           Delete
        </a>
    </div>

</li>

<?php endforeach; ?>

</ul>