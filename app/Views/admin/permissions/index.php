<h3>Permissions</h3>

<form method="POST">
    <input type="text" name="name" placeholder="Permission name" class="form-control mb-2">
    <button class="btn btn-primary">Add Permission</button>
</form>

<ul class="list-group mt-3">
<?php foreach ($permissions as $perm): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">

        <?= $perm->name ?>

        <div>
            <a href="<?= URLROOT ?>/admin/editPermission/<?= $perm->id ?>" 
               class="btn btn-sm btn-warning">Edit</a>

            <a href="<?= URLROOT ?>/admin/deletePermission/<?= $perm->id ?>" 
               class="btn btn-sm btn-danger"
               onclick="return confirm('Delete permission?')">
               Delete
            </a>
        </div>

    </li>
<?php endforeach; ?>
</ul>