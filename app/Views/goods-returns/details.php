<h2>
    <i class="fas fa-undo"></i>
    Goods Return Details
</h2>

<div class="card mb-4">

    <div class="card-header">
        <strong>
            Return Information
        </strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">

                <strong>Return Number</strong>

                <div>
                    <?= htmlspecialchars(
                        $return->return_number
                    ) ?>
                </div>

            </div>

            <div class="col-md-4 mb-3">

                <strong>Supplier</strong>

                <div>
                    <?= htmlspecialchars(
                        $return->supplier_name
                    ) ?>
                </div>

            </div>

            <div class="col-md-4 mb-3">

                <strong>Return Date</strong>

                <div>
                    <?= htmlspecialchars(
                        $return->return_date
                    ) ?>
                </div>

            </div>

            <div class="col-md-4 mb-3">

                <strong>GRN</strong>

                <div>
                    <?= htmlspecialchars(
                        $return->grn_number
                    ) ?>
                </div>

            </div>

            <div class="col-md-4 mb-3">

                <strong>Purchase Order</strong>

                <div>
                    <?= htmlspecialchars(
                        $return->po_number
                    ) ?>
                </div>

            </div>

            <div class="col-md-4 mb-3">

                <strong>Total Amount</strong>

                <div class="fw-bold">
                    <?= number_format(
                        (float)$return->total_amount,
                        2
                    ) ?>
                </div>

            </div>

            <div class="col-md-6 mb-3">

                <strong>Reason</strong>

                <div>
                    <?= htmlspecialchars(
                        $return->reason ?? ''
                    ) ?>
                </div>

            </div>

            <div class="col-md-6 mb-3">

                <strong>Notes</strong>

                <div>
                    <?= nl2br(
                        htmlspecialchars(
                            $return->notes ?? ''
                        )
                    ) ?>
                </div>

            </div>

        </div>

    </div>

</div>


<div class="card">

    <div class="card-header">
        <strong>
            Returned Items
        </strong>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead>

                    <tr>

                        <th>#</th>
                        <th>Item</th>
                        <th>SKU</th>
                        <th>Original GRN Location</th>
                        <th>Returned From</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Total</th>

                    </tr>

                </thead>

                <tbody>

                <?php if (empty($items)): ?>

                    <tr>

                        <td colspan="8"
                            class="text-center text-muted">

                            No returned items found.

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($items as $item): ?>

                        <tr>

                            <td>
                                <?= $item->id ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $item->name
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $item->sku ?? ''
                                ) ?>
                            </td>

                            <td>
    <?= htmlspecialchars(
        $item->original_location_name ?? 'N/A'
    ) ?>
</td>

<td>
    <?= htmlspecialchars(
        $item->return_location_name ?? 'N/A'
    ) ?>
</td>

                            <td>
                                <?= number_format(
                                    (float)$item->quantity,
                                    2
                                ) ?>
                                <?= htmlspecialchars(
                                    $item->base_unit ?? ''
                                ) ?>
                            </td>

                            <td>
                                <?= number_format(
                                    (float)$item->unit_cost,
                                    2
                                ) ?>
                            </td>

                            <td>
                                <?= number_format(
                                    (float)$item->total_cost,
                                    2
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<div class="mt-3">

    <a href="<?= URLROOT ?>/goodsreturns"
       class="btn btn-secondary">

        <i class="fas fa-arrow-left"></i>
        Back to Goods Returns

    </a>

</div>