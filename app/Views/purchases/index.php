<h2>
    <i class="fas fa-shopping-cart"></i>
    Purchases
</h2>

<a href="<?= URLROOT ?>/purchases/create"
   class="btn btn-primary mb-3">
   New Purchase
</a>

<table class="table table-striped">

    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Reference</th>
            <th>Total</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($purchases as $purchase): ?>

            <tr>

                <td><?= $purchase->id ?></td>

                <td>
                    <?= htmlspecialchars($purchase->supplier_name) ?>
                </td>

                <td>
                    <?= htmlspecialchars($purchase->reference ?? '-') ?>
                </td>

                <td>
                    LYD <?= number_format($purchase->total_amount, 2) ?>
                </td>

                <td>
                    <?= date('Y-m-d', strtotime($purchase->created_at)) ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>