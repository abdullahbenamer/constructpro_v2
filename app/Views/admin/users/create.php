<h3>Create User</h3>

<form method="POST">

    <<div class="mb-2">
    <label>Full Name</label>
    <input type="text"
           name="full_name"
           class="form-control"
           required>
</div>

<div class="mb-2">
    <label>Short name</label>
    <input type="text"
           name="name"
           class="form-control"
           required>
</div>

    <div class="mb-2">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Role</label>
        <select name="role_id" class="form-control">
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role->id ?>"><?= $role->name ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <h5>Warehouse Access</h5>

<?php foreach ($locations as $loc): ?>

<div class="form-check">

    <input
        type="checkbox"
        class="form-check-input"
        name="locations[]"
        value="<?= $loc->id ?>">

    <label class="form-check-label">
        <?= htmlspecialchars($loc->name) ?>
    </label>

</div>

<?php endforeach; ?>

<hr>

<label>Default Warehouse</label>

<select
    name="default_location_id"
    class="form-control">

    <option value="">
        Select Default Warehouse
    </option>

    <?php foreach ($locations as $loc): ?>

        <option value="<?= $loc->id ?>">
            <?= htmlspecialchars($loc->name) ?>
        </option>

    <?php endforeach; ?>

</select>

    <button class="btn btn-success">Create</button>

</form>