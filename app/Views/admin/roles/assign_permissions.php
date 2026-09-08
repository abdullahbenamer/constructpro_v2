<h3>Assign Permissions</h3>

<div class="card mb-4">
    <div class="card-body py-2">
        <h3 class="mb-0">
            For: <span class="badge bg-primary ms-2">
                <?= strtoupper(htmlspecialchars($role->name)) ?>
            </span>
        </h3>
    </div>
</div>

<form method="POST">

<?php
// Group permissions
$grouped = [];

foreach ($permissions as $perm) {
    $parts = explode('.', $perm->name);
    $group = $parts[0];
    $grouped[$group][] = $perm;
}
?>

<?php foreach ($grouped as $group => $perms): ?>

<div class="card mb-3">

    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <strong><?= strtoupper($group) ?></strong>

        <button type="button"
                class="btn btn-sm btn-light"
                onclick="toggleGroup('<?= $group ?>')">
            Select All
        </button>
    </div>

    <div class="card-body">
        <div class="row">

            <?php foreach ($perms as $perm): ?>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="perm<?= $perm->id ?>"
                               name="permissions[]"
                               value="<?= $perm->id ?>"
                               data-group="<?= $group ?>"
                               <?= in_array($perm->id, $assigned) ? 'checked' : '' ?>>

                        <label class="form-check-label" for="perm<?= $perm->id ?>">
                            <?= $perm->name ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

</div>

<?php endforeach; ?>

<button type="submit" class="btn btn-success">
    Save Permissions
</button>

</form>

<script>
function toggleGroup(group) {
    document.querySelectorAll('input[data-group="' + group + '"]')
        .forEach(cb => cb.checked = true);
}
</script>