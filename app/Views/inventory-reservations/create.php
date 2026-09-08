<h3>
    Materials Reservation
</h3>
<?php if (isset($_SESSION['user_name'])) : ?>
    <span class="nav-link text-dark">
        Reserved by: <i class="fas fa-user-circle"></i> <?= $_SESSION['full_name'] ?>
    </span>
<?php endif; ?>
<br>
<form method="POST">

    <div class="mb-3">

        <label>Inventory Item</label>

        <select name="inventory_id"
            id="inventory_id"
            class="form-select"
            required>
            <option value="">
                Select Inventory Item
            </option>

            <?php foreach ($inventory as $item): ?>

                <option value="<?= $item->id ?>">

                    <?= htmlspecialchars($item->name) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="mb-3">

        <label>
            From Location
            <span class="text-danger">*</span>
        </label>

        <select
            name="location_id"
            id="location_id"
            class="form-select"
            required>

            <option value="">
                Select inventory item first
            </option>

        </select>

    </div>
    <!-- Show available quantity can be Reserved -->

    <div
        id="stockInfo"
        class="alert alert-info d-none" style="max-width: 400px;">

        <div>
            Physical Stock:
            <strong id="physicalQty">0.00</strong>
        </div>

        <div>
            Already Reserved:
            <strong id="reservedQty">0.00</strong>
        </div>

        <hr>

        <div class="fw-bold">
            Available to Reserve:
            <strong id="availableQty">0.00</strong>
        </div>

    </div>
 
    <!-- ======================= -->

    <div class="mb-3" style="max-width: 400px;">

        <label>Quantity to Reserve</label>

        <input
            type="number"
            id="quantity"
            step="0.01"
            min="0.01"
            name="quantity"
            class="form-control"
            required>
    </div>
    <div class="mb-3">

        <label>
            Project
            <span class="text-danger">*</span>
        </label>

        <select name="project_id"
            class="form-select"
            required>

            <option value="">
                Select Project
            </option>

            <?php foreach ($projects as $p): ?>

                <option value="<?= $p->id ?>">

                    <?= htmlspecialchars($p->title) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="col-md-3 mb-3">

        <label class="form-label">

            Required By Date
            <span class="text-danger">*</span>
        </label>
        <input type="date" name="required_by_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="mb-3">

        <label>Reference</label>

        <input type="text"
            name="reference"
            class="form-control">

    </div>

    <div class="mb-3">

        <label>Notes</label>

        <textarea name="notes"
            class="form-control"></textarea>

    </div>

    <button class="btn btn-primary">

        Reserve

    </button>

</form>

<script>
    const inventorySelect =
        document.getElementById('inventory_id');

    const locationSelect =
        document.getElementById('location_id');


    inventorySelect.addEventListener(
        'change',
        function() {

            const inventoryId = this.value;

            // Reset location dropdown
            locationSelect.innerHTML =
                '<option value="">Loading locations...</option>';

            if (!inventoryId) {

                locationSelect.innerHTML =
                    '<option value="">Select inventory item first</option>';

                return;
            }


            fetch(
                    '<?= URLROOT ?>/inventoryreservations/getItemLocations', {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },

                        body: 'inventory_id=' +
                            encodeURIComponent(inventoryId)
                    }
                )
                .then(response => response.json())
                .then(locations => {

                    locationSelect.innerHTML =
                        '<option value="">Select Location</option>';

                    locations.forEach(location => {

                        const option =
                            document.createElement('option');

                        option.value =
                            location.location_id;

                        option.textContent =
                            location.code +
                            ' - ' +
                            location.name;

                        locationSelect.appendChild(option);

                    });

                })
                .catch(error => {

                    console.error(
                        'Error loading locations:',
                        error
                    );

                    locationSelect.innerHTML =
                        '<option value="">Unable to load locations</option>';

                });

        }
    );
</script>

<script>
    const quantityInput =
        document.getElementById('quantity');

    const stockInfo =
        document.getElementById('stockInfo');

    const availableQty =
        document.getElementById('availableQty');


    locationSelect.addEventListener(
        'change',
        function() {

            const inventoryId =
                inventorySelect.value;

            const locationId =
                this.value;

            // Hide previous stock information
            stockInfo.classList.add('d-none');

            // Remove previous max value
            quantityInput.removeAttribute('max');

            if (!inventoryId || !locationId) {
                return;
            }


            fetch(
                    '<?= URLROOT ?>/inventoryreservations/getLocationStock', {
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

                    const physicalQty =
                        parseFloat(data.physical_qty || 0);

                    const reservedQty =
                        parseFloat(data.reserved_qty || 0);

                    const availableQtyValue =
                        parseFloat(data.available_qty || 0);


                    // DISPLAY QUANTITIES

                    document.getElementById('physicalQty').textContent =
                        physicalQty.toFixed(2);

                    document.getElementById('reservedQty').textContent =
                        reservedQty.toFixed(2);

                    availableQty.textContent =
                        availableQtyValue.toFixed(2);

                    // LIMIT RESERVATION QUANTITY

                    quantityInput.max =
                        availableQtyValue;

                    stockInfo.classList.remove('d-none');

                })
                .catch(error => {

                    console.error(
                        'Error loading stock:',
                        error
                    );

                });

        }
    );
</script>