<h2>
     <i class="fas fa-file-invoice-dollar"></i> Supplier Quotation
</h2>


<div class="card mb-4">

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <p>
                    <strong>Quotation #:</strong>
                    <?= htmlspecialchars(
                        $quotation->quotation_number
                    ) ?>
                </p>

                <p>
                    <strong>Supplier:</strong>
                    <?= htmlspecialchars(
                        $quotation->supplier_name
                    ) ?>
                </p>

                <p>
                    <strong>Supplier Reference:</strong>
                    <?= htmlspecialchars(
                        $quotation->supplier_reference ?? '-'
                    ) ?>
                </p>

                <p>
    <strong>Procurement Reference:</strong>
    <?= htmlspecialchars(
        $quotation->procurement_reference ?? '-'
    ) ?>
</p>

<p>
    <strong>Required Delivery:</strong>
    <?= htmlspecialchars(
        $quotation->required_delivery_date ?? '-'
    ) ?>
</p>

<p>
    <strong>Supplier Promised Delivery:</strong>
    <?= htmlspecialchars(
        $quotation->promised_delivery_date ?? '-'
    ) ?>
</p>

            </div>

            <div class="col-md-6">

                <p>
                    <strong>Quotation Date:</strong>
                    <?= htmlspecialchars(
                        $quotation->quotation_date
                    ) ?>
                </p>

                <p>
                    <strong>Valid Until:</strong>
                    <?= htmlspecialchars(
                        $quotation->valid_until ?? '-'
                    ) ?>
                </p>

                <p>
                    <strong>Status:</strong>

                    <?php if ($quotation->status === 'DRAFT'): ?>

                        <span class="badge bg-secondary">
                            DRAFT
                        </span>

                    <?php elseif ($quotation->status === 'ACCEPTED'): ?>

                        <span class="badge bg-success">
                            ACCEPTED
                        </span>

                    <?php else: ?>

                        <span class="badge bg-danger">
                            CANCELLED
                        </span>

                    <?php endif; ?>

                </p>

                

            </div>

        </div>



        <?php if (!empty($quotation->notes)): ?>

            <hr>

            <strong>Notes:</strong><br>

            <?= nl2br(
                htmlspecialchars($quotation->notes)
            ) ?>

        <?php endif; ?>
        
        <?php if (!empty($quotation->evaluation_notes)): ?>

    <hr>

    <strong>
        Procurement / Evaluation Notes:
    </strong>
    <br>

    <?= nl2br(
        htmlspecialchars(
            $quotation->evaluation_notes
        )
    ) ?>

<?php endif; ?>

    </div>

</div>


<?php if ($quotation->status === 'DRAFT'): ?>

<div class="card mb-4">

    <div class="card-body">

        <h5>
            Add Quotation Item
        </h5>

        <form method="POST"
              action="<?= URLROOT ?>/supplierquotations/addItem/<?= $quotation->id ?>">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Existing Inventory Item
                    </label>

                    <select name="inventory_id"
                            class="form-select">

                        <option value="">
                            -- New / Not Yet in Inventory --
                        </option>

                        <?php foreach ($inventory ?? [] as $item): ?>

                            <option value="<?= $item->id ?>">

                                <?= htmlspecialchars(
                                    $item->name
                                ) ?>

                                -

                                <?= htmlspecialchars(
                                    $item->sku
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Description *
                    </label>

                    <input type="text"
                           name="description"
                           class="form-control"
                           required>

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        UOM
                    </label>

                    <select name="unit_id"
                            class="form-select">

                        <option value="">
                            Select UOM
                        </option>

                        <?php foreach ($units ?? [] as $unit): ?>

                            <option value="<?= $unit->id ?>">

                                <?= htmlspecialchars(
                                    $unit->unit_code
                                ) ?>

                                -
                                <?= htmlspecialchars(
                                    $unit->unit_name
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>


            <div class="mb-3">

                <label class="form-label">
                    Specification
                </label>

                <textarea name="specification"
                          class="form-control"
                          rows="3"
                          placeholder="Supplier specification, size, length, weight, model, etc."></textarea>

            </div>


            <div class="row">

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Quantity
                    </label>

                    <input type="number"
                           name="quantity"
                           class="form-control"
                           step="0.01"
                           min="0.01"
                           required>

                </div>

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Unit Price
                    </label>

                    <input type="number"
                           name="unit_price"
                           class="form-control"
                           step="0.01"
                           min="0"
                           required>

                </div>

<div class="row">

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Quality / Specification Assessment
        </label>

        <select name="quality_status"
                class="form-select">

            <option value="">
                -- Not Evaluated --
            </option>

            <option value="MEETS">
                Meets Specification
            </option>

            <option value="PARTIAL">
                Partially Meets
            </option>

            <option value="DOES_NOT_MEET">
                Does Not Meet
            </option>

        </select>

    </div>

    <div class="col-md-8 mb-3">

        <label class="form-label">
            Quality / Evaluation Notes
        </label>

        <input type="text"
               name="quality_notes"
               class="form-control"
               placeholder="Technical remarks, manufacturer/model confirmation, deviations, etc.">

    </div>

</div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Notes
                    </label>

                    <input type="text"
                           name="item_notes"
                           class="form-control">

                </div>

            </div>


            <button class="btn btn-success">

                <i class="fas fa-plus"></i>
                Add Item

            </button>

        </form>

    </div>

</div>

<?php endif; ?>


<h4>
    Quotation Items
</h4>

<table class="table table-bordered table-striped">

    <thead>

        <tr>

            <th>#</th>
            <th>Item</th>
            <th>Specification</th>
            <th>UOM</th>
            <th>Qty</th>
            <th>Quality</th>
            <th>Unit Price</th>
            <th>Total</th>

            <?php if ($quotation->status === 'DRAFT'): ?>
                <th>Actions</th>
            <?php endif; ?>

        </tr>

    </thead>

    <tbody>

        <?php $grandTotal = 0; ?>

        <?php if (!empty($items)): ?>

            <?php foreach ($items as $item): ?>

                <?php
                    $total =
                        (float)$item->quantity *
                        (float)$item->unit_price;

                    $grandTotal += $total;
                ?>

                <tr>

                    <td>
                        <?= $item->id ?>
                    </td>

                    <td>

                        <?php if (!empty($item->inventory_id)): ?>

                            <?= htmlspecialchars(
                                $item->inventory_name
                            ) ?>

                        <?php else: ?>

                            <?= htmlspecialchars(
                                $item->description
                            ) ?>

                            <span class="badge bg-warning text-dark">
                                New Item
                            </span>

                        <?php endif; ?>

                    </td>

                    <td>
                        <?= nl2br(
                            htmlspecialchars(
                                $item->specification ?? ''
                            )
                        ) ?>
                    </td>

                    <td>

                        <?= htmlspecialchars(
                            $item->unit_code ?? '-'
                        ) ?>

                    </td>

                    <td>
                        <?= number_format(
                            $item->quantity,
                            2
                        ) ?>
                    </td>

                    <td>

    <?php if ($item->quality_status === 'MEETS'): ?>

        <span class="badge bg-success">
            MEETS
        </span>

    <?php elseif ($item->quality_status === 'PARTIAL'): ?>

        <span class="badge bg-warning text-dark">
            PARTIAL
        </span>

    <?php elseif ($item->quality_status === 'DOES_NOT_MEET'): ?>

        <span class="badge bg-danger">
            DOES NOT MEET
        </span>

    <?php else: ?>

        <span class="text-muted">
            Not Evaluated
        </span>

    <?php endif; ?>

    <?php if (!empty($item->quality_notes)): ?>

        <div class="small text-muted mt-1">

            <?= htmlspecialchars(
                $item->quality_notes
            ) ?>

        </div>

    <?php endif; ?>

</td>

                    <td>
                        <?= number_format(
                            $item->unit_price,
                            2
                        ) ?>
                    </td>

                    <td>
                        <?= number_format(
                            $total,
                            2
                        ) ?>
                    </td>

                    <?php if ($quotation->status === 'DRAFT'): ?>

                        <td>

                            <a href="<?= URLROOT ?>/supplierquotations/deleteItem/<?= $item->id ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this quotation item?')">

                                Delete

                            </a>

                        </td>

                    <?php endif; ?>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="8"
                    class="text-center text-muted">

                    No items added yet.

                </td>

            </tr>

        <?php endif; ?>

    </tbody>

    <tfoot>

        <tr>

            <th colspan="6"
                class="text-end">

                Grand Total

            </th>

            <th>
                <?= number_format(
                    $grandTotal,
                    2
                ) ?>
            </th>

            <?php if ($quotation->status === 'DRAFT'): ?>
                <th></th>
            <?php endif; ?>

        </tr>

    </tfoot>

</table>


<div class="mt-3">

    <a href="<?= URLROOT ?>/supplierquotations"
       class="btn btn-secondary">

        Back

    </a>

    <?php if ($quotation->status === 'DRAFT'): ?>

        <a href="<?= URLROOT ?>/supplierquotations/accept/<?= $quotation->id ?>"
           class="btn btn-success"
           onclick="return confirm('Accept this supplier quotation?')">

            Accept Quotation

        </a>

        <a href="<?= URLROOT ?>/supplierquotations/cancel/<?= $quotation->id ?>"
           class="btn btn-danger"
           onclick="return confirm('Cancel this quotation?')">

            Cancel

        </a>

   <?php elseif ($quotation->status === 'ACCEPTED'): ?>

    <span class="badge bg-success fs-6">
        ACCEPTED
    </span>

    <?php if (empty($quotation->purchase_order_id)): ?>

        <a
            href="<?= URLROOT ?>/supplierquotations/createPO/<?= $quotation->id ?>"
            class="btn btn-primary ms-2"
            onclick="return confirm('Create a Purchase Order from this accepted quotation?')">

            <i class="fas fa-file-invoice"></i>
            Create PO

        </a>

    <?php else: ?>

        <a
            href="<?= URLROOT ?>/purchaseorders/details/<?= $quotation->purchase_order_id ?>"
            class="btn btn-info ms-2">

            <i class="fas fa-file-invoice"></i>
            View PO

        </a>

    <?php endif; ?>

<?php endif; ?>

</div>