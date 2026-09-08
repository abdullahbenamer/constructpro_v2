<h3>Edit User</h3>

<form method="POST">

    <div class="mb-2">
        <label>Name</label>
        <input type="text" name="name" class="form-control"
            value="<?= htmlspecialchars($user->name) ?>" required>
    </div>

    <div class="mb-2">
        <label>Email</label>
        <input type="email" name="email" class="form-control"
            value="<?= htmlspecialchars($user->email) ?>" required>
    </div>

    <div class="mb-2">
        <label>Password (leave blank to keep current)</label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="mb-2">
        <label>Role</label>
        <select name="role_id" class="form-control">
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role->id ?>"
                    <?= $user->role_id == $role->id ? 'selected' : '' ?>>
                    <?= $role->name ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <h5>Warehouse Access</h5>

    <?php foreach ($locations as $loc): ?>

        <div class="form-check">

            <input
                class="form-check-input"
                type="checkbox"
                name="locations[]"
                value="<?= $loc->id ?>"

                <?= in_array($loc->id, $assigned_locations)
                    ? 'checked'
                    : '' ?>>

            <label class="form-check-label">
                <?= htmlspecialchars($loc->name) ?>
            </label>

        </div>

    <?php endforeach; ?>

    <!-- Default warehouse -->
    <div class="mb-3">

        <label>Default Warehouse</label>

        <select
            name="default_location_id"
            class="form-control">

            <?php foreach ($locations as $loc): ?>

                <option
                    value="<?= $loc->id ?>"
                    <?= $user->default_location_id == $loc->id
                        ? 'selected'
                        : '' ?>>

                    <?= htmlspecialchars($loc->name) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <button class="btn btn-success">Update User</button>

</form>