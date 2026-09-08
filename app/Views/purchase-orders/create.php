<h2>Create Purchase Order</h2>

<form method="POST">

    <div class="mb-3">

        <label>Supplier</label>

        <select name="supplier_id"
                class="form-select"
                required>

            <option value="">Select Supplier</option>

            <?php foreach ($suppliers as $supplier): ?>

                <option value="<?= $supplier->id ?>">

                    <?= htmlspecialchars($supplier->company_name) ?>

                </option>

            <?php endforeach; ?>

        </select>

            </div>

    <div class="mb-3">

        <label>Order Date</label>

        <input type="date"
               name="order_date"
               class="form-control"
               required>

    </div>

    <div class="mb-3">

        <label>Expected Date</label>

        <input type="date"
               name="expected_date"
               class="form-control">

    </div>

    <div class="mb-3">

        <label>Notes</label>

        <textarea name="notes"
                  class="form-control"></textarea>

    </div>

    <button class="btn btn-success">

        Create Purchase Order

    </button>

</form>