<h3>Permissions</h3>

<form method="POST">
    <input type="text" name="name" placeholder="Permission name" class="form-control mb-2">
    <button class="btn btn-primary">Add Permission</button>
</form>

<ul class="list-group mt-3">
    <?php foreach ($permissions as $perm): ?>
        <li class="list-group-item"><?= $perm->name ?></li>
    <?php endforeach; ?>
</ul>   