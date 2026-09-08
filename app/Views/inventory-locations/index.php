<h2>Inventory Locations</h2>

<a href="<?= URLROOT ?>/inventorylocations/create" class="btn btn-primary mb-3">
    Add Location
</a>

<table class="table table-striped">

    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Warehouse</th>
            <th>Address/Location</th>
            <th>Storekeeper</th>
            <th>Mobile Number</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

        <?php if (empty($locations)): ?>

            <tr>
                <td colspan="7" class="text-center py-5">

                    <i class="fas fa-warehouse fa-3x text-secondary mb-3"></i>

                    <h5 class="mt-3 text-muted">
                        No inventory locations available.
                    </h5>

                    <p class="text-muted mb-3">
                        Add your first warehouse or storage location to begin managing inventory.
                    </p>

                    <a href="<?= URLROOT ?>/inventorylocations/create"
                        class="btn btn-primary">

                        <i class="fas fa-plus"></i>
                        Add Location

                    </a>

                </td>
            </tr>

        <?php else: ?>

            <?php foreach ($locations as $loc) : ?>

                <tr>

                    <td><?= $loc->id ?></td>

                    <td>
                        <a href="<?= URLROOT ?>/inventorylocations/details/<?= $loc->id ?>">
                            <?= htmlspecialchars($loc->code) ?>
                        </a>
                    </td>

                    <td><?= htmlspecialchars($loc->name) ?></td>
                    <td><?= htmlspecialchars($loc->address ?? '') ?></td>
                    <td><?= htmlspecialchars($loc->mobile ?? '') ?></td>
                    <td><?= htmlspecialchars($loc->storekeeper ?? '') ?></td>

                    <td>

                        <a href="<?= URLROOT ?>/inventorylocations/edit/<?= $loc->id ?>"
                            class="btn btn-sm btn-warning">
                            Edit
                        </a>

                        <?php if ($loc->total_stock <= 0): ?>

                            <a href="<?= URLROOT ?>/inventorylocations/delete/<?= $loc->id ?>"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Are you sure you want to delete this location? This action cannot be undone.')">

                                <i class="bi bi-trash"></i>
                                Delete

                            </a>

                        <?php else: ?>

                            <button type="button"
                                class="btn btn-sm btn-warning"
                                disabled
                                title="This location cannot be deleted because it currently contains stock.">

                                <i class="bi bi-box-seam"></i>
                                [Contains Stock] <?= number_format($loc->total_stock, 2) ?> Items.

                            </button>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php endif; ?>

    </tbody>

</table>