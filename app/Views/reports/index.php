<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-chart-line"></i> PROJECT PORTFOLIO DASHBOARD</h2>
    </div>
</div>
<br>
<!-- --------------------------- -->

<div class="row mb-4">

    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <h6>Total Projects</h6>
                <h3><?= $dashboard->total_projects ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <h6>Active</h6>
                <h3><?= $dashboard->active_projects ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card">
            <div class="card-body">
                <h6>Completed</h6>
                <h3><?= $dashboard->completed_projects ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6>Due Soon</h6>
                <h3><?= $dashboard->due_soon_projects ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6>Overdue</h6>
                <h3><?= $dashboard->overdue_projects ?></h3>
            </div>
        </div>
    </div>

</div>

<div class="row mb-4">

    <div class="col-md-3">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <h6>Total Budget</h6>
                <h3><?= number_format($dashboard->total_budget,2) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Total Cash In (Advances)</h5>
                <h3><?= number_format($global_advances, 2) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5>Total Cash Out (Costs)</h5>
                <h3><?= number_format($global_costs, 2) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Net Position</h5>
                <h3><?= number_format($global_balance, 2) ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- --------------------------------- -->
<!-- Cost vs Budget Table -->
<!-- <div class="row mb-4">
    <div class="col-12">
        <h4>Cost vs Budget Analysis</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Projct ID</th>
                        <th>Project</th>
                        <th>Budget</th>
                        <th>Actual Cost</th>
                        <th>Variance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php //foreach ($cost_vs_budget as $report): ?>
                        <tr class="<?//= $report->variance < 0 ? 'table-danger' : 'table-success' ?>">

                         <td>
                                
                        
                                    <?//= "Prj-" . $report->id ?>
                            
                            </td>
                            <td>
                                <a href="<?//= URLROOT ?>/project-costs/financeDashboard/<?//= $report->id ?>"
                                    class="text-decoration-none fw-bold">
                                    <?//= htmlspecialchars($report->title) ?>
                                </a>
                            </td>
                            <td>$<?//= number_format($report->budget, 2) ?></td>
                            <td>$<?//= number_format($report->actual_cost, 2) ?></td>
                            <td>
                                <strong><?//= $report->variance < 0 ? '-' : '' ?>$<?//= number_format(abs($report->variance), 2) ?></strong>
                            </td>
                            <td>
                                <span class="badge bg-<?//= $report->variance >= 0 ? 'success' : 'danger' ?>">
                                    <?//= $report->variance >= 0 ? 'Under Budget' : 'Over Budget' ?>
                                </span>
                            </td>
                        </tr>
                    <?php //endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> -->