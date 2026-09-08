<script>
// cost-type.js

document.addEventListener('DOMContentLoaded', function () {

    const costTypeSelect = document.querySelector('[name="cost_type"]');

    if (!costTypeSelect) return;

    costTypeSelect.addEventListener('change', function () {

        const inventoryField = document.querySelector('[name="inventory_id"]')
            ?.closest('.col-md-4');

        if (!inventoryField) return;

        if (this.value === 'materials') {
            inventoryField.style.display = 'block';
        } else {
            inventoryField.style.display = 'none';
        }
    });

});

</script>