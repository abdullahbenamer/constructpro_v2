<h2>
    <i class="fas fa-truck"></i>
    Suppliers
</h2>
<a href="<?= URLROOT ?>/suppliers/create"
   class="btn btn-primary mb-3">

    <i class="fas fa-plus"></i>
    Add Supplier

</a>
<table class="table table-striped">

    <thead>
        <tr>
            <th>ID</th>
            <th>Company</th>
            <th>Contact</th>
            <th>Phone</th>
            <th>Email</th>
            <th width="220">Actions</th>
        </tr>
    </thead>

  <tbody>

<?php if (!empty($suppliers)): ?>

    <?php foreach ($suppliers as $supplier): ?>

        <tr>

            <td><?= $supplier->id ?></td>

            <td>
                <a href="<?= URLROOT ?>/suppliers/info/<?= $supplier->id ?>"
                   class="fw-bold text-decoration-none">

                    <i class="fas fa-truck"></i>

                    <?= htmlspecialchars($supplier->company_name) ?>

                </a>
            </td>

            <td>
                <?= htmlspecialchars($supplier->contact_person ?? '-') ?>
            </td>

            <td>
                <?= htmlspecialchars($supplier->phone ?? '-') ?>
            </td>
            <td>
                <?= htmlspecialchars($supplier->email ?? '-') ?>
            </td>
            <td>
                <a href="<?= URLROOT ?>/suppliers/info/<?= $supplier->id ?>"
                   class="btn btn-sm btn-info">
                    View Profile
                </a>
                <a href="<?= URLROOT ?>/suppliers/edit/<?= $supplier->id ?>"
                   class="btn btn-sm btn-warning">
                    Edit
                </a>
                <a href="<?= URLROOT ?>/suppliers/delete/<?= $supplier->id ?>"
                   class="btn btn-sm btn-danger"
                   onclick="return confirm('Delete supplier?')">

                    Delete

                </a>

            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>

        <td colspan="6" class="text-center py-5">

            <i class="fas fa-truck-loading fa-3x text-secondary mb-3"></i>

            <h5 class="mt-3">

                No suppliers have been added yet.

            </h5>

            <p class="text-muted mb-3">

                Create your first supplier to start managing purchase orders and purchases.

            </p>

            <a href="<?= URLROOT ?>/suppliers/create"
               class="btn btn-primary">

                <i class="fas fa-plus"></i>

                Add First Supplier

            </a>

        </td>

    </tr>

<?php endif; ?>

</tbody>

</table>