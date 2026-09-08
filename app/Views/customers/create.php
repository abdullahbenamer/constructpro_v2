<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-user-plus"></i> Add New Customer</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Contact Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="Eng. Ahmed Al-Mansour">
            </div>
            <div class="mb-3">
                <label class="form-label">Company <span class="text-danger">*</span></label>
                <input type="text" name="company" class="form-control" required placeholder="Libya Power Systems">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="ahmed@lps.ly">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required placeholder="0912345678">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Tripoli Industrial Zone"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Add Customer
            </button>
            <a href="<?= URLROOT ?>/customers" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>