<div class="container-fluid mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-0">
                <i class="fas fa-plus-circle"></i>
                Add Resource Requisition Item
            </h4>

            <small class="text-muted">
                Add a new item to this requisition
            </small>
        </div>

        <a href="<?= URLROOT ?>/ResourceRequisitions/details/<?= $data['requisition_id']; ?>"
            class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Back to Requisition

        </a>

    </div>


    <div class="card shadow-sm">

        <div class="card-header">

            <strong>
                <i class="fas fa-box"></i>
                Item Details
            </strong>

        </div>


        <div class="card-body">

            <form
                action="<?= URLROOT ?>/ResourceRequisitionItems/store"
                method="POST">

                <!-- REQUISITION -->
                <input
                    type="hidden"
                    name="requisition_id"
                    value="<?= $data['requisition_id']; ?>">

                <input
                    type="hidden"
                    name="resource_source"
                    id="resource_source">

                <input
                    type="hidden"
                    name="inventory_id"
                    id="inventory_id">

                <input
                    type="hidden"
                    name="non_inventory_resource"
                    id="non_inventory_resource">

                <div class="row">

                    <!-- RESOURCE TYPE -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Select Resource Type
                        </label>

                        <select
                            id="resourceType"
                            class="form-select">

                            <option value="INVENTORY">
                                MATERIAL
                            </option>

                            <option value="RESOURCE">
                                NON MATERIAL
                            </option>

                        </select>


                        <!-- MATERIAL ITEMS -->
                        <div class="mb-3" id="inventoryBlock">

                            <label class="form-label">
                                Material Item
                            </label>

                            <select
                                id="inventorySelect"
                                class="form-select">

                                <option value="">
                                    -- Select Material --
                                </option>

                                <?php foreach ($data['inventory'] as $item): ?>

                                    <option
                                        value="<?= (int)$item->id ?>"
                                        data-source="INVENTORY"
                                        data-unit="<?= htmlspecialchars($item->base_unit) ?>"
                                        data-description="<?= htmlspecialchars($item->name) ?>">

                                        <?= htmlspecialchars($item->sku) ?>
                                        -
                                        <?= htmlspecialchars($item->name) ?>

                                        (Available:
                                        <?= number_format((float)$item->available_qty, 2) ?>
                                        )

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <small class="text-danger">
                                Search by SKU or material name.
                            </small>

                        </div>


                        <!-- NON MATERIAL ITEMS -->
                        <div class="mb-3" id="resourceBlock">

                            <label class="form-label">
                                Resource
                            </label>

                            <select
                                id="resourceSelect"
                                class="form-select">

                                <option value="">
                                    -- Select Resource --
                                </option>

                                <?php foreach ($data['resources'] as $resource): ?>

                                    <option
                                        value="<?= (int)$resource->id ?>"
                                        data-source="RESOURCE"
                                        data-unit="<?= htmlspecialchars($resource->unit_name) ?>"
                                        data-description="<?= htmlspecialchars($resource->resource_name) ?>">

                                        <?= htmlspecialchars($resource->resource_code) ?>
                                        -
                                        <?= htmlspecialchars($resource->resource_name) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <small class="text-muted">
                                Search by resource code or name.
                            </small>

                        </div>

                    </div>


                    <!-- DESCRIPTION -->
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <input
                            type="text"
                            name="description"
                            id="description"
                            class="form-control"
                            readonly>

                    </div>

                </div>


                <div class="row">

                    <!-- QUANTITY -->
                    <div class="col-md-2 mb-3">

                        <label class="form-label">
                            Requested Quantity
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            name="quantity"
                            step="0.01"
                            min="0.01"
                            value="1"
                            required>

                    </div>


                    <!-- UOM -->
                    <div class="col-md-2 mb-3">

                        <label class="form-label">
                            UOM
                        </label>

                        <input
                            type="text"
                            name="uom"
                            id="uom"
                            class="form-control"
                            readonly>

                    </div>

                </div>


                <!-- REMARKS -->
                <div class="mb-3">

                    <label class="form-label">
                        Remarks
                    </label>

                    <textarea
                        name="remarks"
                        class="form-control"
                        rows="4"></textarea>

                </div>


                <div class="text-end">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-save"></i>
                        Save Item

                    </button>


                    <a
                        href="<?= URLROOT ?>/ResourceRequisitions/details/<?= $data['requisition_id']; ?>"
                        class="btn btn-secondary">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<style>
    /* Select2 search box placeholder */
    .select2-container--open .select2-search__field::placeholder {
        color: #dc3545 !important;
        opacity: 1 !important;
    }

    .select2-container--open .select2-search__field::-webkit-input-placeholder {
        color: #dc3545 !important;
        opacity: 1 !important;
    }

    .select2-container--open .select2-search__field::-moz-placeholder {
        color: #dc3545 !important;
        opacity: 1 !important;
    }

    .select2-container--open .select2-search__field:-ms-input-placeholder {
        color: #dc3545 !important;
        opacity: 1 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const type = document.getElementById('resourceType');

        const inventoryBlock =
            document.getElementById('inventoryBlock');

        const resourceBlock =
            document.getElementById('resourceBlock');

        const inventorySelect =
            document.getElementById('inventorySelect');

        const resourceSelect =
            document.getElementById('resourceSelect');

        const resourceSource =
            document.getElementById('resource_source');

        const inventoryId =
            document.getElementById('inventory_id');

        const nonInventoryResource =
            document.getElementById('non_inventory_resource');

        const description =
            document.getElementById('description');

        const uom =
            document.getElementById('uom');


        /*
        |--------------------------------------------------------------------------
        | SAFETY CHECK
        |--------------------------------------------------------------------------
        */

        if (!type) {
            console.error('resourceType not found.');
            return;
        }

        if (!inventorySelect) {
            console.error('inventorySelect not found.');
            return;
        }

        if (!resourceSelect) {
            console.error('resourceSelect not found.');
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | INITIALIZE SELECT2 IF AVAILABLE
        |--------------------------------------------------------------------------
        */

        if (
            typeof window.jQuery !== 'undefined' &&
            typeof jQuery.fn.select2 === 'function'
        ) {

            $('#inventorySelect').select2({

                width: '100%',

                placeholder: '-- Search by SKU or material name --',

                allowClear: true,

                minimumResultsForSearch: 0

            });


            $('#resourceSelect').select2({

                width: '100%',

                placeholder: '-- Search Resource --',

                allowClear: true,

                minimumResultsForSearch: 0

            });


            /*
            | Search placeholder - MATERIAL
            */

            $('#inventorySelect').on(
                'select2:open',
                function() {

                    setTimeout(function() {

                        const field =
                            document.querySelector(
                                '.select2-container--open .select2-search__field'
                            );

                        if (field) {

                            field.placeholder =
                                'Search by SKU or material name...';

                            field.style.color = 'red';

                        }

                    }, 10);

                }
            );


            /*
            | Search placeholder - RESOURCE
            */

            $('#resourceSelect').on(
                'select2:open',
                function() {

                    setTimeout(function() {

                        const field =
                            document.querySelector(
                                '.select2-container--open .select2-search__field'
                            );

                        if (field) {

                            field.placeholder =
                                'Search Resource...';

                            field.style.color = 'red';

                        }

                    }, 10);

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CLEAR DETAILS
        |--------------------------------------------------------------------------
        */

        function clearDetails() {

            description.value = '';

            uom.value = '';

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE MATERIAL
        |--------------------------------------------------------------------------
        */

      function updateInventory() {

    const selectedId =
        inventorySelect.value;

    resourceSource.value = 'INVENTORY';

    inventoryId.value =
        selectedId || '';

    nonInventoryResource.value = '';

    if (!selectedId) {

        description.value = '';
        uom.value = '';

        return;
    }

    const option =
        inventorySelect.options[
            inventorySelect.selectedIndex
        ];

    if (!option) {
        return;
    }

    description.value =
        option.getAttribute('data-description') || '';

    uom.value =
        option.getAttribute('data-unit') || '';
}


        /*
        |--------------------------------------------------------------------------
        | UPDATE NON-MATERIAL RESOURCE
        |--------------------------------------------------------------------------
        */
function updateResource() {

    const selectedId =
        resourceSelect.value;

    resourceSource.value = 'RESOURCE';

    nonInventoryResource.value =
        selectedId || '';

    inventoryId.value = '';

    if (!selectedId) {

        description.value = '';
        uom.value = '';

        return;
    }

    const option =
        resourceSelect.options[
            resourceSelect.selectedIndex
        ];

    if (!option) {
        return;
    }

    description.value =
        option.getAttribute('data-description') || '';

    uom.value =
        option.getAttribute('data-unit') || '';
}


        /*
        |--------------------------------------------------------------------------
        | CLEAR MATERIAL
        |--------------------------------------------------------------------------
        */

        function clearInventory() {

            inventorySelect.value = '';

            inventoryId.value = '';

        }


        /*
        |--------------------------------------------------------------------------
        | CLEAR NON-MATERIAL
        |--------------------------------------------------------------------------
        */

        function clearResource() {

            resourceSelect.value = '';

            nonInventoryResource.value = '';

        }


        /*
        |--------------------------------------------------------------------------
        | SHOW MATERIAL
        |--------------------------------------------------------------------------
        */

        function showMaterial() {

            /*
            | Use the native hidden property.
            | This does NOT depend on Bootstrap CSS.
            */

            inventoryBlock.hidden = false;

            resourceBlock.hidden = true;


            /*
            | Clear non-material
            */

            clearResource();


            /*
            | Set source
            */

            resourceSource.value =
                'INVENTORY';


            /*
            | Update material
            */

            updateInventory();

        }


        /*
        |--------------------------------------------------------------------------
        | SHOW NON-MATERIAL
        |--------------------------------------------------------------------------
        */

        function showResource() {

            /*
            | Use the native hidden property.
            */

            inventoryBlock.hidden = true;

            resourceBlock.hidden = false;


            /*
            | Clear material
            */

            clearInventory();


            /*
            | Set source
            */

            resourceSource.value =
                'RESOURCE';


            /*
            | Update resource
            */

            updateResource();

        }


        /*
        |--------------------------------------------------------------------------
        | RESOURCE TYPE CHANGE
        |--------------------------------------------------------------------------
        */

        type.addEventListener(
            'change',
            function() {

                if (
                    type.value === 'INVENTORY'
                ) {

                    showMaterial();

                } else {

                    showResource();

                }

            }
        );
    
/*
|--------------------------------------------------------------------------
| MATERIAL SELECT2 CHANGE
|--------------------------------------------------------------------------
*/

$('#inventorySelect').on('change', function () {

    if (type.value === 'INVENTORY') {

        updateInventory();

    }

});


/*
|--------------------------------------------------------------------------
| NON-MATERIAL SELECT2 CHANGE
|--------------------------------------------------------------------------
*/

$('#resourceSelect').on('change', function () {

    if (type.value === 'RESOURCE') {

        updateResource();

    }

});

        /*
        |--------------------------------------------------------------------------
        | FORM SUBMIT
        |--------------------------------------------------------------------------
        */

        const form =
            inventorySelect.closest('form');


        if (form) {

            form.addEventListener(
                'submit',
                function(event) {

                    /*
                    |--------------------------------------------------------------
                    | MATERIAL
                    |--------------------------------------------------------------
                    */

                    if (
                        type.value === 'INVENTORY'
                    ) {

                        const selectedId =
                            inventorySelect.value;


                        if (!selectedId) {

                            event.preventDefault();

                            alert(
                                'Please select a material item.'
                            );

                            if (
                                typeof window.jQuery !== 'undefined' &&
                                typeof jQuery.fn.select2 === 'function'
                            ) {

                                $('#inventorySelect')
                                    .select2('open');

                            } else {

                                inventorySelect.focus();

                            }

                            return;

                        }


                        /*
                        | Force correct POST values
                        */

                        resourceSource.value =
                            'INVENTORY';

                        inventoryId.value =
                            selectedId;

                        nonInventoryResource.value =
                            '';

                        updateInventory();

                    }


                    /*
                    |--------------------------------------------------------------
                    | NON-MATERIAL
                    |--------------------------------------------------------------
                    */
                    else {

                        const selectedId =
                            resourceSelect.value;


                        if (!selectedId) {

                            event.preventDefault();

                            alert(
                                'Please select a non-material resource.'
                            );

                            if (
                                typeof window.jQuery !== 'undefined' &&
                                typeof jQuery.fn.select2 === 'function'
                            ) {

                                $('#resourceSelect')
                                    .select2('open');

                            } else {

                                resourceSelect.focus();

                            }

                            return;

                        }


                        /*
                        | Force correct POST values
                        */

                        resourceSource.value =
                            'RESOURCE';

                        inventoryId.value =
                            '';

                        nonInventoryResource.value =
                            selectedId;

                        updateResource();

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | INITIAL STATE
        |--------------------------------------------------------------------------
        */

        if (
            type.value === 'INVENTORY'
        ) {

            showMaterial();

        } else {

            showResource();

        }

    });
</script>