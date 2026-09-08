<h2>
    Location:
    <?= htmlspecialchars($location->code) ?>
</h2>

<p>
    <?= htmlspecialchars($location->name) ?>
</p>

<!-- sort and filter -->
 <div class="row mb-3">

    <div class="col-md-3">
        <select id="stockFilter" class="form-select">
            <option value="">All Items</option>
            <option value="low">Low Stock</option>
            <option value="out">Out Of Stock</option>
        </select>
    </div>

</div>
<!-- Search -->
<div class="row mb-3">
    <div class="col-md-4">
        <input type="text"
               id="searchInput"
               class="form-control"
               placeholder="Search Item or SKU...">
    </div>
</div>
<table class="table table-bordered table-striped">

    <thead>
    <tr>
        <th>Item</th>
        <th>SKU</th>
        <th class="text-end">Physical Qty</th>
        <th class="text-end">Reserved Qty</th>
        <th class="text-end">Available Qty</th>
        <th>Status</th>
    </tr>
</thead>

<tbody>

<?php foreach ($items as $item): ?>

    <?php

        // Status should be based on AVAILABLE quantity,
        // not the physical quantity.

        $stockStatus =
            ($item->available_quantity <= 0)
                ? 'out'
                : (($item->available_quantity <= ($item->min_stock ?? 0))
                    ? 'low'
                    : 'ok');

    ?>

    <tr
        data-name="<?= strtolower($item->name) ?>"
        data-sku="<?= strtolower($item->sku) ?>"
        data-stock="<?= $stockStatus ?>"
    >

        <!-- ITEM -->

        <td>
            <?= htmlspecialchars($item->name) ?>
        </td>

        <!-- SKU -->

        <td>
            <?= htmlspecialchars($item->sku) ?>
        </td>

        <!-- PHYSICAL QUANTITY -->

        <td class="text-end">

            <?= number_format(
                (float) $item->quantity,
                2
            ) ?>

            <?= htmlspecialchars(
                $item->base_unit
            ) ?>

        </td>

        <!-- RESERVED QUANTITY -->

        <td class="text-end text-warning fw-bold">

            <?= number_format(
                (float) $item->reserved_quantity,
                2
            ) ?>

        </td>

        <!-- AVAILABLE QUANTITY -->

        <td class="text-end text-success fw-bold">

            <?= number_format(
                (float) $item->available_quantity,
                2
            ) ?>

        </td>

        <!-- STATUS -->

        <td>

            <?php

                if ($stockStatus === 'out') {

                    echo '<span class="badge bg-danger">
                            Fully Reserved / Out of Stock
                          </span>';

                } elseif ($stockStatus === 'low') {

                    echo '<span class="badge bg-warning text-dark">
                            Low Available Stock
                          </span>';

                } else {

                    echo '<span class="badge bg-success">
                            Available
                          </span>';

                }

            ?>

        </td>

    </tr>

<?php endforeach; ?>

</tbody>

   

</table>

<script>
    // Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {

    const searchInput =
        document.getElementById('searchInput');

    const stockFilter =
        document.getElementById('stockFilter');

    function filterRows() {

        const search =
            searchInput.value.toLowerCase();

        const filter =
            stockFilter.value;

        document.querySelectorAll('tbody tr')
            .forEach(row => {

            const name = (row.dataset.name || '');
const sku  = (row.dataset.sku || '');

                const stock =
                    row.dataset.stock;

                const matchesSearch =
                    name.includes(search) ||
                    sku.includes(search);

                const matchesFilter =
                    !filter ||
                    stock === filter;

                row.style.display =
                    (matchesSearch && matchesFilter)
                    ? ''
                    : 'none';
            });
    }

    searchInput.addEventListener(
        'keyup',
        filterRows
    );

    stockFilter.addEventListener(
        'change',
        filterRows
    );

});
</script>