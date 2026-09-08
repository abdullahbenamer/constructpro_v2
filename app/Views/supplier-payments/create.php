<div class="container mt-4">

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                Record Supplier Payment
            </h5>
        </div>

        <div class="card-body">

            <h5 class="mb-3">
                <?= htmlspecialchars($supplier->company_name) ?>
            </h5>

            <form method="POST">

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label>Date</label>
                        <input type="date" name="payment_date"
                               class="form-control"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Amount</label>
                        <input type="number" step="0.01"
                               name="amount"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Method</label>
                        <select name="method" class="form-control">
                            <option>Cash</option>
                            <option>Bank Transfer</option>
                            <option>Cheque</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Reference</label>
                        <input type="text" name="reference"
                               class="form-control">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Notes</label>
                        <textarea name="notes"
                                  class="form-control"></textarea>
                    </div>

                </div>

                <button class="btn btn-success">
                    Save Payment
                </button>

                <a href="<?= URLROOT ?>/suppliers/info/<?= $supplier->id ?>"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>
</div>