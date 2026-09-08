<h2>Purchase Orders</h2>

<a href="<?= URLROOT ?>/purchaseorders/create"
   class="btn btn-primary mb-3">

    Create Purchase Order

</a>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>PO Number</th>
            <th>Supplier</th>
            <th>Status</th>
            <th>Total</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($orders as $po): ?>

            <tr>

                <td><?= $po->po_number ?></td>

<td>
    <a href="<?= URLROOT ?>/suppliers/info/<?= $po->supplier_id ?>"
       target="_blank"
       class="fw-bold text-decoration-none">
        <i class="fas fa-truck"></i>
        <?= htmlspecialchars($po->supplier_name) ?>
    </a>
</td>

                <td><?= $po->status ?></td>

                <td><?= number_format($po->total_amount,2) ?></td>

                <td><?= date('Y-m-d', strtotime($po->created_at)) ?></td>

                <td>

                <a href="<?= URLROOT ?>/purchaseorders/details/<?= $po->id ?>"
   class="btn btn-sm btn-info">
    Open
</a>

<a href="<?= URLROOT ?>/purchaseorders/itemsPage/<?= $po->id ?>" class="btn btn-sm btn-primary">
    Items
</a>

<?php if (
    $po->status === 'draft' &&
    (int)$po->item_count > 0
): ?>

    <a href="<?= URLROOT ?>/purchaseorders/approve/<?= $po->id ?>"
       class="btn btn-sm btn-success"
       onclick="return confirm('Approve this Purchase Order?')">

        Approve

    </a>

<?php endif; ?>

<?php if (in_array($po->status, ['draft', 'approved', 'partial'], true)): ?>

    <a href="<?= URLROOT ?>/purchaseorders/cancel/<?= $po->id ?>"
       class="btn btn-sm btn-danger"
       onclick="return confirm('Cancel this Purchase Order? This action cannot be undone.')">

        Cancel

    </a>

<?php endif; ?>
                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>
   

</table>