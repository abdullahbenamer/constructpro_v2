<script>
// inventory.js

document.addEventListener('DOMContentLoaded', function () {

    // AUTO-FILL UNIT PRICE
    const inventorySelect = document.querySelector('[name="inventory_id"]');
    if (inventorySelect) {
        inventorySelect.addEventListener('change', function () {
            let selected = this.options[this.selectedIndex];
            let price = selected.getAttribute('data-price');

            if (price) {
                document.querySelector('[name="unit_price"]').value = price;
            }
        });
    }

    // AUTO-FILL DESCRIPTION + PRICE (if inventorySelect2 exists)
    const inventorySelect2 = document.getElementById('inventorySelect');
    if (inventorySelect2) {
        inventorySelect2.addEventListener('change', function () {
            let selected = this.options[this.selectedIndex];

            let name = selected.text;
            let price = selected.getAttribute('data-price');

            const desc = document.querySelector('[name="description"]');
            const priceField = document.querySelector('[name="unit_price"]');

            if (desc) desc.value = name;
            if (priceField && price) priceField.value = price;
        });
    }

});

</script>