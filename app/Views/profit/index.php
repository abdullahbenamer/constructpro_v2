<h1>🔷 Summary Cards</h1>
<div class="row mb-4">

    <div class="col-md-3">
        <div class="card p-3">
            <h6>Today Profit</h6>
            <h4 class="text-success">
                <?= number_format($data['today']->profit ?? 0, 2) ?>
            </h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3">
            <h6>Total Revenue</h6>
            <h4 class="text-primary">
                <?= number_format($data['summary']->revenue ?? 0, 2) ?>
            </h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3">
            <h6>Total Cost</h6>
            <h4 class="text-danger">
                <?= number_format($data['summary']->cost ?? 0, 2) ?>
            </h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3">
            <h6>Total Profit</h6>
            <h4 class="text-success">
                <?= number_format($data['summary']->profit ?? 0, 2) ?>
            </h4>
        </div>
    </div>

</div>

<h1>📊 Daily Table</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Revenue</th>
            <th>Cost</th>
            <th>Profit</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['daily'] as $row): ?>
        <tr>
            <td><?= $row->date ?></td>
            <td><?= number_format($row->revenue, 2) ?></td>
            <td><?= number_format($row->cost, 2) ?></td>
            <td class="<?= $row->profit >= 0 ? 'text-success' : 'text-danger' ?>">
                <?= number_format($row->profit, 2) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h1>🏆 Top Products</h1>
<h5>Top Profitable Products</h5>

<ul class="list-group">
<?php foreach ($data['top'] as $p): ?>
    <li class="list-group-item d-flex justify-content-between">
    <?= htmlspecialchars($p->name) ?> - (units Sold: <?= $p->qty_sold ?>)
        <span class="text-success"><?= number_format($p->profit, 2) ?></span>
    </li>
<?php endforeach; ?>
</ul>


<h1>⚠️ Bottom Products!</h1>
<h5 class="mt-4 text-danger">⚠️ Loss-Making Products</h5>

<?php if (!empty($data['loss'])): ?>

<ul class="list-group">

<?php foreach ($data['loss'] as $p): ?>

    <li class="list-group-item d-flex justify-content-between">

        <div>
            <strong><?= htmlspecialchars($p->name) ?></strong><br>
            <small>
                Sold: <?= $p->qty_sold ?> |
                Revenue: <?= number_format($p->revenue, 2) ?> |
                Cost: <?= number_format($p->cost, 2) ?>
            </small>
        </div>

        <span class="text-danger">
            <?= number_format($p->profit, 2) ?>
        </span>

    </li>

<?php endforeach; ?>

</ul>

<?php else: ?>

<div class="alert alert-success">
    ✅ No loss products — good job!
</div>

<?php endif; ?><h1>Loss Products</h1>