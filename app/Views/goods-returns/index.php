<h2>
    <i class="fas fa-undo"></i>
    Goods Returns
</h2>

<div class="mb-3">
    <a href="<?= URLROOT ?>/goodsreturns/create"
       class="btn btn-primary">

        <i class="fas fa-plus"></i>
        Return Goods

    </a>
</div>

<div class="table-responsive">

    <table class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>Return Number</th>
                <th>Supplier</th>
                <th>GRN</th>
                <th>Purchase Order</th>
                <th>Return Date</th>
                <th>Reason</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

        <?php if (empty($returns)): ?>

            <tr>
                <td colspan="8"
                    class="text-center text-muted">

                    No goods returns found.

                </td>
            </tr>

        <?php else: ?>

            <?php foreach ($returns as $return): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars(
                            $return->return_number
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $return->supplier_name
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $return->grn_number
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $return->po_number
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $return->return_date
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $return->reason ?? ''
                        ) ?>
                    </td>

                    <td class="text-end">
                        <?= number_format(
                            (float)$return->total_amount,
                            2
                        ) ?>
                    </td>

                    <td>

                        <a href="<?= URLROOT ?>/goodsreturns/details/<?= $return->id ?>"
                           class="btn btn-sm btn-info">

                            <i class="fas fa-eye"></i>
                            View

                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

</div>