<h2><i class="fas fa-edit"></i> Edit Cost Item #<?= $cost->id ?></h2>
<div class="alert alert-danger my-3">
    <strong>Note:</strong> Changing material item, warehouse, or cost type requires deleting the existing cost and creating a new cost.
</div>
<form method="POST">

    <input type="hidden"
           name="project_id"
           value="<?= $project_id ?>">

    <input type="hidden"
           name="cost_type"
           value="<?= $cost->cost_type ?>">

    <!-- <input type="hidden"
           name="inventory_id"
           value="<?//= $cost->inventory_id ?>">

    <input type="hidden"
           name="location_id"
           value="<?//= $cost->location_id ?>"> -->

    <div class="row">

        <!-- COST TYPE -->
        <div class="col-md-3">
            <label class="form-label">Cost Type</label>
            <span class="badge bg-primary">
    <?= strtoupper($cost->cost_type) ?>
</span>
        </div>

        <!-- DESCRIPTION -->
        <div class="col-md-5">
            <label class="form-label">Description</label>
            <input type="text" name="description"
               value="<?= htmlspecialchars($cost->description ?? '') ?>"
                class="form-control" required>
        </div>

        <!-- QTY -->
        <div class="col-md-2">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity"
                value="<?= $cost->quantity ?>"
                class="form-control" min="0" step="0.01" required>
        </div>

        <!-- PRICE -->
        <div class="col-md-2">
            <label class="form-label" id="priceLabel">Unit Cost ($)</label>
            <?php if ($cost->cost_type === 'materials'): ?>

<input type="number"
       value="<?= $cost->unit_price ?>"
       class="form-control"
       step="0.01"
       readonly>

<input type="hidden"
       name="unit_price"
       value="<?= $cost->unit_price ?>">

<?php else: ?>

<input type="number"
       name="unit_price"
       value="<?= $cost->unit_price ?>"
       step="0.01"
       class="form-control"
       required>

<?php endif; ?>
        </div>

    </div>

    <!-- INVENTORY ITEM read-only -->
  <?php if ($cost->cost_type == 'materials'): ?>

<div class="col-md-6 mt-2">
    <label class="form-label">Inventory Item</label>

    <select class="form-select" disabled>

        <?php foreach ($inventory as $item): ?>

            <?php if ($item->id == $cost->inventory_id): ?>

                <option selected>
                    <?= htmlspecialchars($item->name) ?>
                </option>

            <?php endif; ?>

        <?php endforeach; ?>

    </select>

</div>

<?php endif; ?>


    <!-- LOCATION for Materials read-only -->
   <?php if ($cost->cost_type == 'materials'): ?>

<div class="col-md-6 mt-2">

    <label class="form-label">
        Warehouse Location
    </label>

    <select class="form-select" disabled>

        <?php foreach ($locations as $location): ?>

            <?php if ($location->id == $cost->location_id): ?>

                <option selected>

                    <?= htmlspecialchars($location->code) ?>
                    -
                    <?= htmlspecialchars($location->name) ?>

                </option>

            <?php endif; ?>

        <?php endforeach; ?>

    </select>

</div>

<?php endif; ?>

    <button type="submit" class="btn btn-success mt-3">
        <i class="fas fa-save"></i> Update Cost
    </button>

    <a href="<?= URLROOT ?>/project-costs/<?= $project_id ?>" class="btn btn-secondary mt-3">Cancel</a>
</form>

<!-- JS scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {  
        
        const unitPrice = document.getElementById('unitPrice');
        const priceLabel = document.getElementById('priceLabel');

    });
</script>