<h2>
    <i class="fas fa-truck"></i>
    Add Supplier
</h2>

<form method="POST">

    <div class="row">

        <div class="col-md-6 mb-3">
            <label class="form-label">
                Company Name *
            </label>

            <input type="text"
                   name="company_name"
                   class="form-control"
                   required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">
                Contact Person
            </label>

            <input type="text"
                   name="contact_person"
                   class="form-control">
        </div>

    </div>

    <div class="row">

        <div class="col-md-4 mb-3">
            <label class="form-label">
                Phone
            </label>

            <input type="text"
                   name="phone"
                   class="form-control">
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">
                Email
            </label>

            <input type="email"
                   name="email"
                   class="form-control">
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">
                Address
            </label>

            <input type="text"
                   name="address"
                   class="form-control">
        </div>

    </div>

    <div class="mb-3">
        <label class="form-label">
            Notes
        </label>

        <textarea name="notes"
                  class="form-control"
                  rows="4"></textarea>
    </div>

    <button class="btn btn-success">

        <i class="fas fa-save"></i>
        Save Supplier

    </button>

    <a href="<?= URLROOT ?>/suppliers"
       class="btn btn-secondary">

        Cancel

    </a>

</form>