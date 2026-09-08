<h2>
    <?= htmlspecialchars($supplier->company_name) ?>
</h2>

<div class="card mb-4">

    <div class="card-body">

        <p>
            <strong>Contact:</strong>
            <?= htmlspecialchars($supplier->contact_person) ?>
        </p>

        <p>
            <strong>Phone:</strong>
            <?= htmlspecialchars($supplier->phone) ?>
        </p>

        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($supplier->email) ?>
        </p>

        <p>
            <strong>Address:</strong>
            <?= htmlspecialchars($supplier->address) ?>
        </p>

        <p>
            <strong>Total Purchases:</strong>

            <?= number_format($total_purchases ?? 0, 2) ?>
        </p>

    </div>

</div>

<h4>Purchase Orders</h4>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>PO Number</th>
            <th>Status</th>
            <th>Total</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($purchase_orders as $po): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($po->po_number) ?>
                </td>

                <td>
                    <?= htmlspecialchars($po->status) ?>
                </td>

                <td>
                    <?= number_format($po->total_amount, 2) ?>
                </td>

                <td>
                    <?= $po->created_at ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>