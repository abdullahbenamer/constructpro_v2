<h3>Add Project Advance</h3>

<h5>Project: <?= htmlspecialchars($project->title) ?></h5>

<form method="POST">

    <div class="mb-2">
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Payment Method</label>
        <select name="payment_method" class="form-control">
            <option>Cash</option>
            <option>Bank Transfer</option>
            <option>Cheque</option>
        </select>
    </div>

    <div class="mb-2">
        <label>Reference</label>
        <input type="text" name="reference" class="form-control">
    </div>

    <div class="mb-2">
        <label>Date</label>
        <input type="date" name="advance_date" class="form-control" value="<?= date('Y-m-d') ?>">
    </div>

    <div class="mb-2">
        <label>Notes</label>
        <textarea name="notes" class="form-control"></textarea>
    </div>

    <button class="btn btn-success">Save Advance</button>
</form>