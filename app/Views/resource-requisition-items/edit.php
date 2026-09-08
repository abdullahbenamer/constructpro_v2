<div class="container-fluid mt-4">

    <!-- PAGE HEADER -->

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">

                <i class="fas fa-edit"></i>

                Edit Requisition Item #<?= $data['item']->id ?>

            </h4>

            <small class="text-muted">

                You can Edit/Update quantity, description, and remarks Only

            </small>

        </div>


        <a
            href="<?= URLROOT ?>/ResourceRequisitions/details/<?= $data['item']->requisition_id ?>"
            class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

    </div>


    <!-- WARNING -->

    <div class="alert alert-danger">

        <strong>Note:</strong>

        Changing the material/resource or resource type requires
        deleting the existing item and creating a new item.

    </div>


    <!-- FORM -->

    <div class="card shadow-sm">

        <div class="card-header">

            <strong>

                <i class="fas fa-box"></i>

                Requisition Item

            </strong>

        </div>


        <div class="card-body">

            <form
                method="POST"
                action="<?= URLROOT ?>/ResourceRequisitionItems/update/<?= $data['item']->id ?>">


                <!--
                |--------------------------------------------------------------------------
                | RESOURCE TYPE / DESCRIPTION / QUANTITY
                |--------------------------------------------------------------------------
                -->

                <div class="row">

                    <!-- RESOURCE TYPE -->

                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Resource Type

                        </label>

                        <div>

                            <?php if ($data['item']->resource_source === 'INVENTORY'): ?>

                                <span class="badge bg-primary fs-6">

                                    MATERIAL

                                </span>

                            <?php else: ?>

                                <span class="badge bg-secondary fs-6">

                                    NON MATERIAL

                                </span>

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- DESCRIPTION -->

                    <div class="col-md-5 mb-3">

                        <label class="form-label">

                            Description

                        </label>

                        <input
                            type="text"
                            name="description"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $data['item']->description
                                ?? $data['item']->resource_name
                                ?? ''
                            ) ?>"
                            required>

                    </div>


                    <!-- QUANTITY -->

                    <div class="col-md-2 mb-3">

                        <label class="form-label">

                            Quantity

                        </label>

                        <input
                            type="number"
                            name="quantity"
                            class="form-control"
                            step="0.01"
                            min="0.01"
                            value="<?= htmlspecialchars($data['item']->quantity) ?>"
                            required>

                    </div>


                    <!-- UOM -->

                    <div class="col-md-2 mb-3">

                        <label class="form-label">

                            UOM

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                $data['item']->uom
                                ?? $data['item']->unit_name
                                ?? ''
                            ) ?>"
                            readonly>

                    </div>

                </div>


                <!--
                |--------------------------------------------------------------------------
                | RESOURCE / INVENTORY
                |--------------------------------------------------------------------------
                -->

                <div class="row">

                    <div class="col-md-8 mb-3">

                        <label class="form-label">

                            <?= $data['item']->resource_source === 'INVENTORY'
                                ? 'Inventory Item'
                                : 'Resource' ?>

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="<?= htmlspecialchars(
                                ($data['item']->resource_code ?? '') .
                                ' - ' .
                                ($data['item']->resource_name ?? '')
                            ) ?>"
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
                        rows="4"><?= htmlspecialchars(
                            $data['item']->remarks ?? ''
                        ) ?></textarea>

                </div>


                <!-- ACTIONS -->

                <div class="text-end">

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="fas fa-save"></i>

                        Update Item

                    </button>


                    <a
                        href="<?= URLROOT ?>/ResourceRequisitions/details/<?= $data['item']->requisition_id ?>"
                        class="btn btn-secondary">

                        Cancel

                    </a>

                </div>


            </form>

        </div>

    </div>

</div>