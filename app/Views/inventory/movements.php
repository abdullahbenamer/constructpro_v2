<h2>
    <i class="fas fa-exchange-alt"></i>
    Inventory Movements
</h2>

<div class="table-responsive">

    <table class="table table-striped table-bordered">

        <thead>
            <tr>
                <th>Date</th>
                <th>Item</th>
                <th>User</th>
                <th>Type</th>
                <th>Qty</th>
                <th>Source Location</th>
                <th>WH Balance After</th>
                <th>Global Balance After</th>
                <th>Reference</th>
                <th>Notes</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($movements as $move) : ?>

                <tr>

                    <td>
                        <?= date('Y-m-d', strtotime($move->created_at)) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($move->item_name ?? 'Unknown') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($move->user_name ?? 'System') ?>
                    </td>

                    <td>
                        <?php if ($move->type == 'IN') : ?>
                            <span class="badge bg-success">IN</span>

                        <?php elseif ($move->type == 'OUT') : ?>
                            <span class="badge bg-danger">OUT</span>

                        <?php else : ?>
                            <span class="badge bg-warning text-dark">
                                ADJUSTMENT
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?= number_format($move->quantity, 2) ?>
                    </td>

                    <td>
    <?php if (!empty($move->location_code)): ?>

        <span class="badge bg-secondary">
            <?= htmlspecialchars($move->location_code) ?>
        </span>

        <?php if (!empty($move->location_name)): ?>
            <br>
            <small>
                <?= htmlspecialchars($move->location_name) ?>
            </small>
        <?php endif; ?>

    <?php else: ?>

        <span class="text-muted">N/A</span>

    <?php endif; ?>
</td>

<td>
    <?= number_format($move->balance_after, 2) ?>
</td>

<td>
    <?= number_format($move->global_balance_after, 2) ?>
</td>
                    <td>
                        <?= htmlspecialchars($move->reference ?? '-') ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($move->notes ?? '-') ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>