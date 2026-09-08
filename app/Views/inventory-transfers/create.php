<?php if (!empty($_SESSION['error'])) : ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<h2>
    Transfer Inventory
</h2>
<!-- bootstrap error notification container -->
<div id="jsNotification"></div>
<form method="POST">
    <div class="mb-3">
        <label>Scan / Enter SKU or Barcode</label>
        <input type="text"
            id="skuInput"
            class="form-control"
            placeholder="Scan barcode or type SKU...">
        <small class="text-muted">
            You can also select manually below
        </small>
    </div>
    <div class="mb-3">
        <label>Item</label>
        <select name="inventory_id" id="inventorySelect" class="form-select" required>
            <option value="">
                Select Item
            </option>
            <?php foreach ($inventory as $item) : ?>
                <option value="<?= $item->id ?>"
                    data-sku="<?= htmlspecialchars($item->sku) ?>">
                    <?= htmlspecialchars($item->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row">

        <div class="col-md-6 mb-3">

            <label>From Location</label>

            <select name="from_location_id"
                id="fromLocation"
                class="form-select"
                required>

                <option value="">
                    Select Source
                </option>

                <?php foreach ($locations as $loc): ?>

                    <option value="<?= $loc->id ?>">

                        <?= htmlspecialchars($loc->code) ?>
                        -
                        <?= htmlspecialchars($loc->name) ?>

                    </option>

                <?php endforeach; ?>

            </select>

            <div class="alert alert-info d-none mt-2" id="stockInfo">
    <strong>Available Qty:</strong>
    <span id="availableQty">0</span>
</div>

        </div>

        <div class="col-md-6 mb-3">

            <label>To Location</label>

            <select name="to_location_id"  id="toLocation" class="form-select" required>

                <option value="">
                    Select Destination
                </option>

                <?php foreach ($locations as $loc) : ?>

                    <option value="<?= $loc->id ?>">

                        <?= htmlspecialchars($loc->code) ?>
                        -
                        <?= htmlspecialchars($loc->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-3">

        <label>Quantity</label>

        <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>

    </div>

    <div class="mb-3">

        <label>Reference</label>

        <input type="text" name="reference" class="form-control">

    </div>

    <div class="mb-3">

        <label>Notes</label>

        <textarea name="notes" class="form-control"></textarea>

    </div>

    <button class="btn btn-primary">

        Transfer

    </button>

</form>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const inventorySelect = document.getElementById('inventorySelect');
    const fromLocation = document.getElementById('fromLocation');
    const skuInput = document.getElementById('skuInput');

    const stockInfo = document.getElementById('stockInfo');
    const availableQty = document.getElementById('availableQty');

    // -----------------------------
    // STOCK BY LOCATION
    // -----------------------------
 function loadStock() {

const inventory_id = inventorySelect.value;
const location_id = fromLocation.value;

if (!inventory_id || !location_id) {
    stockInfo.classList.add('d-none');
    availableQty.textContent = 0;
    return;
}

fetch('<?= URLROOT ?>/inventorytransfers/getLocationStock', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'inventory_id=' + inventory_id +
          '&location_id=' + location_id
})
.then(res => res.json())
.then(data => {

    stockInfo.classList.remove('d-none');

    const qty = parseFloat(data.quantity ?? 0);

    availableQty.textContent = qty;

    stockInfo.classList.remove('alert-info', 'alert-danger', 'alert-warning');

    if (qty <= 0) {
        stockInfo.classList.add('alert-danger');
    } else if (qty < 10) {
        stockInfo.classList.add('alert-warning');
    } else {
        stockInfo.classList.add('alert-info');
    }
    
})
.catch(err => {
    console.error('Stock load error:', err);
    stockInfo.classList.add('d-none');
});

}

    // -----------------------------
    // LOAD LOCATIONS FOR ITEM
    // -----------------------------
 function loadLocations(itemId) {

    fetch('<?= URLROOT ?>/inventorytransfers/getItemLocations', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'inventory_id=' + itemId
    })
    .then(res => res.json())
    .then(locations => {

        fromLocation.innerHTML = '<option value="">Select Source</option>';

        locations.forEach(loc => {
            fromLocation.innerHTML += `
                <option value="${loc.location_id}">
                    ${loc.code} - ${loc.name} (${loc.quantity})
                </option>
            `;
        });

        // 👇 auto-select first location (important fix)
        if (locations.length > 0) {
            fromLocation.value = locations[0].location_id;
            loadStock(); 
        }
    });
}

    // -----------------------------
    // SKU SEARCH
    // -----------------------------
    let typingTimer;

    skuInput.addEventListener('input', function() {

        clearTimeout(typingTimer);

        const value = this.value.trim();

        if (value.length < 2) return;

        typingTimer = setTimeout(() => {

            fetch('<?= URLROOT ?>/inventorytransfers/getBySku', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'value=' + encodeURIComponent(value)
            })
            .then(res => res.text())
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Invalid JSON:", text);
                    return null;
                }
            })
            .then(item => {

                if (!item || !item.id) {
                    inventorySelect.value = '';
                    inventorySelect.dispatchEvent(new Event('change'));
                    return;
                }

                inventorySelect.value = item.id;
                inventorySelect.dispatchEvent(new Event('change'));
            });

        }, 300);
    });

    // -----------------------------
    // MAIN CHANGE HANDLER
    // -----------------------------
  inventorySelect.addEventListener('change', function() {

    loadLocations(this.value);

    // reset stock display on item change
    stockInfo.classList.add('d-none');
    availableQty.textContent = 0;

    if (this.value && fromLocation.value) {
        loadStock();
    }
});

// Prevent same source/destination
const form = document.querySelector('form');
const toLocation = document.getElementById('toLocation');

form.addEventListener('submit', function(e) {

    if (fromLocation.value === toLocation.value) {

        e.preventDefault();

        showNotification(
            'Source and destination locations cannot be the same.',
            'danger'
        );

        return;
    }
});

    fromLocation.addEventListener('change', loadStock);

});
function showNotification(message, type = 'danger') {

    const container = document.getElementById('jsNotification');

    container.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
}
</script>