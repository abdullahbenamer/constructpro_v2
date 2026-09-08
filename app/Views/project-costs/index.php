<!-- =========================================================
     PROJECT WORKSPACE HEADER
========================================================= -->

<div class="card shadow-sm mb-3">

    <div class="card-body">

        <!-- PROJECT IDENTITY -->
        <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">

            <div>
                <div class="text-muted small">
                    PROJECT
                </div>

                <h3 class="mb-1 text-uppercase">
                    <?= htmlspecialchars($project->title) ?>
                </h3>

                <div>
                    <span class="badge bg-primary fs-6">
                        <?= htmlspecialchars($project->project_code ?? 'N/A') ?>
                    </span>

                    <span class="badge bg-secondary fs-6">
                        <?= htmlspecialchars(ucfirst($project->project_type ?? 'N/A')) ?>
                    </span>

                    <?php
                    $statusColors = [
                        'planning'    => 'secondary',
                        'in_progress' => 'warning',
                        'testing'     => 'info',
                        'completed'   => 'success',
                        'cancelled'   => 'danger'
                    ];

                    $statusColor =
                        $statusColors[$project->status ?? '']
                        ?? 'secondary';
                    ?>

                    <span class="badge bg-<?= $statusColor ?> fs-6">
                        <?= ucwords(str_replace('_', ' ', $project->status ?? 'N/A')) ?>
                    </span>
                </div>
            </div>

            <div class="mt-2 mt-md-0">

                <a href="<?= URLROOT ?>/projects/edit/<?= $project->id ?>"
                   class="btn btn-warning">
                    <i class="fas fa-edit"></i>
                    Edit Project
                </a>

                <a href="<?= URLROOT ?>/customers/details/<?= $project->customer_id ?>"
                   class="btn btn-outline-primary">
                    <i class="fas fa-address-card"></i>
                    Customer
                </a>

            </div>

        </div>


        <!-- PROJECT SPECIFICATIONS -->
        <div class="border rounded p-3 bg-light">

            <h6 class="mb-3">
                <i class="fas fa-info-circle"></i>
                PROJECT SPECIFICATIONS
            </h6>

            <div class="row g-3">

                <!-- PROJECT CODE -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Project Code
                    </div>

                    <strong>
                        <?= htmlspecialchars($project->project_code ?? 'N/A') ?>
                    </strong>
                </div>


                <!-- PROJECT TYPE -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Project Type
                    </div>

                    <strong>
                        <?= htmlspecialchars(
                            $project->project_type
                            ? ucfirst($project->project_type)
                            : 'N/A'
                        ) ?>
                    </strong>
                </div>


                <!-- CUSTOMER -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Customer
                    </div>

                    <strong>
                        <?= htmlspecialchars($project->customer_name ?? 'N/A') ?>
                    </strong>
                </div>


                <!-- CONTRACT NUMBER -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Contract Number
                    </div>

                    <strong>
                        <?= htmlspecialchars($project->contract_number ?? 'N/A') ?>
                    </strong>
                </div>


                <!-- PROJECT MANAGER -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Project Manager
                    </div>

                    <strong>
                        <?= htmlspecialchars(
                            $project->project_manager_name
                            ?? 'N/A'
                        ) ?>
                    </strong>
                </div>


                <!-- PRIORITY -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Priority
                    </div>

                    <strong>
                        <?= htmlspecialchars(
                            ucfirst($project->priority ?? 'N/A')
                        ) ?>
                    </strong>
                </div>


                <!-- START DATE -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Start Date
                    </div>

                    <strong>
                        <?= !empty($project->start_date)
                            ? date('d M Y', strtotime($project->start_date))
                            : 'N/A'
                        ?>
                    </strong>
                </div>


                <!-- DEADLINE -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Deadline
                    </div>

                    <strong>
                        <?= !empty($project->deadline)
                            ? date('d M Y', strtotime($project->deadline))
                            : 'N/A'
                        ?>
                    </strong>
                </div>


                <!-- SITE LOCATION -->
                <div class="col-md-6">
                    <div class="text-muted small">
                        Site Location
                    </div>

                    <strong>
                        <?= htmlspecialchars(
                            $project->site_location ?? 'N/A'
                        ) ?>
                    </strong>
                </div>


                <!-- PROJECT WAREHOUSE -->
                <div class="col-md-3">
                    <div class="text-muted small">
                        Project Warehouse
                    </div>

                    <strong>
                        <?= htmlspecialchars(
                            $project->project_code ?? 'N/A'
                        ) ?>
                    </strong>
                </div>


                <!-- BUDGET -->
               <div class="col-md-3">
    <div class="text-muted small">Project Budget</div>
    <strong>
        <?= number_format((float)($project->budget ?? 0), 2) ?> LYD
    </strong>

    <div class="text-muted small mt-3">Project Costs</div>
    <strong>
        <?= number_format((float)($total_cost ?? 0), 2) ?> LYD
    </strong>
</div>

            </div>


            <!-- PROJECT SCOPE -->
            <div class="mt-4">

                <div class="text-muted small mb-2">
                    Project Scope
                </div>

                <?php if (!empty($project_scopes)): ?>

                    <?php foreach ($project_scopes as $scope): ?>

                        <span class="badge bg-dark me-1 mb-1">
                            <?= htmlspecialchars($scope->scope) ?>
                        </span>

                    <?php endforeach; ?>

                <?php else: ?>

                    <span class="text-muted">
                        No project scope specified.
                    </span>

                <?php endif; ?>

            </div>


            <!-- DESCRIPTION -->
            <?php if (!empty($project->description)): ?>

                <div class="mt-4">

                    <div class="text-muted small mb-1">
                        Description
                    </div>

                    <div>
                        <?= nl2br(
                            htmlspecialchars($project->description)
                        ) ?>
                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<!-- =========================================================
     PROJECT NAVIGATION
========================================================= -->

<div class="d-flex flex-wrap gap-2 mb-3">

    <a href="<?= URLROOT ?>/projects"
       class="btn btn-success">
        <i class="fas fa-arrow-left"></i>
        Projects
    </a>

    <a href="<?= URLROOT ?>/projects/documents/<?= $project->id ?>"
       class="btn btn-secondary">
        <i class="fas fa-folder-open"></i>
        Documents
    </a>

    <a href="<?= URLROOT ?>/projectcosts/finance/<?= $project->id ?>"
       class="btn btn-primary">
        <i class="fas fa-money-check-dollar"></i>
        Advance Payment
    </a>

    <a href="<?= URLROOT ?>/projectcosts/ledger/<?= $project->id ?>"
       class="btn btn-secondary">
        <i class="fas fa-book"></i>
        Finance Ledger
    </a>

    <a href="<?= URLROOT ?>/projectcosts/financeDashboard/<?= $project->id ?>"
       class="btn btn-info">
        <i class="fas fa-chart-line"></i>
        Finance Dashboard
    </a>

</div>


<!-- =========================================================
     PROJECT COSTS TITLE
========================================================= -->

<?php if (!empty($costs)): ?>

    <!-- PROJECT COSTS TABLE -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>DATE</th>
                    <th>COST TYPE</th>
                    <th>DESCRIPTION</th>
                    <th class="text-end">QUANTITY</th>
                    <th class="text-end">UNIT PRICE</th>
                    <th class="text-end">TOTAL</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($costs as $index => $cost): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>

                        <td>
                            <?= !empty($cost->created_at)
                                ? date('d M Y', strtotime($cost->created_at))
                                : 'N/A' ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($cost->cost_type ?? 'N/A') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($cost->description ?? '') ?>
                        </td>

                        <td class="text-end">
                            <?= number_format((float)($cost->quantity ?? 0), 2) ?>
                        </td>

                        <td class="text-end">
                            <?= number_format((float)($cost->unit_price ?? 0), 2) ?>
                            LYD
                        </td>

                        <td class="text-end fw-bold">
                            <?= number_format((float)($cost->total_cost ?? 0), 2) ?>
                            LYD
                        </td>

                        <td>
    <a href="<?= URLROOT ?>/project-costs/edit/<?= $cost->id ?>/<?= $project_id ?>"
       class="btn btn-sm btn-warning">
        <i class="fas fa-edit"></i>
        Edit
    </a>

    <a href="<?= URLROOT ?>/project-costs/delete/<?= $cost->id ?>"
       class="btn btn-sm btn-danger"
       onclick="return confirm('Delete this cost?')">
        <i class="fas fa-trash"></i>
        Delete
    </a>
</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr class="table-light">
                    <th colspan="6" class="text-end">
                        TOTAL PROJECT COST
                    </th>
                    <th class="text-end">
                        <?= number_format((float)($total_cost ?? 0), 2) ?> LYD
                    </th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

<?php else: ?>

    <!-- EMPTY PROJECT COSTS STATE -->
    <div class="card border-0 bg-light mb-4">
        <div class="card-body text-center py-5">

            <div class="mb-3">
                <i class="fas fa-coins fa-3x text-muted"></i>
            </div>

            <h5 class="mb-2">
                NO PROJECT COSTS RECORDED
            </h5>

            <p class="text-muted mb-4">
                No costs have been recorded for this project yet.
                Start by adding the first project cost.
            </p>

            <a href="<?= URLROOT ?>/project-costs/create/<?= $project->id ?>"
               class="btn btn-primary">
                <i class="fas fa-plus"></i>
                ADD FIRST COST
            </a>

        </div>
    </div>

<?php endif; ?>