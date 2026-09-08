<h2>Edit/Update Inventory Location</h2>

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
               value="<?= htmlspecialchars($_POST['code'] ?? $location->code) ?>" required>
    </div>

    <!-- Name -->
    <div class="mb-3">
        <label>Warehouse Name</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($_POST['name'] ?? $location->name) ?>" required>
    </div>

    <!-- Address -->
    <div class="mb-3">
        <label>Address / Location</label>
        <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($_POST['address'] ?? $location->address) ?></textarea>
    </div>

    <!-- Responsible Storekeeper -->
    <div class="mb-3">
        <label>Responsible Storekeeper</label>
        <select name="storekeeper_id" class="form-select">
            <option value="">-- Select Storekeeper --</option>

            <?php foreach ($storekeepers as $user): ?>

                <?php 
                    $selectedStorekeeper = $_POST['storekeeper_id'] ?? $location->storekeeper_id;
                ?>

                <option value="<?= $user->id ?>"
                    <?= ($selectedStorekeeper == $user->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user->full_name) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </div>

 <?php
$assignedUsers = $_POST['user_locations'] ?? $assignedUsers ?? [];
?>

<!-- Warehouse Team -->
<div class="mb-3">

    <label class="form-label">
        Warehouse Team
    </label>

    <select
        name="user_locations[]"
        class="form-select"
        multiple
        size="8">

        <?php foreach ($users as $user): ?>

            <option value="<?= $user->id ?>"
                <?= in_array($user->id, $assignedUsers) ? 'selected' : '' ?>>

                <?= htmlspecialchars($user->full_name) ?>

            </option>

        <?php endforeach; ?>

    </select>

    <small class="text-muted">
        Hold Ctrl to select multiple users who are authorized to access this warehouse.
    </small>

</div>

    <!-- Mobile -->
    <div class="mb-3">
        <label>Mobile Number</label>
        <input type="text" name="mobile" class="form-control"
               value="<?= htmlspecialchars($_POST['mobile'] ?? $location->mobile) ?>">
    </div>

    <!-- Notes -->
    <div class="mb-3">
        <label>Notes</label>
        <textarea name="notes" class="form-control"><?= htmlspecialchars($_POST['notes'] ?? $location->notes) ?></textarea>
    </div>

    <button class="btn btn-primary">
        Update Location
    </button>

    <a href="<?= URLROOT ?>/inventorylocations" class="btn btn-secondary">
        Cancel
    </a>

</form>