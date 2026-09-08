<div class="container-fluid mt-4">


    <!-- PAGE HEADER -->

    <div class="d-flex justify-content-between align-items-center mb-3">


        <div>

            <h4 class="mb-0">

                <i class="fas fa-file-alt"></i>
                Resource Requisition Details

            </h4>


            <small class="text-muted">

                View requisition header information

            </small>


        </div>




        <div>
            <?php if (
                $data['requisition']->status == 'DRAFT'
                &&
                !empty($data['items'])
            ): ?>

                <a href="<?= URLROOT ?>/ResourceRequisitions/submit/<?= $data['requisition']->id ?>"
                    class="btn btn-success"
                    onclick="return confirm('Submit this requisition?\n\nAfter submission it can no longer be edited.')">

                    <i class="fas fa-paper-plane"></i>
                    Submit Requisition

                </a>

            <?php endif; ?>

            <!-- APPROVE / REJECT -->

            <?php if (
                $data['requisition']->status == 'SUBMITTED'
                &&
                AuthHelper::canView('resource_requisitions.approve')
            ): ?>

                <a href="<?= URLROOT ?>/ResourceRequisitions/approve/<?= $data['requisition']->id ?>"
                    class="btn btn-success">
                    <i class="fas fa-check-circle"></i>
                    Approval Decision
                </a>

            <?php endif; ?>

            <?php if ($data['requisition']->status == 'DRAFT'): ?>

                <a href="<?= URLROOT ?>/ResourceRequisitions/edit/<?= $data['requisition']->id ?>"
                    class="btn btn-warning">

                    <i class="fas fa-edit"></i>
                    Edit

                </a>

            <?php endif; ?>


            <a href="<?= URLROOT ?>/ResourceRequisitions"
                class="btn btn-secondary">

                <i class="fas fa-arrow-left"></i>
                Back To Resource Requisitions

            </a>


        </div>


    </div>


    <!-- HEADER INFORMATION CARD -->


    <div class="card shadow-sm">


        <div class="card-header bg-white">


            <strong>

                <i class="fas fa-info-circle"></i>
                Header Information

            </strong>


        </div>





        <div class="card-body">


            <div class="row">



                <!-- REQUISITION NUMBER -->

                <div class="col-md-4 mb-3">


                    <label class="text-muted">

                        Requisition No.

                    </label>


                    <div class="fw-bold">

                        <?= $data['requisition']->requisition_no ?>

                    </div>

                </div>

                <!-- PROJECT -->

                <div class="col-md-4 mb-3">


                    <label class="text-muted">

                        Project

                    </label>


                    <div class="fw-bold">

                        <?= $data['requisition']->project_name ?? '-' ?>

                    </div>

                </div>

                <!-- STATUS -->

                <div class="col-md-4 mb-3">

                    <label class="text-muted">

                        Status

                    </label>

                    <div>
                        <span class="badge bg-success">

                            <?= $data['requisition']->status ?>

                        </span>

                    </div>

                </div>

            </div>

    <div class="row">

    <!-- REQUEST DATE -->
    <div class="col-md-3 mb-3">

        <label class="text-muted">
            Request Date
        </label>

        <div>
            <?= htmlspecialchars($data['requisition']->request_date ?? '-') ?>
        </div>

    </div>


    <!-- REQUIRED DATE -->
    <div class="col-md-3 mb-3">

        <label class="text-muted">
            Required Date
        </label>

        <div>
            <?= htmlspecialchars($data['requisition']->required_date ?? '-') ?>
        </div>

    </div>


    <!-- PRIORITY -->
    <div class="col-md-3 mb-3">

        <label class="text-muted">
            Priority
        </label>

        <div>

            <?php if ($data['requisition']->priority == 'HIGH'): ?>

                <span class="badge bg-danger">
                    HIGH
                </span>

            <?php elseif ($data['requisition']->priority == 'MEDIUM'): ?>

                <span class="badge bg-warning text-dark">
                    MEDIUM
                </span>

            <?php else: ?>

                <span class="badge bg-secondary">
                    LOW
                </span>

            <?php endif; ?>

        </div>

    </div>


    <!-- DELIVERY METHOD -->
    <div class="col-md-3 mb-3">

        <label class="text-muted">
            Delivery Method
        </label>

        <div>

            <?php if ($data['requisition']->delivery_method === 'WAREHOUSE'): ?>

                <span class="badge bg-primary">
                    <i class="fas fa-warehouse"></i>
                    Warehouse
                </span>

            <?php elseif ($data['requisition']->delivery_method === 'DIRECT_TO_PROJECT_SITE'): ?>

                <span class="badge bg-info text-dark">
                    <i class="fas fa-truck"></i>
                    Direct to Project Site
                </span>

            <?php else: ?>

                <span class="text-muted">
                    -
                </span>

            <?php endif; ?>

        </div>

    </div>

</div>

<!-- Target warehouse -->
 <div class="row">

    <!-- TARGET WAREHOUSE -->
    <div class="col-md-6 mb-3">

        <label class="text-muted">
            Target Warehouse
        </label>

        <div class="fw-semibold">

            <?php if (
                $data['requisition']->delivery_method === 'WAREHOUSE'
                && !empty($data['requisition']->target_warehouse_name)
            ): ?>

                <i class="fas fa-warehouse text-primary"></i>

                <?= htmlspecialchars(
                    $data['requisition']->target_warehouse_name
                ) ?>

            <?php elseif (
                $data['requisition']->delivery_method === 'DIRECT_TO_PROJECT_SITE'
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


    <!-- DESTINATION NOTE -->
    <div class="col-md-6 mb-3">

        <label class="text-muted">
            Destination
        </label>

        <div>

            <?php if (
                $data['requisition']->delivery_method === 'WAREHOUSE'
            ): ?>

                <span class="text-muted">
                    Goods are planned to be delivered to the selected warehouse.
                </span>

            <?php elseif (
                $data['requisition']->delivery_method === 'DIRECT_TO_PROJECT_SITE'
            ): ?>

                <span class="text-muted">
                    Goods are planned for direct delivery to the project site.
                </span>

            <?php else: ?>

                <span class="text-muted">
                    Not specified
                </span>

            <?php endif; ?>

        </div>

    </div>

</div>
            <!-- REMARKS -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="text-muted">
                        Remarks

                    </label>


                    <div class="border rounded p-3 bg-light">


                        <?= nl2br($data['requisition']->remarks) ?>

                    </div>
                </div>
            </div>
            <div class="row">

                <!-- REQUESTED BY -->

                <div class="col-md-4 mb-3">

                    <label class="text-muted">

                        Requested By

                    </label>

                    <div>

                        <?php if (!empty($data['requisition']->requested_by)): ?>

                            <a href="<?= URLROOT ?>/users/details/<?= $data['requisition']->requested_by ?>" class="link-primary text-decoration-none fw-semibold">
                                <?= htmlspecialchars($data['requisition']->requested_by_name ?? '-') ?>
                            </a>

                        <?php else: ?>

                            N/A

                        <?php endif; ?>

                    </div>
                    <br>

                    Submitted By:
                    <div><?php if (!empty($data['requisition']->submitted_by)): ?>

                            <a href="<?= URLROOT ?>/users/details/<?= $data['requisition']->submitted_by ?>"
                                class="link-primary text-decoration-none">

                                <?= htmlspecialchars($data['requisition']->submitted_by_name ?? '-') ?>

                            </a>

                        <?php else: ?>

                            <span class="text-muted">Not Submitted</span>

                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <br>
   


<!-- FULFILLMENT ACTIONS -->

<?php if (
    $data['requisition']->status === 'APPROVED'
    ||
    $data['requisition']->status === 'PARTIAL'
): ?>

    <div class="d-flex gap-2 mb-3">

        <?php if (!empty($data['hasMaterialItems'])): ?>

            <a
                href="<?= URLROOT ?>/ResourceRequisitionFulfillments/create/<?= $data['requisition']->id ?>"
                class="btn btn-success"
            >
                <i class="fas fa-boxes"></i>
                Fulfill Materials
            </a>

        <?php endif; ?>

        <a
    href="<?= URLROOT ?>/requisitionpurchaseorders/create/<?= $data['requisition']->id ?>"
    class="btn btn-primary">

    <i class="fas fa-shopping-cart"></i>
    Create PO for Materials

</a>


        <?php if (!empty($data['hasResourceItems'])): ?>

            <a
                href="<?= URLROOT ?>/ResourceRequisitionFulfillments/createResource/<?= $data['requisition']->id ?>"
                class="btn btn-primary"
            >
                <i class="fas fa-tools"></i>
                Fulfill Resources
            </a>

        <?php endif; ?>

    </div>

<?php endif; ?>

    <!-- REQUISITION ITEMS -->

    <div class="card shadow-sm mt-4">

        <div class="card-header d-flex justify-content-between align-items-center">

            <strong>
                <i class="fas fa-list"></i>
                Requisition Items
            </strong>


            <?php if ($data['requisition']->status == 'DRAFT'): ?>

                <a href="<?= URLROOT ?>/ResourceRequisitionItems/create/<?= $data['requisition']->id ?>"
                    class="btn btn-primary">

                    <i class="fas fa-plus"></i>
                    Add Item

                </a>

            <?php endif; ?>


        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead>

                        <tr>

                            <th width="60">#</th>

                            <th>Resource</th>

                            <th width="120">Requested</th>

                            <th width="150">Available</th>

                            <th width="120">Unit</th>

                            <th>Remarks</th>

                            <th width="140">Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($data['items'])): ?>

                            <?php $i = 1; ?>

                            <?php foreach ($data['items'] as $item): ?>

                                <tr>

                                    <td><?= $i++ ?></td>


                                    <td>


                                        <strong>

                                            <?= $item->resource_code ?? '-' ?>

                                        </strong>


                                        <br>


                                        <?= $item->resource_name ?? $item->description ?>


                                        <br>
                                        <small class="text-muted">
                                            Category:
                                            <?= $item->category_name ?? '-' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?= number_format((float)$item->quantity, 2) ?>
                                    </td>

                                    <td>

                                        <?php if ($item->resource_source === 'INVENTORY'): ?>

                                            <?php
                                            $available = (float)($item->available_qty ?? 0);
                                            $requested = (float)$item->quantity;
                                            ?>

                                            <?php if ($available >= $requested): ?>

                                                <span class="fw-bold text-success">
                                                    Available: <?= number_format($available, 2) ?>
                                                </span>

                                            <?php elseif ($available > 0): ?>

                                                <span class="fw-bold text-danger">
                                                    Available: <?= number_format($available, 2) ?>
                                                </span>

                                            <?php else: ?>

                                                <span class="fw-bold text-danger">
                                                    OUT OF STOCK
                                                </span>

                                            <?php endif; ?>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                N/A
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars($item->uom ?? '-') ?>
                                    </td>
                                    
                                    <td><?= $item->remarks ?></td>
                                    <td>
                                        <?php if ($data['requisition']->status == 'DRAFT'): ?>
                                            <a href="<?= URLROOT ?>/ResourceRequisitionItems/edit/<?= $item->id ?>"
                                                class="btn btn-sm btn-warning">
                                                <!-- <i class="fas fa-edit"></i> -->
                                                Edit
                                            </a>
                                            <a href="<?= URLROOT ?>/ResourceRequisitionItems/delete/<?= $item->id ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this item?');">
                                                <!-- <i class="fas fa-trash"></i> -->
                                                Delete
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                Locked
                                            </span>
                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                               <td colspan="7" class="text-center text-muted">

                                    No requisition items have been added.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- //////////////////////// -->


    <div class="card shadow-sm mt-4">

        <div class="card-header">

            <strong>
                <i class="fas fa-check-circle"></i>
                Approval History
            </strong>

        </div>

        <div class="card-body text-muted">

            Approval workflow will be implemented in a future milestone.

        </div>

    </div>

    <div class="card shadow-sm mt-4">

        <div class="card-header">

            <strong>
                <i class="fas fa-paperclip"></i>
                Attachments
            </strong>

        </div>

        <div class="card-body text-muted">

            Attachment management will be implemented in a future milestone.

        </div>

    </div>

    <div class="card shadow-sm mt-4 mb-4">

        <div class="card-header">

            <strong>
                <i class="fas fa-comments"></i>
                Comments
            </strong>

        </div>

        <div class="card-body text-muted">

            Comments will be implemented in a future milestone.

        </div>

    </div>



</div>