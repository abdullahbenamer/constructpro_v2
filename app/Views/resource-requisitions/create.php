<h2 class="mb-4">
    <i class="fas fa-clipboard-list text-primary"></i>
    New Resource Requisition
</h2>

<div class="card shadow-sm">

    <div class="card-header bg-primary text-white">

        <strong>Create Resource Requisition</strong>

    </div>

    <div class="card-body">

        <form method="POST">

            <div class="row">

                <!-- Requisition Number -->
                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Requisition No.
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($next_number) ?>"
                        readonly>

                </div>

                <!-- Request Date -->
                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Request Date
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="request_date"
                        class="form-control"
                        value="<?= date('Y-m-d') ?>"
                        required>

                </div>

                <!-- Required Date -->
                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Required Date
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="required_date"
                        class="form-control"
                        min="<?= date('Y-m-d') ?>"
                        required>

                </div>

            </div>

            <div class="row">

                <!-- Project -->
                <div class="col-md-8 mb-3">

                    <label class="form-label">
                        Project
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="project_id"
                        class="form-select"
                        required>

                        <option value="">
                            -- Select Project --
                        </option>

                        <?php foreach ($projects as $project): ?>

                            <option value="<?= $project->id ?>">

                                <?= htmlspecialchars($project->title) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Priority -->
             <!-- Priority -->
<div class="col-md-4 mb-3">

    <label class="form-label">
        Priority
    </label>

    <select
        name="priority"
        class="form-select">

        <option value="LOW">LOW</option>

        <option value="MEDIUM" selected>
            MEDIUM
        </option>

        <option value="HIGH">
            HIGH
        </option>

    </select>

</div>

                <div class="row">

    <!-- DELIVERY METHOD -->
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Delivery Method
            <span class="text-danger">*</span>
        </label>

        <select
            name="delivery_method"
            id="delivery_method"
            class="form-select"
            required>

            <option value="WAREHOUSE" selected>
                Warehouse
            </option>

            <option value="DIRECT_TO_PROJECT_SITE">
                Direct to Project Site
            </option>

        </select>

    </div>

    <!-- TARGET WAREHOUSE -->
    <div class="col-md-6 mb-3" id="targetWarehouseGroup">

        <label class="form-label">
            Target Warehouse
            <span class="text-danger">*</span>
        </label>

        <select
            name="target_warehouse_id"
            id="target_warehouse_id"
            class="form-select">

            <option value="">
                -- Select Warehouse --
            </option>

            <?php foreach ($locations as $location): ?>

                <option value="<?= $location->id ?>">

                    <?= htmlspecialchars($location->code) ?>
                    -
                    <?= htmlspecialchars($location->name) ?>

                </option>

            <?php endforeach; ?>

        </select>

    </div>

</div>

<div
    id="projectSiteInfo"
    class="alert alert-info d-none">

    <i class="fas fa-truck"></i>

    This requisition will be delivered directly to the
    selected project's site.

</div>

            </div>

            <!-- Remarks -->

            <div class="mb-3">

                <label class="form-label">

                    Remarks

                </label>

                <textarea
                    name="remarks"
                    rows="4"
                    class="form-control"
                    placeholder="Enter purpose, work description or special instructions..."></textarea>

            </div>

            <hr>

            <div class="d-flex justify-content-between">

                <a
                    href="<?= URLROOT ?>/resourcerequisitions"
                    class="btn btn-secondary">

                    <i class="fas fa-arrow-left"></i>

                    Back

                </a>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fas fa-save"></i>

                    Save Draft

                </button>

            </div>

        </form>

    </div>

</div>
<script>

document.addEventListener('DOMContentLoaded', function () {

    const deliveryMethod =
        document.getElementById('delivery_method');

    const warehouseGroup =
        document.getElementById('targetWarehouseGroup');

    const warehouse =
        document.getElementById('target_warehouse_id');

    const projectSiteInfo =
        document.getElementById('projectSiteInfo');

    function updateDeliveryFields() {

        if (deliveryMethod.value === 'WAREHOUSE') {

            warehouseGroup.classList.remove('d-none');

            warehouse.required = true;

            projectSiteInfo.classList.add('d-none');

        } else {

            warehouseGroup.classList.add('d-none');

            warehouse.required = false;

            warehouse.value = '';

            projectSiteInfo.classList.remove('d-none');
        }
    }

    deliveryMethod.addEventListener(
        'change',
        updateDeliveryFields
    );

    updateDeliveryFields();

});

</script>