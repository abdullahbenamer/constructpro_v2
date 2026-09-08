<h2>
    <i class="fas fa-box"></i>
    Inventory Details
</h2>

<a href="<?= URLROOT ?>/inventory" class="btn btn-secondary mb-3">
    Back
</a>

<!-- ITEM INFO -->

<div class="card mb-4">

    <div class="card-body">

        <h3>
            <?= htmlspecialchars($item->name) ?>
        </h3>

        <div class="row">

            <div class="col-md-3">
                <strong>SKU:</strong><br>
                <?= htmlspecialchars($item->sku) ?>
            </div>

            <div class="col-md-3">
                <strong>Category:</strong><br>
                <?= htmlspecialchars($item->category) ?>
            </div>

            <div class="col-md-3">
                <strong>Current Stock:</strong><br>

                <span class="badge bg-primary">
                    <?= $item->quantity ?>
                    <?= $item->base_unit ?>
                </span>
            </div>

            <div class="col-md-3">
                <strong>Total Value:</strong><br>

                LYD
                <?= number_format(
                    $item->quantity * $item->cost_price,
                    2
                ) ?>
            </div>

        </div>

    </div>

</div>

<!-- MOVEMENTS -->

<div class="card mb-4">

    <div class="card-header">
        <strong>Stock Movements</strong>
    </div>

    <div class="card-body">

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Balance</th>
                    <th>Supplier</th>
                    <th>Reference</th>
                    <th>Notes</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($movements as $m) : ?>

                    <tr>

                        <td>
                            <?= $m->created_at ?>
                        </td>

                        <td>

                            <?php if ($m->type == 'IN') : ?>

                                <span class="badge bg-success">
                                    IN
                                </span>

                            <?php elseif ($m->type == 'OUT') : ?>

                                <span class="badge bg-danger">
                                    OUT
                                </span>

                            <?php else : ?>

                                <span class="badge bg-warning">
                                    ADJUSTMENT
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?= $m->quantity ?>
                        </td>

                        <td>
                            <?= $m->balance_after ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($m->supplier_name ?? '-') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($m->reference ?? '-') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($m->notes ?? '-') ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<!-- PROJECT USAGE -->

<div class="card">

    <div class="card-header">
        <strong>Project Usage</strong>
    </div>

    <div class="card-body">

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>Project</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($projectUsage as $u) : ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($u->project_title) ?>
                        </td>

                        <td>
                            <?= $u->quantity ?>
                        </td>

                        <td>
                            LYD <?= number_format($u->unit_price, 2) ?>
                        </td>

                        <td>
                            LYD
                            <?= number_format(
                                $u->quantity * $u->unit_price,
                                2
                            ) ?>
                        </td>

                        <td>
                            <?= $u->created_at ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>