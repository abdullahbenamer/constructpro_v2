<?php if (isset($inventory) && $inventory): ?>
<h2><i class="fas fa-edit"></i> Edit Inventory Item #<?= $inventory->id ?></h2>

<form method="POST">
    <input type="hidden" name="id" value="<?= $inventory->id ?>">

    <div class="row">
        <!-- NAME -->
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">Item Name *</label>
                <input type="text" name="name"
                       value="<?= htmlspecialchars($inventory->name ?? '') ?>"
                       class="form-control" required>
            </div>
        </div>

        <!-- SKU -->
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label">SKU *</label>
                <input type="text" name="sku"
                       value="<?= htmlspecialchars($inventory->sku ?? '') ?>"
                       class="form-control" required>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- CATEGORY -->
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <?php
                    $categories = ['Switchgear', 'Protection', 'Instrumentation', 'General'];
                    foreach ($categories as $cat): ?>
                        <option value="<?= $cat ?>"
                            <?= ($inventory->category == $cat) ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- QUANTITY -->
        <!-- <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity"
                       value="<?//= $inventory->quantity ?? 0 ?>"
                       class="form-control">
            </div>
        </div> -->

        <!-- MIN STOCK -->
        <div class="col-md-4">
            <div class="mb-3">
                <label class="form-label">Min Stock</label>
                <input type="number" name="min_stock"
                       value="<?= $inventory->min_stock ?? 10 ?>"
                       class="form-control">
            </div>
        </div>
    </div>

    <!-- UNIT PRICE -->
    <div class="mb-3">
        <label class="form-label">Cost Price</label>
        <input type="number" step="0.01" name="cost_price"
               value="<?= htmlspecialchars($inventory->cost_price ?? '') ?>"
               class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Price per Base Unit</label>
        <input type="number" step="0.01" name="price_per_base"
               value="<?= htmlspecialchars($inventory->price_per_base ?? '') ?>"
               class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Price per Sale Unit</label>
        <input type="number" step="0.01" name="price_per_sale"
               value="<?= htmlspecialchars($inventory->price_per_sale ?? '') ?>"
               class="form-control">
    </div>

    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Update Item
    </button>

    <a href="<?= URLROOT ?>/inventory" class="btn btn-secondary">Cancel</a>
</form>

<?php else: ?>
<div class="alert alert-danger">
    <h4>Item Not Found!</h4>
    <a href="<?= URLROOT ?>/inventory" class="btn btn-primary">Back to Inventory</a>
</div>
<?php endif; ?>