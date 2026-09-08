<div class="container-fluid mt-4">

    <!-- ==========================================================
     PAGE HEADER
    =========================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="mb-1">

                <i class="fas fa-dolly text-primary"></i>

                Fulfill Material Requisition

            </h2>

            <p class="text-muted mb-0">

                Issue materials from inventory to fulfill the approved requisition.

            </p>

        </div>
     
    </div>

    <!-- ==========================================================
     REQUISITION INFORMATION
    =========================================================== -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-light">

            <strong>

                <i class="fas fa-clipboard-list"></i>

                Requisition Information

            </strong>

        </div>


        <div class="card-body">

            <div class="row">

                <!-- Requisition Number -->

                <div class="col-md-3 mb-3">

                    <label class="form-label text-muted">

                        Requisition No.

                    </label>

                    <div class="fw-bold">

                        <?= htmlspecialchars(
                            $data['requisition']->req_number
                        ) ?>

                    </div>

                </div>


                <!-- Project -->

                <div class="col-md-5 mb-3">

                    <label class="form-label text-muted">

                        Project

                    </label>

                    <div class="fw-bold">

                        <?= htmlspecialchars(
                            $data['requisition']->project_name
                        ) ?>

                    </div>

                </div>


                <!-- Status -->

                <div class="col-md-2 mb-3">

                    <label class="form-label text-muted">

                        Status

                    </label>

                    <div>

                        <span class="badge bg-success">

                            <?= htmlspecialchars(
                                $data['requisition']->status
                            ) ?>

                        </span>

                    </div>

                </div>


                <!-- Priority -->

                <div class="col-md-2 mb-3">

                    <label class="form-label text-muted">

                        Priority

                    </label>

                    <div class="fw-bold">

                        <?= htmlspecialchars(
                            $data['requisition']->priority
                        ) ?>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ==========================================================
     FULFILLMENT FORM
    =========================================================== -->

    <form method="POST"
        action="<?= URLROOT ?>/ResourceRequisitionFulfillments/store"
        id="fulfillmentForm">


        <!-- REQUISITION ID -->

        <input type="hidden"
            name="requisition_id"
            value="<?= $data['requisition']->id ?>">


        <!-- ======================================================
         FULFILLMENT HEADER
        =========================================================== -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <strong>

                    <i class="fas fa-file-alt"></i>

                    Fulfillment Details

                </strong>

            </div>


            <div class="card-body">

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Fulfillment Reference
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="Auto-generated after processing"
                        readonly>

                </div>


                <!-- FULFILLMENT DATE -->

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Fulfillment Date

                        <span class="text-danger">*</span>

                    </label>

                    <input type="date"
                        name="fulfillment_date"
                        class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        required>

                </div>


                <!-- MATERIAL ONLY -->

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Fulfillment Type

                    </label>

                    <input type="text"
                        class="form-control"
                        value="MATERIAL / INVENTORY"
                        readonly>

                </div>

            </div>


            <!-- REMARKS -->

            <div class="mb-0">

                <label class="form-label">

                    Remarks

                </label>

                <textarea name="remarks"
                    class="form-control"
                    rows="3"
                    placeholder="Enter fulfillment remarks or notes..."></textarea>

            </div>

        </div>

</div>


<!-- ======================================================
         MATERIAL ITEMS
        =========================================================== -->

<div class="card shadow-sm">

    <div class="card-header bg-success text-white">

        <strong>

            <i class="fas fa-boxes"></i>

            Material Items to Fulfill

        </strong>

    </div>


    <div class="card-body p-0">

        <?php if (!empty($data['items'])): ?>


            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0 align-middle">

                    <thead class="table-light">

                        <tr>

                            <th style="min-width: 250px;">

                                Material

                            </th>


                            <th class="text-center">

                                UOM

                            </th>


                            <th class="text-end">

                                Requested

                            </th>


                            <th class="text-end">

                                Previously Fulfilled

                            </th>


                            <th class="text-end">

                                Remaining

                            </th>


                            <th style="min-width: 220px;">

                                Issue From Location

                            </th>


                            <th class="text-end">

                                Available Stock

                            </th>


                            <th style="min-width: 150px;">

                                Quantity to Fulfill

                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach ($data['items'] as $item): ?>


                            <?php

                            /*
                                    |--------------------------------------------------
                                    | NORMALIZE VALUES
                                    |--------------------------------------------------
                                    */

                            $requested_qty =
                                (float) (
                                    $item->quantity ?? 0
                                );


                            $fulfilled_qty =
                                (float) (
                                    $item->fulfilled_qty ?? 0
                                );


                            $remaining_qty =
                                (float) (
                                    $item->remaining_qty ?? 0
                                );


                            ?>


                            <tr
                                data-item-id="<?= $item->id ?>"
                                data-inventory-id="<?= $item->resource_id ?>">

                                <!-- ======================================
                                         MATERIAL
                                        ======================================= -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $item->inventory_name
                                                ?? $item->description
                                        ) ?>

                                    </strong>

                                    <br>

                                    <small class="text-muted">

                                        SKU:

                                        <?= htmlspecialchars(
                                            $item->sku ?? '-'
                                        ) ?>

                                    </small>

                                </td>


                                <!-- ======================================
                                         UOM
                                        ======================================= -->

                                <td>

                                    <?= htmlspecialchars(
                                        $item->uom
                                            ?? $item->base_unit
                                            ?? '-'
                                    ) ?>

                                </td>

                                <!-- ====================
                                         REQUESTED
                                    ==================== -->

                                <td class="text-end">

                                    <?= number_format((float) $item->quantity, 2) ?>
                                </td>

                                <!-- ======================================
                                         PREVIOUSLY FULFILLED
                                        ======================================= -->

                                <td class="text-end">

                                   <?= number_format((float) $item->fulfilled_quantity, 2) ?>

                                </td>


                                <!-- ======================================
                                         REMAINING
                                        ======================================= -->

                                <td class="text-end">

                                    <strong class="text-primary">

                                       <?= number_format((float) $item->remaining_quantity, 2) ?>

                                    </strong>

                                </td>


                                <!-- ======================================
                                         LOCATION
                                        ======================================= -->

                                <td>

                                    <select
                                        class="form-select location-select"
                                        name="items[<?= $item->id ?>][location_id]"
                                        data-row="<?= $item->id ?>">

                                        <option value="">

                                            -- Select Location --

                                        </option>

                                        <?php foreach ($item->locations as $location): ?>

                                            <option
                                                value="<?= $location->location_id ?>"
                                                data-available="<?= $location->available_qty ?>">

                                                <?= htmlspecialchars(
                                                    $location->location_name
                                                ) ?>

                                                <?php if (!empty($location->location_code)): ?>

                                                    (<?= htmlspecialchars(
                                                            $location->location_code
                                                        ) ?>)

                                                <?php endif; ?>

                                                — Available:
                                                <?= number_format(
                                                    $location->available_qty,
                                                    2
                                                ) ?>

                                            </option>

                                        <?php endforeach; ?>
                                    </select>

                                </td>

                                <!-- ======================================
                                         AVAILABLE STOCK
                                        ======================================= -->

                                <td class="text-center">

                                    <strong
                                        class="available-stock"
                                        data-row="<?= $item->id ?>">
                                        0.00
                                    </strong>

                                </td>


                                <!-- ======================================
                                         FULFILL QUANTITY
                                        ======================================= -->

                                <td>
                                    <input
                                        type="hidden"
                                        name="items[<?= $item->id ?>][inventory_id]"
                                        value="<?= $item->resource_id ?>">
                                    <input
                                        type="number"
                                        class="form-control text-end fulfill-quantity"
                                        name="items[<?= $item->id ?>][fulfilled_quantity]"
                                        data-item-id="<?= $item->id ?>"
                                        step="0.01"
                                        min="0"
                                    max="<?= (float) $item->remaining_quantity ?>"
                                        placeholder="0.00">

                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="alert alert-info m-3 mb-0">

                <i class="fas fa-info-circle"></i>

                There are no material items remaining to fulfill.

            </div>


        <?php endif; ?>


    </div>


    <!-- ==================================================
             FORM FOOTER
            =================================================== -->

    <div class="card-footer">

        <div class="d-flex justify-content-between align-items-center">

            <a href="<?= URLROOT ?>/ResourceRequisitionFulfillments/index/<?= $data['requisition']->id ?>"
                class="btn btn-secondary">

                <i class="fas fa-times"></i>

                Cancel

            </a>


            <button type="submit"
                class="btn btn-success"
                id="submitFulfillment">

                <i class="fas fa-check-circle"></i>

                Process Material Fulfillment

            </button>

        </div>

    </div>

</div>


</form>


</div>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {


            /*
            |--------------------------------------------------------------------------
            | URL ROOT
            |--------------------------------------------------------------------------
            */

            const URLROOT =
                '<?= URLROOT ?>';


            /*
            |--------------------------------------------------------------------------
            | GET STOCK AVAILABILITY
            |--------------------------------------------------------------------------
            |
            | This requires an endpoint:
            |
            | ResourceRequisitionFulfillments/getStockAvailability
            |
            | POST:
            |
            | inventory_id
            | location_id
            |
            */

            document.querySelectorAll(
                '.location-select'
            ).forEach(
                function(locationSelect) {


                    locationSelect.addEventListener(
                        'change',
                        function() {


                            const row =
                                this.closest('tr');


                            const itemId =
                                this.dataset.itemId;


                            const locationId =
                                this.value;


                            const stockDisplay =
                                row.querySelector(
                                    '.available-stock'
                                );


                            /*
                            |----------------------------------------------------------
                            | NO LOCATION
                            |----------------------------------------------------------
                            */

                            if (!locationId) {

                                stockDisplay.innerHTML =
                                    'Select location';


                                stockDisplay.className =
                                    'available-stock text-muted';


                                return;

                            }


                            /*
                            |----------------------------------------------------------
                            | GET INVENTORY ID
                            |
                            | The fulfillment model must provide inventory_id
                            | through getFulfillableItems().
                            |----------------------------------------------------------
                            */

                            const inventoryId =
                                row.dataset.inventoryId;


                            if (!inventoryId) {

                                stockDisplay.innerHTML =
                                    'Invalid material';


                                stockDisplay.className =
                                    'available-stock text-danger';


                                return;

                            }


                            /*
                            |----------------------------------------------------------
                            | LOADING
                            |----------------------------------------------------------
                            */

                            stockDisplay.innerHTML =
                                '<i class="fas fa-spinner fa-spin"></i>';


                        }

                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDATE QUANTITY WHILE USER TYPES
            |--------------------------------------------------------------------------
            */

            document.querySelectorAll(
                '.fulfill-quantity'
            ).forEach(
                function(input) {


                    input.addEventListener(
                        'input',
                        function() {


                            const row =
                                this.closest('tr');


                            const requestedQty =
                                parseFloat(
                                    this.max
                                ) || 0;


                            const availableQty =
                                parseFloat(
                                    row.dataset.availableQty
                                );


                            const enteredQty =
                                parseFloat(
                                    this.value
                                ) || 0;


                            /*
                            |----------------------------------------------------------
                            | ABOVE REMAINING QUANTITY
                            |----------------------------------------------------------
                            */

                            if (
                                enteredQty >
                                requestedQty
                            ) {

                                this.setCustomValidity(

                                    'Quantity cannot exceed the remaining requisition quantity.'

                                );


                            }

                            /*
                            |----------------------------------------------------------
                            | ABOVE AVAILABLE STOCK
                            |----------------------------------------------------------
                            */
                            else if (

                                !isNaN(
                                    availableQty
                                )

                                &&

                                enteredQty >
                                availableQty

                            ) {

                                this.setCustomValidity(

                                    'Quantity exceeds available stock at the selected location.'

                                );


                            } else {

                                this.setCustomValidity(
                                    ''
                                );

                            }

                        }

                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | PREPARE POST DATA
            |--------------------------------------------------------------------------
            |
            | The table inputs do not initially have name attributes.
            |
            | We only submit rows where quantity > 0.
            |
            */

            document.getElementById(
                'fulfillmentForm'
            ).addEventListener(
                'submit',
                function(event) {


                    let validItems =
                        0;


                    const existingInputs =
                        document.querySelectorAll(
                            '.dynamic-fulfillment-input'
                        );


                    existingInputs.forEach(
                        input => input.remove()
                    );


                    document.querySelectorAll(
                        'tbody tr'
                    ).forEach(
                        function(row) {


                            const itemId =
                                row.dataset.itemId;


                            const quantityInput =
                                row.querySelector(
                                    '.fulfill-quantity'
                                );


                            const locationSelect =
                                row.querySelector(
                                    '.location-select'
                                );


                            if (

                                !quantityInput

                                ||

                                !locationSelect

                            ) {

                                return;

                            }


                            const quantity =
                                parseFloat(
                                    quantityInput.value
                                ) || 0;


                            /*
                            |----------------------------------------------------------
                            | SKIP ZERO
                            |----------------------------------------------------------
                            */

                            if (
                                quantity <= 0
                            ) {

                                return;

                            }


                            /*
                            |----------------------------------------------------------
                            | LOCATION REQUIRED
                            |----------------------------------------------------------
                            */

                            if (
                                !locationSelect.value
                            ) {

                                event.preventDefault();


                                alert(

                                    'Please select an inventory location for every item being fulfilled.'

                                );


                                locationSelect.focus();


                                return;

                            }


                            /*
                            |----------------------------------------------------------
                            | CREATE QUANTITY INPUT
                            |----------------------------------------------------------
                            */

                            const quantityHidden =
                                document.createElement(
                                    'input'
                                );


                            quantityHidden.type =
                                'hidden';


                            quantityHidden.name =
                                'items[' +

                                itemId +

                                '][quantity]';


                            quantityHidden.value =
                                quantity;


                            quantityHidden.classList.add(
                                'dynamic-fulfillment-input'
                            );


                            this.appendChild(
                                quantityHidden
                            );


                            /*
                            |----------------------------------------------------------
                            | CREATE LOCATION INPUT
                            |----------------------------------------------------------
                            */

                            const locationHidden =
                                document.createElement(
                                    'input'
                                );


                            locationHidden.type =
                                'hidden';


                            locationHidden.name =
                                'items[' +

                                itemId +

                                '][location_id]';


                            locationHidden.value =
                                locationSelect.value;


                            locationHidden.classList.add(
                                'dynamic-fulfillment-input'
                            );


                            this.appendChild(
                                locationHidden
                            );


                            validItems++;

                        }.bind(this)
                    );


                    /*
                    |--------------------------------------------------------------
                    | NOTHING SELECTED
                    |--------------------------------------------------------------
                    */

                    if (
                        validItems === 0
                    ) {

                        event.preventDefault();


                        alert(

                            'Please enter a fulfillment quantity for at least one material item.'

                        );


                        return;

                    }


                    /*
                    |--------------------------------------------------------------
                    | FINAL BROWSER VALIDATION
                    |--------------------------------------------------------------
                    */

                    if (
                        !this.checkValidity()
                    ) {

                        event.preventDefault();

                        this.reportValidity();

                    }

                }
            );

        }
    );
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.location-select').forEach(function(select) {

            select.addEventListener('change', function() {

                const rowId =
                    this.dataset.row;

                const stockDisplay =
                    document.querySelector(
                        '.available-stock[data-row="' + rowId + '"]'
                    );

                const selectedOption =
                    this.options[
                        this.selectedIndex
                    ];

                const available =
                    parseFloat(
                        selectedOption.dataset.available
                    ) || 0;

                stockDisplay.textContent =
                    available.toFixed(2);

            });


            /*
            |--------------------------------------------------------------------------
            | SET INITIAL STOCK
            |--------------------------------------------------------------------------
            */

            if (select.value) {

                const selectedOption =
                    select.options[
                        select.selectedIndex
                    ];

                const available =
                    parseFloat(
                        selectedOption.dataset.available
                    ) || 0;

                const rowId =
                    select.dataset.row;

                const stockDisplay =
                    document.querySelector(
                        '.available-stock[data-row="' + rowId + '"]'
                    );

                if (stockDisplay) {

                    stockDisplay.textContent =
                        available.toFixed(2);
                }
            }

        });

    });
</script>