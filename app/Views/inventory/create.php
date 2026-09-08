<?php if (!empty($_SESSION['error'])) : ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (!empty($data['error'])): ?>
    <div class="alert alert-danger">
        <?= $data['error']; ?>
    </div>
<?php endif; ?>

<h2><i class="fas fa-plus"></i> Add Inventory Item</h2>

<form method="POST">

    <!-- BASIC INFO -->
    <div class="row">
        <div class="col-md-6">
            <label class="form-label">Item Name *</label>
            <input type="text" name="name" value="<?= $_SESSION['old']['name'] ?? '' ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label">SKU *</label>
            <input type="text" name="sku" value="<?= $_SESSION['old']['sku'] ?? '' ?>">
        </div>
    </div>

    <!-- BRAND name / Make-->
<div class="row mt-2">
    <div class="col-md-6">
        <label class="form-label">Brand</label>
        <select name="brand_id" class="form-select">
            <option value="">-- Select Brand --</option>
            <?php if (!empty($data['brands'])): ?>
                <?php foreach ($data['brands'] as $brand): ?>
                    <option value="<?= $brand->id ?>"
                        <?= (!empty($_SESSION['old']['brand_id']) && $_SESSION['old']['brand_id'] == $brand->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($brand->brand_name) ?>
                        <?php if (!empty($brand->country)): ?>
                            (<?= htmlspecialchars($brand->country) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
</div>

<!-- COUNTRY (Made In) -->
<div class="col-md-6">
    <label class="form-label">Made In (Country)</label>
    <select name="country_id" class="form-select">
        <option value="">-- Select Country --</option>

        <?php if (!empty($data['countries'])): ?>
            <?php foreach ($data['countries'] as $country): ?>
                <option value="<?= $country->id ?>"
                    <?= (!empty($_SESSION['old']['country_id']) && $_SESSION['old']['country_id'] == $country->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($country->country_name) ?>
                    <?php if (!empty($country->country_code)): ?>
                        (<?= htmlspecialchars($country->country_code) ?>)
                    <?php endif; ?> 
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

    <?php unset($_SESSION['old']); ?>
    <div class="row mt-2">
        <div class="col-md-4">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option>Switchgear</option>
                <option>Component</option>
                <option>Instrumentation</option>
                <option>Protection</option>
                <option>Tools</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" value="0" min="0">
        </div>


        <!-- LOCATION -->

<div class="row mt-3">
    <div class="col-md-6">
        <label class="form-label">Storage Location *</label>

        <select name="location_id" id="location_id" class="form-select" required>
            <option value="">-- Select Location --</option>

            <?php foreach ($data['locations'] as $loc): ?>
               <option value="<?= $loc->id ?>"
    <?= ($loc->id == $data['default_location_id']) ? 'selected' : '' ?>>
    <?= htmlspecialchars($loc->name) ?>
    <?= ($loc->id == $data['default_location_id']) ? '⭐ Default' : '' ?>
</option>
            <?php endforeach; ?>
        </select>

        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="confirm_location">
            <label class="form-check-label">
                I confirm this storage location
            </label>
        </div>
    </div>
</div>
        <!-- ------------------------ -->
        <div class="col-md-4">
            <label class="form-label">Min Stock</label>
            <input type="number" name="min_stock" class="form-control" value="10" min="0">
        </div>
    </div>

    <!-- COST -->
    <div class="row mt-3">
        <div class="col-md-4">
            <label class="form-label">Cost Price *</label>
            <input type="number" name="cost_price" step="0.01" min="0" class="form-control" required>
        </div>
    </div>

    <!-- UNIT SYSTEM -->
    <h5 class="mt-4">Unit Configuration</h5>

    <div class="row">
        <div class="col-md-3">
            <label class="form-label">Base Unit</label>
            <select name="base_unit" class="form-select">
                <option value="piece">Piece</option>
                <option value="meter">Meter</option>
                <option value="kg">Kg</option>
                <option value="liter">Liter</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Allow Fraction</label><br>
            <input type="checkbox" name="allow_fraction" value="1">
            <small class="text-muted">e.g. 2.5 meters</small>
        </div>

        <div class="col-md-3">
            <label class="form-label">Sale Unit</label>
            <input type="text" name="sale_unit" class="form-control" placeholder="Roll / Box">
        </div>

        <div class="col-md-3">
            <label class="form-label">Units per Sale</label>
            <input type="number" name="units_per_sale" class="form-control" value="1" min="1">
        </div>
    </div>

    <!-- PRICING -->
    <h5 class="mt-4">Selling Prices</h5>

    <div class="row">
        <div class="col-md-6">
            <label class="form-label">Price per Base Unit</label>
            <input type="number" name="price_per_base" step="0.01" min="0" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Price per Sale Unit</label>
            <input type="number" name="price_per_sale" step="0.01" min="0" class="form-control">
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-4">
        <i class="fas fa-save"></i> Add Item
    </button>

    <a href="<?= URLROOT ?>/inventory" class="btn btn-secondary mt-4">Cancel</a>

</form>

<!-- ------------ SELECTION MODAL--------------- -->
 <div class="modal fade" id="locationConfirmModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">

      <h5>Confirm Storage Location</h5>

      <p>
        You selected:
        <strong id="selectedLocationText"></strong>
      </p>

      <button type="button" class="btn btn-success" id="confirmBtn">
        Confirm
      </button>

      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Change
      </button>

    </div>
  </div>
</div>
<!-- ------------------------------- -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const locationSelect = document.getElementById('location_id');
    const confirmCheckbox = document.getElementById('confirm_location');
    const modalEl = document.getElementById('locationConfirmModal');

    const modal = new bootstrap.Modal(modalEl);

    confirmCheckbox.addEventListener('change', function () {

        if (locationSelect.value === "") {
            alert("Please select a storage location first");
            this.checked = false;
            return;
        }

        const text = locationSelect.options[locationSelect.selectedIndex].text;
        document.getElementById('selectedLocationText').innerText = text;

        modal.show();
    });

    document.getElementById('confirmBtn').addEventListener('click', function () {
    document.getElementById('confirm_location').dataset.confirmed = "1";

    bootstrap.Modal.getInstance(
        document.getElementById('locationConfirmModal')
    ).hide();
});
});
</script>