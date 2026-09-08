<h2>
    Purchase Order Items
</h2>

<div class="card mb-4">
    <div class="card-body">

        <h5>
            PO:
            <?= htmlspecialchars($po->po_number) ?>
        </h5>

        <form method="POST"
              action="<?= URLROOT ?>/purchaseorders/addItem/<?= $po->id ?>">

            <div class="row">

                <div class="col-md-5 mb-3">
                    <label>Inventory Item</label>

                    <select name="inventory_id"
                            class="form-select"
                            required>

                        <option value="">
                            Select Item
                        </option>

                        <?php foreach ($inventory as $item): ?>

                            <option value="<?= $item->id ?>">

                                <?= htmlspecialchars($item->name) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label>Quantity</label>

                    <input type="number"
                           step="0.01"
                           min="0.01"
                           name="quantity"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-2 mb-3">
                    <label>Unit Cost</label>

                    <input type="number"
                           step="0.01"
                           min="0"
                           name="unit_cost"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-3 mb-3 d-flex align-items-end">

                    <button class="btn btn-success w-100">

                        <i class="fas fa-plus"></i>
                        Add Item

                    </button>

                </div>

            </div>

        </form>

    </div>
</div>

<table class="table table-bordered table-striped">

    <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>SKU</th>
            <th>Qty</th>
            <th>Unit Cost</th>
            <th>Total</th>
            <th>Qty Received</th>
            <th width="120">Actions</th>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($items as $row): ?>
<!-- <pre><?php //print_r($row); ?></pre> -->
            <tr>

                <td><?= $row->id ?></td>

                <td>
                    <?= htmlspecialchars($row->name) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row->sku) ?>
                </td>

                <td>
                    <?= $row->quantity ?>
                    <?= $row->base_unit ?>
                </td>

                <td>
                    <?= number_format($row->unit_cost, 2) ?>
                </td>

           <td>
    <?= number_format($row->quantity * $row->unit_cost, 2) ?>
</td>

                <td>
                <?= $row->received_quantity ?>
                </td>

                <td>

                    <a href="<?= URLROOT ?>/purchaseorders/deleteItem/<?= $row->id ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete item?')">

                        Delete

                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>

<a href="<?= URLROOT ?>/purchaseorders"
   class="btn btn-secondary">

    Back

</a>