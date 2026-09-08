<?php
$project = $project ?? null;
$project_id = $project_id ?? null;
$inventory = $inventory ?? [];
$locations = $locations ?? [];
?>

<div class="card mb-3">
    <h3>Add Cost to Project</h3>
    <div class="card-body">
        <h4>
            Project #<?= $project->id ?> -
            <?= htmlspecialchars($project->title) ?>
        </h4>
        <p class="mb-0">
            <strong>Customer:</strong> <?= htmlspecialchars($project->customer_name ?? '') ?><br>
            <strong>Status:</strong> <?= ucfirst($project->status) ?><br>
            <strong>Budget:</strong> $<?= number_format($project->budget ?? 0, 2) ?>
        </p>
    </div>
</div>
<!-- ---------------------------------------- -->
<form method="POST" action="">
    <input type="hidden" name="project_id" value="<?= $project->id ?>">
    <div class="row">

        <div class="col-md-3">
            <label class="form-label">Cost Type</label>

            <select name="cost_type" id="costType" class="form-select">
                <option value="materials">MATERIALS</option>
                <option value="labor">LABOR</option>
                <option value="transport">TRANSPORT</option>
                <option value="subcontract">SUBCONTRACT</option>
                <option value="misc">MISCELLANEOUS</option>
            </select>
        </div>

        <!-- Material Item -->
        <div class="col-md-4" id="inventoryBlock">
            <label class="form-label">Inventory Item</label>

            <select name="inventory_id" id="inventorySelect" class="form-select" required>
                <option value="">-- Select Item --</option>

                <?php foreach ($inventory as $item) : ?>
                    <option value="<?= $item->id ?>"
                        data-cost="<?= $item->cost_price ?>">
                        <?= $item->name ?>
                        (
                        Available: <?= $item->available_qty ?>
                        /
                        Physical: <?= $item->quantity ?>
                        )
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <!-- Cost/Item Description -->
        <div class="col-md-5">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" required placeholder="Description of the Resource ....">
        </div>

        <!-- Location -->
        <div class="col-md-4" id="locationBlock">

            <label class="form-label">Location</label>

            <select name="location_id" id="locationSelect" class="form-select" required>

                <option value="">-- Select Location --</option>

                <?php foreach ($locations as $location) : ?>
                    <option value="<?= $location->id ?>">
                        <?= htmlspecialchars($location->code) ?>
                        -
                        <?= htmlspecialchars($location->name) ?>
                    </option>
                <?php endforeach; ?>

            </select>

        </div>

        <div class="col-md-2">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" value="" min="0" step="0.01" required>
            <small id="qtyWarning" class="text-danger">
    (Don't exceed the available quantity in any Warehouse)
</small>
        </div>

        <div class="col-md-2">
            <label class="form-label" id="priceLabel">Unit Cost ($)</label>
            <input type="number" name="unit_price" class="form-control" step="0.01" required placeholder="Autofill for Materials ...">
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save"></i> Add Cost
    </button>
    <a href="<?= URLROOT ?>/project-costs/<?= $project_id ?>" class="btn btn-secondary mt-3">Cancel</a>
</form>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==================================================
    // ELEMENTS
    // ==================================================

    const costType = document.getElementById('costType');

    const inventoryBlock = document.getElementById('inventoryBlock');
    const locationBlock  = document.getElementById('locationBlock');

    const inventorySelect = document.getElementById('inventorySelect');
    const locationSelect  = document.getElementById('locationSelect');

    const descriptionInput = document.querySelector('[name="description"]');
    const quantityInput    = document.querySelector('[name="quantity"]');
    const unitPriceInput   = document.querySelector('[name="unit_price"]');

    const qtyWarning = document.getElementById('qtyWarning');


    // ==================================================
    // MATERIAL / NON-MATERIAL MODE
    // ==================================================

    function updateMode() {

        const material = (costType.value === 'materials');

        inventoryBlock.style.display = material ? '' : 'none';
        locationBlock.style.display  = material ? '' : 'none';

        qtyWarning.style.display = material ? '' : 'none';

        inventorySelect.required = material;
        locationSelect.required  = material;

        unitPriceInput.readOnly = material;

        if (!material) {

            inventorySelect.value = '';
            locationSelect.innerHTML =
                '<option value="">-- Select Location --</option>';

            descriptionInput.value = '';
            unitPriceInput.value = '';

        } else {

            autoFillMaterial();

        }

    }


    // ==================================================
    // AUTO FILL MATERIAL INFO
    // ==================================================

    function autoFillMaterial() {

        const option =
            inventorySelect.options[inventorySelect.selectedIndex];

        if (!option || !inventorySelect.value) {

            descriptionInput.value = '';
            unitPriceInput.value = '';

            return;

        }

        descriptionInput.value =
            option.text.split('(')[0].trim();

        unitPriceInput.value =
            parseFloat(
                option.dataset.cost || 0
            ).toFixed(2);

    }


    // ==================================================
    // LOAD LOCATIONS
    // ==================================================

    function loadLocations() {

        const inventoryId = inventorySelect.value;

        if (!inventoryId) {

            locationSelect.innerHTML =
                '<option value="">-- Select Location --</option>';

            return;

        }

        fetch(
            '<?= URLROOT ?>/project-costs/getInventoryLocations/' +
            inventoryId
        )
        .then(r => r.json())
        .then(data => {

            let html =
                '<option value="">-- Select Location --</option>';

            data.forEach(function(location){

                html += `
                    <option value="${location.location_id}">
                        ${location.code}
                        - ${location.name}
                        (Qty: ${location.quantity})
                    </option>
                `;

            });

            locationSelect.innerHTML = html;

        });

    }


    // ==================================================
    // EVENTS
    // ==================================================

    costType.addEventListener('change', updateMode);

    inventorySelect.addEventListener('change', function () {

        autoFillMaterial();

        loadLocations();

    });


    // ==================================================
    // INITIAL PAGE
    // ==================================================

    updateMode();

});
</script>