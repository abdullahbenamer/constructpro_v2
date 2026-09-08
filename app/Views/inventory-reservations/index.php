<h2>
    Inventory Reservations
</h2>

<a href="<?= URLROOT ?>/inventoryreservations/create"
   class="btn btn-primary mb-3">

    New Reservation

</a>

<table class="table table-striped">

    <thead>

        <tr>

            <th>Date</th>
            <th>Item</th>
             <th>SKU</th>
            <th>Project/Site</th>
            <th>Required Date</th>
            <th>Qty</th>
            <th>Ordered By</th>
            <th>Status</th>
            <th>Actions</th>

        </tr>

    </thead>

   <tbody>

<?php if (!empty($reservations)): ?>

    <?php foreach ($reservations as $r): ?>

        <tr>

            <td><?= $r->created_at ?></td>

            <td><?= htmlspecialchars($r->item_name) ?></td>

            <td><?= htmlspecialchars($r->sku) ?></td>

            <td><?= htmlspecialchars($r->project_name ?? '-') ?></td>

            <?php
                $required = strtotime($r->required_by_date);
                $today = strtotime(date('Y-m-d'));
            ?>

            <td>

                <?php if ($r->status == 'ACTIVE' && $required < $today): ?>

                    <span class="badge bg-danger">
                        <?= date('d M Y', $required) ?>
                    </span>

                <?php elseif ($r->status == 'ACTIVE' && $required == $today): ?>

                    <span class="badge bg-warning text-dark">
                        Today
                    </span>

                <?php else: ?>

                    <span class="badge bg-success">
                        <?= date('d M Y', $required) ?>
                    </span>

                <?php endif; ?>

            </td>

            <td><?= $r->quantity ?></td>

            <td>
                <a href="<?= URLROOT ?>/users/details/<?= $r->created_by ?>">
                    <i class="fas fa-user text-primary"></i>
                    <?= htmlspecialchars($r->created_by_name ?? '-') ?>
                </a>
            </td>

            <td>

                <?php if ($r->status == 'ACTIVE'): ?>

                    <span class="badge bg-warning">
                        ACTIVE
                    </span>

                <?php elseif ($r->status == 'FULFILLED'): ?>

                    <span class="badge bg-success">
                        FULFILLED
                    </span>

                <?php else: ?>

                    <span class="badge bg-danger">
                        CANCELLED
                    </span>

                <?php endif; ?>

            </td>

            <td>

                <?php if ($r->status == 'ACTIVE'): ?>

                    <a href="<?= URLROOT ?>/inventoryreservations/edit/<?= $r->id ?>"
                       class="btn btn-primary btn-sm">
                        Edit
                    </a>

                    <a href="<?= URLROOT ?>/inventoryreservations/fulfill/<?= $r->id ?>"
                       class="btn btn-success btn-sm">
                        Fulfill
                    </a>

                    <a href="<?= URLROOT ?>/inventoryreservations/cancel/<?= $r->id ?>"
                       class="btn btn-warning btn-sm">
                        Cancel
                    </a>

                    <a href="<?= URLROOT ?>/inventoryreservations/delete/<?= $r->id ?>"
                       class="btn btn-outline-danger btn-sm"
                       onclick="return confirm('Delete this reservation?')">
                        Delete
                    </a>

                <?php endif; ?>

            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>
        <td colspan="9" class="text-center py-5">

            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>

            <h5 class="text-muted">
                No Inventory Reservations Found
            </h5>

            <p class="text-muted mb-3">
                There are currently no inventory reservations.
            </p>

            <a href="<?= URLROOT ?>/inventoryreservations/create"
               class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Create First Reservation
            </a>

                       

                    </td>
    </tr>

<?php endif; ?>

</tbody>

</table>