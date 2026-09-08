<h2><i class="fas fa-warehouse"></i> Inventory Management</h2>

<a href="<?= URLROOT ?>/inventory/create" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Add Item
</a>

<?php if (count($low_stock ?? []) > 0) : ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <?= count($low_stock) ?> items below minimum stock!
    </div>
<?php endif; ?>

<?php

$totalValue = 0;

foreach ($stock as $inventory) {

    $totalValue += (
        (float)$inventory->quantity *
        (float)$inventory->cost_price
    );
}

?>

<!-- ---------------------------------------------- -->
<div class="row mb-4">

    <!-- TOTAL ITEMS -->
    <div class="col-md-2">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <small class="text-muted">
                    Total Items
                </small>

                <h3>
                    <?= count($stock) ?>
                </h3>

            </div>

        </div>

    </div>

    <!-- INVENTORY VALUE -->
    <div class="col-md-3">

        <div class="card shadow-sm border-0 bg-success text-white">

            <div class="card-body">

                <small>
                    Inventory Value
                </small>

                <h3>
                    LYD <?= '&#126;' . number_format(round($inventoryValue, -3), 0) ?>

                </h3>

            </div>

        </div>

    </div>

    <!-- LOW STOCK -->
    <div class="col-md-2">

        <div class="card shadow-sm border-0 bg-warning">

            <div class="card-body">

                <small>
                    Low Stock
                </small>

                <h3>
                    <?= count($low_stock ?? []) ?>
                </h3>

            </div>

        </div>

    </div>

    <!-- OUT OF STOCK -->
    <div class="col-md-2">

        <div class="card shadow-sm border-0 bg-danger text-white">

            <div class="card-body">

                <small>
                    Out of Stock
                </small>

                <h3>

                    <?php
                    $out = 0;

                    foreach ($stock as $i) {

                        if ($i->quantity <= 0) {
                            $out++;
                        }
                    }

                    echo $out;
                    ?>

                </h3>

            </div>

        </div>

    </div>

    <!-- RESERVED -->
    <div class="col-md-3">

        <div class="card shadow-sm border-0 bg-info text-white">

            <div class="card-body">

                <small>
                    Reserved Stock
                </small>

                <h3>

                    <?= $reserved_count ?? 0 ?>

                </h3>

            </div>

        </div>

    </div>

</div>
<!-- ------------------------------------------ -->
<div class="card mb-4 shadow-sm">

    <div class="card-body">

        <div class="d-flex flex-wrap gap-2">

            <a href="<?= URLROOT ?>/inventory/create" class="btn btn-primary">

                <i class="fas fa-plus"></i>
                Add Item

            </a>

            <a href="<?= URLROOT ?>/inventorymovements/receive" class="btn btn-success">

                <i class="fas fa-truck-loading"></i>
                Receive Stock (PO)

            </a>

            <a href="<?= URLROOT ?>/inventorytransfers" class="btn btn-warning">

                <i class="fas fa-exchange-alt"></i>
                Transfers

            </a>

            <a href="<?= URLROOT ?>/inventoryreservations" class="btn btn-info">

                <i class="fas fa-lock"></i>
                Reservations

            </a>

            <a href="<?= URLROOT ?>/purchaseorders" class="btn btn-dark">

                <i class="fas fa-file-invoice"></i>
                Purchase Orders

            </a>

        </div>

    </div>

</div>
<!-- ----------------------------------- -->

<!-- Search -->
<div class="card mb-3">

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">
                <i class="fas fa-search"></i>
                <strong>SEARCH:</strong>
                <br>
                <input type="text" id="inventorySearch" class="form-control" placeholder="Search Item Name or SKU/barcode ...">

            </div>

        </div>

    </div>

</div>
<!-- --------------------------------- -->
<div class="table-responsive">
    <table class="table table-striped" id="inventoryTable">
        <thead class="table-dark">
            <tr>
                <th width="40">#</th>
                <th>Item</th>
                <th>SKU</th>
                <th>Brand</th>
                <th>Made</th>
                <th>Category</th>
                <th>Physical</th>
                <th>Reserved</th>
                <th>Available</th>
                <th width="140">Pricing</th>
                <th width="80">Status</th>
                <th width="200">Actions</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($stock as $inventory) : ?>

                <tr class="<?= $inventory->quantity < $inventory->min_stock ? 'table-warning' : '' ?>">

                    <td>
                        <?= $inventory->id ?>
                    </td>
                    <td>
                        <strong>
                            <?= htmlspecialchars($inventory->name) ?>
                        </strong>
                        <br>

                        <small class="text-muted">

                            <?= $inventory->base_unit ?>

                            <?php if ($inventory->units_per_sale > 1) : ?>

                                • 1 <?= $inventory->sale_unit ?>

                                =
                                <?= $inventory->units_per_sale ?>
                                <?= $inventory->base_unit ?>

                            <?php endif; ?>

                        </small>

                    </td>

                    <!-- SKU -->
                    <td>

                        <code>
                            <?= htmlspecialchars($inventory->sku) ?>
                        </code>

                    </td>

                    <td><?= htmlspecialchars($inventory->brand_name ?? '-') ?></td>

                    <td>
                        <?= htmlspecialchars($inventory->brand_country ?? '-') ?>
                        <?= htmlspecialchars($inventory->country_code ?? '-') ?>
                    </td>

                    <!-- CATEGORY -->
                    <td>

                        <span class="badge bg-secondary">

                            <?= htmlspecialchars($inventory->category) ?>

                        </span>

                    </td>

                    <!-- STOCK -->
                    <td>
                        <strong>
                            <?= $inventory->quantity ?>
                            <?= $inventory->base_unit ?>
                        </strong>
                    </td>

                    <td>

                        <?php if ($inventory->reserved_qty > 0) : ?>

                            <span class="badge bg-warning text-dark">

                                <?= $inventory->reserved_qty ?>

                            </span>

                        <?php else : ?>

                            <span class="text-muted">0</span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <?php if ($inventory->available_qty <= 0) : ?>

                            <span class="badge bg-danger">

                                OUT

                            </span>

                        <?php else : ?>

                            <strong class="text-success">

                                <?= $inventory->available_qty ?>

                            </strong>

                        <?php endif; ?>

                    </td>



                    <!-- PRICING -->

                    <td>

                        <small class="d-block">

                            Cost:
                            <strong class="text-danger">

                                <?= number_format($inventory->cost_price, 2) ?>

                            </strong>

                        </small>

                        <small class="d-block">

                            Sale:
                            <strong class="text-success">

                                <?= number_format($inventory->price_per_base, 2) ?>

                            </strong>

                        </small>

                    </td>

                    <!-- STATUS -->
                    <td>

                        <?php if ($inventory->quantity <= 0) : ?>

                            <span class="badge bg-danger">
                                Out
                            </span>

                        <?php elseif ($inventory->quantity < $inventory->min_stock) : ?>

                            <span class="badge bg-warning text-dark">
                                Low
                            </span>

                        <?php else : ?>

                            <span class="badge bg-success">
                                OK
                            </span>

                        <?php endif; ?>

                    </td>

                    <!-- ACTIONS -->
                    <td>

                        <div class="btn-group btn-group-sm">

                            <a href="<?= URLROOT ?>/inventory/stockDetails/<?= $inventory->id ?>"
                                class="btn btn-sm btn-primary"
                                title="View Global Stock Details">
                                Stock Details <i class="fas fa-chart-bar"></i>

                            </a>

                            <a href="<?= URLROOT ?>/inventory/details/<?= $inventory->id ?>" class="btn btn-info">

                                View Item

                            </a>

                            <a href="<?= URLROOT ?>/inventory/edit/<?= $inventory->id ?>" class="btn btn-warning">

                                Edit Item

                            </a>

                               <a href="<?= URLROOT ?>/stockadjustments/create/<?= $inventory->id ?>" class="btn btn-success">

                                Adjust Qty

                            </a>

                            <a href="<?= URLROOT ?>/inventory/delete/<?= $inventory->id ?>" class="btn btn-danger" onclick="return confirm('Delete item?')">

                                Delete Item

                            </a>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>
    </table>

</div>
<script>
    // Search inventory
    document
        .getElementById('inventorySearch')
        .addEventListener('input', function() {

            let value =
                this.value.toLowerCase();

            document
                .querySelectorAll('#inventoryTable tbody tr')
                .forEach(row => {

                    let text =
                        row.innerText.toLowerCase();

                    row.style.display =
                        text.includes(value) ?
                        '' :
                        'none';
                });

        });
</script>