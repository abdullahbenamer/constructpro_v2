<h3>Add Stock Movement</h3>

<form method="POST">
    <input type="hidden" name="inventory_id" value="<?= $inventory_id ?>">

    <div class="mb-3">
        <label>Type</label>
        <select name="type" class="form-select">
            <option value="IN">Stock In</option>
            <option value="OUT">Stock Out</option>
            <option value="ADJUSTMENT">Adjustment</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Location</label>

        <select name="location_id" class="form-select" required>

            <option value="">-- Select Stock Location --</option>

            <?php foreach ($locations as $location) : ?>

                <option value="<?= $location->id ?>">

                    <?= htmlspecialchars($location->code) ?>
                    -
                    <?= htmlspecialchars($location->name) ?>

                </option>

            <?php endforeach; ?>

        </select>
    </div>
    <div class="mb-3">
        <label>Quantity</label>
        <input type="number" name="quantity" class="form-control" step="0.01" min="0.01" required>
    </div>

     <div class="mb-3">
        <label>Reference</label>
        <input type="text" name="reference" class="form-control">
    </div>

    <div class="mb-3">
        <label>Notes</label>
        <textarea name="notes" class="form-control"></textarea>
    </div>

    <button class="btn btn-success">Save</button>
</form>