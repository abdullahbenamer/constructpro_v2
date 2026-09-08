<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="mb-1">

                <i class="fas fa-shopping-cart"></i>
                Create Purchase Order

            </h2>

            <div class="text-muted">

                From Resource Requisition:

                <strong>
                    <?= htmlspecialchars(
                        $requisition->req_number
                            ?? $requisition->requisition_no
                            ?? $requisition->id
                    ) ?>
                </strong>

            </div>

        </div>

    </div>

    <!-- REQUISITION INFORMATION -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row">

                <!-- PROJECT -->
                <div class="col-md-3 mb-3">

                    <strong>
                        Project
                    </strong>

                    <div>

                        <?= htmlspecialchars(
                            $requisition->project_name ?? '-'
                        ) ?>

                    </div>

                </div>


                <!-- RR -->
                <div class="col-md-3 mb-3">

                    <strong>
                        Source RR
                    </strong>

                    <div>

                        <?= htmlspecialchars(
                            $requisition->req_number
                                ?? $requisition->requisition_no
                                ?? $requisition->id
                        ) ?>

                    </div>

                </div>


                <!-- REQUIRED DATE -->
                <div class="col-md-2 mb-3">

                    <strong>
                        Required Date
                    </strong>

                    <div>

                        <?= htmlspecialchars(
                            $requisition->required_date ?? '-'
                        ) ?>

                    </div>

                </div>


                <!-- PRIORITY -->
                <div class="col-md-2 mb-3">

                    <strong>
                        Priority
                    </strong>

                    <div>

                        <?php if ($requisition->priority === 'HIGH'): ?>

                            <span class="badge bg-danger">
                                HIGH
                            </span>

                        <?php elseif ($requisition->priority === 'MEDIUM'): ?>

                            <span class="badge bg-warning text-dark">
                                MEDIUM
                            </span>

                        <?php elseif ($requisition->priority === 'LOW'): ?>

                            <span class="badge bg-secondary">
                                LOW
                            </span>

                        <?php else: ?>

                            <span class="text-muted">
                                -
                            </span>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- DELIVERY METHOD -->
                <div class="col-md-2 mb-3">

                    <strong>
                        Delivery
                    </strong>

                    <div>

                        <?php if (
                            $requisition->delivery_method === 'WAREHOUSE'
                        ): ?>

                            <span class="badge bg-primary">

                                <i class="fas fa-warehouse"></i>

                                Warehouse

                            </span>

                        <?php elseif (
                            $requisition->delivery_method === 'DIRECT_TO_PROJECT_SITE'
                        ): ?>

                            <span class="badge bg-info text-dark">

                                <i class="fas fa-truck"></i>

                                Direct to Site

                            </span>

                        <?php else: ?>

                            <span class="text-muted">
                                -
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- DESTINATION -->

            <div class="row mt-2">

                <div class="col-md-6">

                    <strong>
                        Target Warehouse
                    </strong>

                    <div>

                        <?php if (
                            $requisition->delivery_method === 'WAREHOUSE'
                            && !empty($requisition->target_warehouse_name)
                        ): ?>

                            <i class="fas fa-warehouse text-primary"></i>

                            <?= htmlspecialchars(
                                $requisition->target_warehouse_name
                            ) ?>

                        <?php elseif (
                            $requisition->delivery_method === 'DIRECT_TO_PROJECT_SITE'
                        ): ?>

                            <span class="text-info">

                                <i class="fas fa-map-marker-alt"></i>

                                Project Site

                            </span>

                        <?php else: ?>

                            <span class="text-muted">
                                -
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <form method="POST">


        <!-- PO HEADER -->

        <div class="card shadow-sm mb-4">

            <div class="card-header">

                <strong>
                    Purchase Order Details
                </strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Supplier *
                        </label>

                        <select
                            name="supplier_id"
                            class="form-select"
                            required>

                            <option value="">
                                Select Supplier
                            </option>

                            <?php foreach ($suppliers as $supplier): ?>

                                <option value="<?= $supplier->id ?>">

                                    <?= htmlspecialchars(
                                        $supplier->company_name
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Order Date
                        </label>

                        <input
                            type="date"
                            name="order_date"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>"
                            required>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Expected Date
                        </label>

                        <input
                            type="date"
                            name="expected_date"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                        $requisition->required_date ?? ''
                                    ) ?>">

                    </div>

                </div>

            </div>

        </div>


        <!-- MATERIALS -->

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">

                <strong>
                    Materials to Purchase
                </strong>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Material
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    UOM
                                </th>

                                <th class="text-end">
                                    RR Remaining
                                </th>

                                <th style="width:180px;">
                                    Quantity to Purchase
                                </th>

                                <th style="width:180px;">
                                    Unit Cost
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($items as $item): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $item->inventory_name
                                            ) ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item->sku ?? '-'
                                        ) ?>

                                    </td>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item->inventory_uom
                                                ?? $item->uom
                                                ?? '-'
                                        ) ?>

                                    </td>

                                    <td class="text-end">

                                        <strong class="text-primary">

                                            <?= number_format(
                                                (float)$item->remaining_quantity,
                                                2
                                            ) ?>

                                        </strong>

                                    </td>

                                    <td>

                                        <input
                                            type="number"
                                            name="items[<?= $item->id ?>][quantity]"
                                            class="form-control text-end"
                                            min="0"
                                            max="<?= (float)$item->remaining_quantity ?>"
                                            step="0.01"
                                            value="<?= (float)$item->remaining_quantity ?>">

                                    </td>

                                 <td>

    <input
        type="number"
        name="items[<?= $item->id ?>][unit_cost]"
        class="form-control text-end"
        min="0"
        step="0.01"
        value=""
        placeholder="Enter price"
        required>

    <?php if (
        isset($item->current_cost)
        && (float)$item->current_cost > 0
    ): ?>

        <small class="text-muted d-block mt-1">
            Reference only — Current Cost:
            <strong>
                <?= number_format(
                    (float)$item->current_cost,
                    2
                ) ?>
            </strong>
        </small>

    <?php endif; ?>

</td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <div class="card-footer d-flex justify-content-between">

                <a
                    href="<?= URLROOT ?>/ResourceRequisitions/details/<?= $requisition->id ?>"
                    class="btn btn-secondary">

                    <i class="fas fa-times"></i>
                    Cancel

                </a>


                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="fas fa-file-invoice"></i>
                    Create Purchase Order

                </button>

            </div>

        </div>

    </form>

</div>