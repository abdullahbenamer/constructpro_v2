<h2 class="mb-4">
    <i class="fas fa-sliders-h"></i>
    Stock Adjustment
</h2>

<div id="jsNotification"></div>

<form method="POST" id="adjustmentForm">

    <!-- ITEM -->

    <div class="mb-3">

        <label class="form-label">
            Inventory Item
            <span class="text-danger">*</span>
        </label>

        <select
            name="inventory_id"
            id="inventory_id"
            class="form-select"
            required>

            <option value="">
                Select Inventory Item
            </option>

            <?php foreach ($inventory as $item): ?>

                <option
                    value="<?= (int)$item->id ?>"
                    <?= (
                        (int)$item->id ===
                        (int)($selected_inventory_id ?? 0)
                    ) ? 'selected' : '' ?>>

                    <?= htmlspecialchars($item->name) ?>

                    <?php if (!empty($item->sku)): ?>
                        — <?= htmlspecialchars($item->sku) ?>
                    <?php endif; ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>


    <!-- LOCATION -->

    <div class="mb-3">

        <label class="form-label">

            Location

            <span class="text-danger">*</span>

        </label>

        <select
            name="location_id"
            id="location_id"
            class="form-select"
            required>

            <option value="">
                Select Location
            </option>

            <?php foreach ($locations as $location): ?>

                <option value="<?= (int)$location->id ?>">

                    <?= htmlspecialchars($location->code) ?>
                    -
                    <?= htmlspecialchars($location->name) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>


    <!-- CURRENT STOCK INFO -->

    <div
        id="stockInfo"
        class="alert alert-info d-none"
        style="max-width: 500px;">

        <div>
            Physical Stock:
            <strong id="physicalQty">0.00</strong>
        </div>

        <div>
            Reserved:
            <strong id="reservedQty">0.00</strong>
        </div>

        <div>
            Available for Adjustment:
            <strong id="availableQty">0.00</strong>
        </div>

    </div>


    <!-- ADJUSTMENT TYPE -->

    <div class="mb-3">

        <label class="form-label">

            Adjustment Type

            <span class="text-danger">*</span>

        </label>

        <select
            name="adjustment_type"
            id="adjustment_type"
            class="form-select"
            required>

            <option value="">
                Select Adjustment Type
            </option>

            <option value="INCREASE">
                Increase Stock
            </option>

            <option value="DECREASE">
                Decrease Stock
            </option>

        </select>

    </div>


    <!-- QUANTITY -->

    <div
        class="mb-3"
        style="max-width: 400px;">

        <label class="form-label">

            Quantity

            <span class="text-danger">*</span>

        </label>

        <input
            type="number"
            name="quantity"
            id="quantity"
            class="form-control"
            step="0.01"
            min="0.01"
            required>

        <small
            id="quantityHelp"
            class="text-muted">
        </small>

    </div>


    <!-- NEW BALANCE -->

    <div
        id="balancePreview"
        class="alert alert-secondary d-none"
        style="max-width: 500px;">

        New Physical Balance:

        <strong id="newBalance">
            0.00
        </strong>

        <span id="baseUnit"></span>

    </div>


    <!-- REASON -->

    <div class="mb-3">

        <label class="form-label">

            Reason

            <span class="text-danger">*</span>

        </label>

        <select
            name="reason"
            id="reason"
            class="form-select"
            required>

            <option value="">
                Select Reason
            </option>

            <option value="DAMAGED">
                Damaged
            </option>

            <option value="BROKEN">
                Broken
            </option>

            <option value="LOST">
                Lost
            </option>

            <option value="FOUND">
                Found
            </option>

            <option value="PHYSICAL_COUNT_CORRECTION">
                Physical Count Correction
            </option>

            <option value="EXPIRED">
                Expired
            </option>

            <option value="OTHER">
                Other
            </option>

        </select>

    </div>


    <!-- NOTES -->

    <div class="mb-3">

        <label class="form-label">
            Notes
        </label>

        <textarea
            name="notes"
            id="notes"
            class="form-control"
            rows="3"
            placeholder="Additional explanation..."></textarea>

    </div>


    <!-- ACTIONS -->

    <button
        type="submit"
        id="submitBtn"
        class="btn btn-primary">

        <i class="fas fa-sliders-h"></i>
        Post Adjustment

    </button>

    <a
        href="<?= URLROOT ?>/inventory"
        class="btn btn-secondary">

        Cancel

    </a>

</form>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const inventorySelect =
            document.getElementById('inventory_id');

        const locationSelect =
            document.getElementById('location_id');

        const adjustmentType =
            document.getElementById('adjustment_type');

        const quantityInput =
            document.getElementById('quantity');

        const stockInfo =
            document.getElementById('stockInfo');

        const physicalQty =
            document.getElementById('physicalQty');

        const reservedQty =
            document.getElementById('reservedQty');

        const availableQty =
            document.getElementById('availableQty');

        const quantityHelp =
            document.getElementById('quantityHelp');

        const balancePreview =
            document.getElementById('balancePreview');

        const newBalance =
            document.getElementById('newBalance');

        const submitBtn =
            document.getElementById('submitBtn');

        let currentPhysical = 0;
        let currentReserved = 0;
        let currentAvailable = 0;


        function resetStockInfo() {

            currentPhysical = 0;
            currentReserved = 0;
            currentAvailable = 0;

            stockInfo.classList.add('d-none');

            balancePreview.classList.add('d-none');

            quantityInput.removeAttribute('max');

            quantityInput.value = '';

            quantityHelp.textContent = '';

        }


        function loadStock() {

            const inventoryId =
                inventorySelect.value;

            const locationId =
                locationSelect.value;

            if (!inventoryId || !locationId) {

                resetStockInfo();

                return;
            }


            fetch(
                    '<?= URLROOT ?>/stockadjustments/getLocationStock', {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },

                        body: 'inventory_id=' +
                            encodeURIComponent(inventoryId) +
                            '&location_id=' +
                            encodeURIComponent(locationId)
                    }
                )
                .then(response => response.json())
                .then(data => {

                    currentPhysical =
                        parseFloat(data.physical_qty || 0);

                    currentReserved =
                        parseFloat(data.reserved_qty || 0);

                    currentAvailable =
                        parseFloat(data.available_qty || 0);


                    physicalQty.textContent =
                        currentPhysical.toFixed(2);

                    reservedQty.textContent =
                        currentReserved.toFixed(2);

                    availableQty.textContent =
                        currentAvailable.toFixed(2);


                    stockInfo.classList.remove('d-none');


                    if (
                        adjustmentType.value === 'DECREASE'
                    ) {

                        quantityInput.max =
                            currentAvailable;

                        quantityHelp.textContent =
                            'Maximum decrease: ' +
                            currentAvailable.toFixed(2);

                    } else {

                        quantityInput.removeAttribute('max');

                        quantityHelp.textContent =
                            'Increase is not limited by current stock.';

                    }

                    updatePreview();

                })
                .catch(error => {

                    console.error(
                        'Error loading stock:',
                        error
                    );

                    resetStockInfo();

                });

        }


        function updatePreview() {

            if (
                !inventorySelect.value ||
                !locationSelect.value ||
                !adjustmentType.value ||
                !quantityInput.value
            ) {

                balancePreview.classList.add('d-none');

                return;
            }


            const quantity =
                parseFloat(quantityInput.value || 0);


            if (quantity <= 0) {

                balancePreview.classList.add('d-none');

                return;
            }


            let result = currentPhysical;


            if (
                adjustmentType.value === 'INCREASE'
            ) {

                result += quantity;

            } else {

                result -= quantity;

            }


            if (result < 0) {

                balancePreview.classList.add('d-none');

                return;
            }


            newBalance.textContent =
                result.toFixed(2);

            balancePreview.classList.remove('d-none');

        }


        inventorySelect.addEventListener(
            'change',
            loadStock
        );

        locationSelect.addEventListener(
            'change',
            loadStock
        );


        adjustmentType.addEventListener(
            'change',
            function() {

                if (
                    this.value === 'DECREASE'
                ) {

                    quantityInput.max =
                        currentAvailable;

                    quantityHelp.textContent =
                        'Maximum decrease: ' +
                        currentAvailable.toFixed(2);

                } else if (
                    this.value === 'INCREASE'
                ) {

                    quantityInput.removeAttribute(
                        'max'
                    );

                    quantityHelp.textContent =
                        'Increase is not limited by current stock.';

                } else {

                    quantityInput.removeAttribute(
                        'max'
                    );

                    quantityHelp.textContent = '';

                }

                updatePreview();

            }
        );


        quantityInput.addEventListener(
            'input',
            updatePreview
        );


        document
            .getElementById('reason')
            .addEventListener(
                'change',
                function() {

                    const notes =
                        document.getElementById('notes');

                    if (this.value === 'OTHER') {

                        notes.required = true;

                        notes.placeholder =
                            'Please explain the reason...';

                    } else {

                        notes.required = false;

                        notes.placeholder =
                            'Additional explanation...';

                    }

                }
            );


        document
            .getElementById('adjustmentForm')
            .addEventListener(
                'submit',
                function(e) {

                    if (
                        adjustmentType.value === 'DECREASE' &&
                        (
                            parseFloat(
                                quantityInput.value || 0
                            ) > currentAvailable
                        )
                    ) {

                        e.preventDefault();

                        alert(
                            'The adjustment quantity cannot exceed the available stock.'
                        );

                        return;
                    }

                }
            );

        if (
            inventorySelect.value &&
            locationSelect.value
        ) {
            loadStock();
        }

    });
</script>