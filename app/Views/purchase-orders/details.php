<h2>
    Purchase Order Details
</h2>

<div class="card mb-4">

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <p>
                    <strong>PO Number:</strong>
                    <?= htmlspecialchars($po->po_number) ?>
                </p>

                <p>
                    <strong>Supplier:</strong>
                    <?= htmlspecialchars($po->supplier_name) ?>
                </p>

                <p>
                    <strong>Status:</strong>

                    <?php if ($po->status === 'draft'): ?>

                        <span class="badge bg-secondary">
                            Draft
                        </span>

                    <?php elseif ($po->status === 'approved'): ?>

                        <span class="badge bg-success">
                            Approved
                        </span>

                    <?php elseif ($po->status === 'partially_received'): ?>

                        <span class="badge bg-warning text-dark">
                            Partially Received
                        </span>

                    <?php elseif ($po->status === 'received'): ?>

                        <span class="badge bg-primary">
                            Received
                        </span>

                    <?php else: ?>

                        <span class="badge bg-dark">
                            <?= htmlspecialchars($po->status) ?>
                        </span>

                    <?php endif; ?>
                </p>

            </div>

            <div class="col-md-6">

                <p>
                    <strong>Order Date:</strong>
                  <?= $po->order_date ?>
                </p>

                <p>
                    <strong>Expected Date:</strong>
                    <span class="bg-primary text-white px-2 py-1 rounded">
    <?= $po->expected_date ?>
</span>
                </p>

                <p>
                    <strong>Created:</strong>
                    <?= $po->created_at ?>
                </p>

            </div>

        </div>
        
         <?php if (!empty($po->notes)): ?>

            <hr>

            <p>
                <strong>Notes:</strong><br>
                <?= nl2br(htmlspecialchars($po->notes)) ?>
            </p>

        <?php endif; ?> 

    </div>

</div>

<!-- DELIVERY / SHIP TO -->

<div class="card shadow-sm mb-4">

    <div class="card-header bg-white">

        <strong>
            <i class="fas fa-truck"></i>
            Delivery / Ship To
        </strong>

    </div>

    <div class="card-body">

        <?php if ($po->delivery_method === 'DIRECT_TO_PROJECT_SITE'): ?>

            <div class="row">

                <!-- PROJECT -->
                <div class="col-md-6 mb-3">

                    <strong>
                        Project
                    </strong>

                    <div>
                        <?= htmlspecialchars(
                            $po->project_name ?? '-'
                        ) ?>
                    </div>

                </div>


                <!-- DELIVERY METHOD -->
                <div class="col-md-6 mb-3">

                    <strong>
                        Delivery Method
                    </strong>

                    <div>

                        <span class="badge bg-info text-dark">

                            <i class="fas fa-truck"></i>

                            Direct to Project Site

                        </span>

                    </div>

                </div>


                <!-- SITE LOCATION -->
                <div class="col-md-12 mb-3">

                    <strong>
                        Delivery Location
                    </strong>

                    <div class="border rounded p-3 bg-light">

                        <?= nl2br(
                            htmlspecialchars(
                                $po->project_site_location ?? '-'
                            )
                        ) ?>

                    </div>

                </div>


                <!-- PROJECT MANAGER -->
                <div class="col-md-6 mb-3">

                    <strong>
                        Site Contact
                    </strong>

                    <div>
                        <?= htmlspecialchars(
                            $po->project_manager_name ?? '-'
                        ) ?>
                    </div>

                </div>


                <!-- PM MOBILE -->
                <div class="col-md-6 mb-3">

                    <strong>
                        Contact Number
                    </strong>

                    <div>
                        <?= htmlspecialchars(
                            $po->project_manager_mobile ?? '-'
                        ) ?>
                    </div>

                </div>

            </div>


        <?php elseif ($po->delivery_method === 'WAREHOUSE'): ?>

            <div class="row">

                <!-- WAREHOUSE -->
                <div class="col-md-6 mb-3">

                    <strong>
                        Warehouse
                    </strong>

                    <div>

                        <?= htmlspecialchars(
                            $po->target_warehouse_code ?? ''
                        ) ?>

                        <?php if (!empty($po->target_warehouse_name)): ?>
                            -
                            <?= htmlspecialchars(
                                $po->target_warehouse_name
                            ) ?>
                        <?php endif; ?>

                    </div>

                </div>


                <!-- DELIVERY METHOD -->
                <div class="col-md-6 mb-3">

                    <strong>
                        Delivery Method
                    </strong>

                    <div>

                        <span class="badge bg-primary">

                            <i class="fas fa-warehouse"></i>

                            Warehouse

                        </span>

                    </div>

                </div>


                <!-- ADDRESS -->
                <div class="col-md-12 mb-3">

                    <strong>
                        Delivery Location
                    </strong>

                    <div class="border rounded p-3 bg-light">

                        <?= nl2br(
                            htmlspecialchars(
                                $po->target_warehouse_address ?? '-'
                            )
                        ) ?>

                    </div>

                </div>


                <!-- WAREHOUSE PHONE -->
                <div class="col-md-6 mb-3">

                    <strong>
                        Warehouse Contact Number
                    </strong>

                    <div>
                        <?= htmlspecialchars(
                            $po->target_warehouse_mobile ?? '-'
                        ) ?>
                    </div>

                </div>


                <!-- STOREKEEPER -->
                <div class="col-md-6 mb-3">

                    <strong>
                        Storekeeper
                    </strong>

                    <div>
                        <?= htmlspecialchars(
                            $po->storekeeper_name ?? '-'
                        ) ?>

                        <?php if (!empty($po->storekeeper_mobile)): ?>

                            <br>

                            <small class="text-muted">
                                <?= htmlspecialchars(
                                    $po->storekeeper_mobile
                                ) ?>
                            </small>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


        <?php else: ?>

            <div class="text-muted">
                Delivery information not specified.
            </div>

        <?php endif; ?>

    </div>

</div>

<div class="mb-3">

    <a href="<?= URLROOT ?>/purchaseorders"
        class="btn btn-secondary">

        Back

    </a>

    <a href="<?= URLROOT ?>/purchaseorders/itemsPage/<?= $po->id ?>"
        class="btn btn-primary">

        Manage Items

    </a>

<?php if ($po->status === 'draft' && !empty($items)): ?>

    <a href="<?= URLROOT ?>/purchaseorders/approve/<?= $po->id ?>"
        class="btn btn-success"
        onclick="return confirm('Approve this Purchase Order?')">

        Approve Purchase Order

    </a>

<?php endif; ?>

<!-- Print PO -->
<?php if (
    in_array(
        $po->status,
        ['approved', 'partial', 'received'],
        true
    )
): ?>

    <a
        href="<?= URLROOT ?>/purchaseorders/print/<?= $po->id ?>"
        class="btn btn-dark"
        target="_blank">

        <i class="fas fa-print"></i>
        Print PO

    </a>

<?php endif; ?>
</div>

<h4>
    Purchase Order Items
</h4>

<table class="table table-striped">

    <thead class="table-light">

        <tr>

            <th>Item</th>
            <th>SKU</th>
            <th width="120">Qty</th>
            <th width="150">Unit Cost</th>
            <th width="150">Total</th>

        </tr>

    </thead>

    <tbody>

        <?php if (!empty($items)): ?>

            <?php foreach ($items as $item): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($item->name) ?>
                    </td>
                     <td>
                        <?= htmlspecialchars($item->sku) ?>
                    </td>

                    <td>
                        <?= number_format($item->quantity, 2) ?>
                    </td>

                    <td>
                        <?= number_format($item->unit_cost, 2) ?>
                    </td>

                    <td>

                        <?= number_format(
                            $item->quantity * $item->unit_cost,
                            2
                        ) ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="4" class="text-center text-muted">

                    No items added yet

                </td>

            </tr>

        <?php endif; ?>

    </tbody>

    <tfoot>

        <tr>

            <th colspan="3" class="text-end">
                Grand Total
            </th>

            <th>

                <?= number_format($po->total_amount, 2) ?>

            </th>

        </tr>

    </tfoot>

</table>