<h2>Edit Supplier</h2>

<form method="POST">

    <div class="mb-3">
        <label>Company Name</label>

        <input type="text"
               name="company_name"
               class="form-control"
               value="<?= htmlspecialchars($supplier->company_name) ?>"
               required>
    </div>

    <div class="mb-3">
        <label>Contact Person</label>

        <input type="text"
               name="contact_person"
               class="form-control"
               value="<?= htmlspecialchars($supplier->contact_person) ?>">
    </div>

    <div class="mb-3">
        <label>Phone</label>

        <input type="text"
               name="phone"
               class="form-control"
               value="<?= htmlspecialchars($supplier->phone) ?>">
    </div>

    <div class="mb-3">
        <label>Email</label>

        <input type="email"
               name="email"
               class="form-control"
               value="<?= htmlspecialchars($supplier->email) ?>">
    </div>

    <div class="mb-3">
        <label>Address</label>

        <textarea name="address"
                  class="form-control"><?= htmlspecialchars($supplier->address) ?></textarea>
    </div>

    <div class="mb-3">
        <label>Notes</label>

        <textarea name="notes"
                  class="form-control"><?= htmlspecialchars($supplier->notes) ?></textarea>
    </div>

    <button class="btn btn-success">
        Update Supplier
    </button>

</form>