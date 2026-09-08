<h2>
    Create Supplier Quotation
</h2>

<form method="POST">

    <div class="row">

        <div class="col-md-4 mb-3">

            <label class="form-label">
                Supplier *
            </label>

            <select name="supplier_id"
                    class="form-select"
                    required>

                <option value="">
                    Select Supplier
                </option>

                <?php foreach ($suppliers as $supplier): ?>

                    <option value="<?= $supplier->id ?>">

                        <?= htmlspecialchars(
                            $supplier->company_name
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="col-md-4 mb-3">

            <label class="form-label">
                Supplier Quotation No.
            </label>

            <input type="text"
                   name="supplier_reference"
                   class="form-control">

        </div>

        <div class="col-md-4 mb-3">

            <label class="form-label">
                Procurement Reference
            </label>

            <input type="text"
                   name="procurement_reference"
                   class="form-control"
                   placeholder="e.g. PR-2026-001">

            <small class="text-muted">
                Use the same reference for quotations being compared for the same requirement.
            </small>

        </div>

    </div>


    <div class="row">

        <div class="col-md-4 mb-3">

            <label class="form-label">
                Quotation Date *
            </label>

            <input type="date"
                   name="quotation_date"
                   class="form-control"
                   value="<?= date('Y-m-d') ?>"
                   required>

        </div>

        <div class="col-md-4 mb-3">

            <label class="form-label">
                Valid Until
            </label>

            <input type="date"
                   name="valid_until"
                   class="form-control">

        </div>

        <div class="col-md-4 mb-3">

            <label class="form-label">
                Required Delivery Date
            </label>

            <input type="date"
                   name="required_delivery_date"
                   class="form-control">

        </div>

    </div>


    <div class="row">

        <div class="col-md-4 mb-3">

            <label class="form-label">
                Supplier Promised Delivery Date
            </label>

            <input type="date"
                   name="promised_delivery_date"
                   class="form-control">

        </div>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Procurement / Evaluation Notes
        </label>

        <textarea name="evaluation_notes"
                  class="form-control"
                  rows="3"
                  placeholder="General procurement evaluation, commercial observations, supplier experience, etc."></textarea>

    </div>


    <div class="mb-3">

        <label class="form-label">
            Notes
        </label>

        <textarea name="notes"
                  class="form-control"
                  rows="3"></textarea>

    </div>


    <button class="btn btn-success">

        <i class="fas fa-save"></i>
        Create Quotation

    </button>

    <a href="<?= URLROOT ?>/supplierquotations"
       class="btn btn-secondary">

        Cancel

    </a>

</form>