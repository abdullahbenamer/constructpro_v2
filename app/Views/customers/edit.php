<?php if (isset($customer) && $customer): ?>
<h2><i class="fas fa-user-edit"></i> Edit Customer #<?= $customer->id ?></h2>

<form method="POST">

    <!-- NAME -->
    <div class="mb-3">
        <label class="form-label">Contact Name *</label>
        <input type="text" name="name"
               value="<?= htmlspecialchars($customer->name ?? '') ?>"
               class="form-control" required>
    </div>

    <!-- COMPANY -->
    <div class="mb-3">
        <label class="form-label">Company</label>
        <input type="text" name="company"
               value="<?= htmlspecialchars($customer->company ?? '') ?>"
               class="form-control">
    </div>

    <!-- EMAIL -->
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($customer->email ?? '') ?>"
               class="form-control">
    </div>

    <!-- PHONE -->
    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone"
               value="<?= htmlspecialchars($customer->phone ?? '') ?>"
               class="form-control">
    </div>

    <!-- ADDRESS -->
    <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($customer->address ?? '') ?></textarea>
    </div>

    <!-- STATUS -->
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" <?= ($customer->status ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= ($customer->status ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
    </div>

    <!-- CREATED AT (READ-ONLY) -->
    <div class="mb-3">
        <label class="form-label">Created At</label>
        <input type="text"
               value="<?= htmlspecialchars($customer->created_at ?? '') ?>"
               class="form-control" readonly>
    </div>

    <!-- ACTIONS -->
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Update Customer
    </button>

    <a href="<?= URLROOT ?>/customers" class="btn btn-secondary">
        Cancel
    </a>

</form>

<?php else: ?>
<div class="alert alert-danger">
    <h4>Customer Not Found!</h4>
    <p>The customer record no longer exists.</p>
    <a href="<?= URLROOT ?>/customers" class="btn btn-primary">Back to Customers</a>
</div>
<?php endif; ?>