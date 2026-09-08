<h2>Add Inventory Location</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="POST">

    <!-- Code -->
    <div class="mb-3">
        <label>Code</label>
        <input type="text" name="code" class="form-control"
            value="<?= htmlspecialchars($_POST['code'] ?? '') ?>" required>
    </div>

    <!-- Name -->
    <div class="mb-3">
        <label>Warehouse Name</label>
        <input type="text" name="name" class="form-control"
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
    </div>

    <!-- Address -->
    <div class="mb-3">
        <label>Address / Location</label>
        <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
    </div>

    <!-- Storekeeper -->
    <div class="mb-3">
        <label>Responsible Storekeeper</label>
        <select name="storekeeper_id" class="form-select">
            <option value="">-- Select Storekeeper --</option>

            <?php foreach ($storekeepers as $user): ?>
                <option value="<?= $user->id ?>"
                    <?= (($_POST['storekeeper_id'] ?? '') == $user->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user->full_name) ?>
                </option>
            <?php endforeach; ?>

        </select>
    </div>

    <!-- Warehouse Team -->
    <div class="mb-3">

        <label class="form-label">
            Authorized Users
        </label>
        <select
            name="user_locations[]"
            class="form-select"
            multiple
            size="8">

            <?php foreach ($users as $user): ?>

                <option value="<?= $user->id ?>">

                    <?= htmlspecialchars($user->full_name) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <small class="text-muted">
            Hold Ctrl to select multiple users.
            Select the users who are authorized to access this inventory location.
        </small>

    </div>

    <!-- Mobile -->
    <div class="mb-3">
        <label>Mobile Number</label>
        <input type="text" name="mobile" class="form-control"
            value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>">
    </div>

    <!-- Notes -->
    <div class="mb-3">
        <label>Notes</label>
        <textarea name="notes" class="form-control"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
    </div>

    <button class="btn btn-success">Save Warehouse</button>

</form>