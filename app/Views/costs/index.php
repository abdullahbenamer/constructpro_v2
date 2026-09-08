<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-coins"></i> All Project Costs</h2>
        <div class="card">
            <div class="card-body">
                <h4>Total Costs: $<?= number_format($total_costs, 2) ?></h4>
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Proj ID</th>
                        <th>Project</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_costs as $cost) : ?>
                        <tr>
                            <td>Prj-<?= htmlspecialchars($cost->project_id) ?>
                                <a class="btn btn-sm btn-info"
                                    href="<?= URLROOT ?>/project-costs/<?= $cost->project_id ?>">Details
                                </a>
                            </td>

                            <td><a href="<?= URLROOT ?>/projects/<?= $cost->project_id ?>"><?= htmlspecialchars($cost->project_title) ?></a></td>

                            <td><span class="badge bg-info"><?= ucfirst($cost->cost_type) ?></span></td>
                            <td> <?= htmlspecialchars($cost->item_name ?? $cost->description) ?></td>
                            <td><?= $cost->quantity ?></td>
                            <td>$<?= number_format($cost->unit_price, 2) ?></td>
                            <td><strong>$<?= number_format($cost->quantity * $cost->unit_price, 2) ?></strong></td>
                            <td><?= date('M j', strtotime($cost->created_at)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>