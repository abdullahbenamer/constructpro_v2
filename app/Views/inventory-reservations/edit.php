<h2>
    Edit Inventory Reservation
</h2>

<form method="POST">

    <div class="mb-3">

        <label>Inventory Item</label>

        <select name="inventory_id" class="form-select" required>

            <?php foreach ($inventory as $item) : ?>

                <option value="<?= $item->id ?>" <?= $reservation->inventory_id == $item->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($item->name) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="mb-3">

        <label>Location</label>

        <select name="location_id" class="form-select">
            <option value="">
                Any Location
            </option>
            <?php foreach ($locations as $loc) : ?>
<option value="<?= $loc->id ?>" <?= $reservation->location_id == $loc->id
                                                    ? 'selected'
                                                    : '' ?>>
                    <?= htmlspecialchars($loc->code) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Project</label>
        <select name="project_id" class="form-select">
            <option value="">
                No Project
            </option>
            <?php foreach ($projects as $p) : ?>
<option value="<?= $p->id ?>" <?= $reservation->project_id == $p->id
                                                    ? 'selected'
                                                    : '' ?>>
                    <?= htmlspecialchars($p->title) ?>
                </option>
            <?php endforeach; ?>

        </select>

    </div>

    <div class="mb-3">

        <label>Quantity</label>

        <input type="number" step="0.01" min="0.01" name="quantity" value="<?= $reservation->quantity ?>" class="form-control" required>

    </div>

    <div class="mb-3">

        <label>Reference</label>

        <input type="text" name="reference" value="<?= htmlspecialchars($reservation->reference) ?>" class="form-control">

    </div>

    <div class="mb-3">

        <label>Notes</label>

        <textarea name="notes" class="form-control"><?= htmlspecialchars($reservation->notes) ?></textarea>

    </div>

    <button class="btn btn-primary">

        Save Update

    </button>

</form>