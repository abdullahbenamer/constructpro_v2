<h3>Project - <?= htmlspecialchars($project->title) ?></h3>
<br>
<h4>Finance Dashboard</h4>
<br>
<div class="row g-3">

    <!-- Advances -->
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Total Advances</h6>
                <h3><?= number_format($summary['advances'], 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- Costs -->
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6>Total Costs</h6>
                <h3><?= number_format($summary['costs'], 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- Balance -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Available Balance</h6>
                <h3><?= number_format($summary['balance'], 2) ?></h3>
            </div>
        </div>
    </div>

    <!-- Budget -->
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Project Budget</h6>
                <h3><?= number_format($project->budget, 2) ?></h3>
            </div>
        </div>
    </div>

</div>
<!-- --------------------------------- -->
 <div class="row mt-3">

    <div class="col-md-6">

        <div class="card">

            <div class="card-body">

                <h6>Budget Utilization</h6>

                <div class="progress" style="height:25px;">
                    <div class="progress-bar"
                         role="progressbar"
                         style="width: <?= min(100, $summary['budget_used']) ?>%">
                        <?= number_format($summary['budget_used'],1) ?>%
                    </div>
                </div>
            </div>         
        </div>
        <br>
<div class="card">

            <div class="card-body">

                <h6>Project Timeline Utilization</h6>

        <div class="progress" style="height:25px;">

            <div class="progress-bar bg-warning"
                 role="progressbar"
                 style="width: <?= $summary['timeline_used'] ?>%">

                <?= number_format($summary['timeline_used'],1) ?>%

            </div>

        </div>

            </div>

        </div>
    </div>

    <div class="col-md-6">

        <div class="card">

            <div class="card-body">

                <h6>Remaining Budget</h6>

                <h3>
                    <?= number_format($summary['budget_remaining'],2) ?>
                </h3>

            </div>
        </div>
    </div>



<div class="card mt-3">

    <div class="card-body">

        <h6>Advance Funding vs Budget</h6>

        <div class="d-flex justify-content-between mb-2">

            <span>
                Advances:
                <strong>
                    <?= number_format($summary['advances'], 2) ?>
                </strong>
            </span>

            <span>
                Budget:
                <strong>
                    <?= number_format($project->budget, 2) ?>
                </strong>
            </span>

        </div>

      <div class="progress" style="height:25px;">
    <div class="progress-bar bg-success"
         role="progressbar"
         style="width: <?= min(100, $summary['advance_funding']) ?>%;">
        <?= number_format($summary['advance_funding'], 1) ?>%
    </div>
</div>

        <small class="text-muted">

            Percentage of project budget funded by received advances.

        </small>

    </div>

</div>




</div>
<br>
<!-- --------------------------- -->
 <?php if ($summary['balance'] < 0): ?>
<br>
<div class="alert alert-danger">
    <i class="fas fa-triangle-exclamation"></i>
    Project costs exceeded available advances.
</div>

<?php endif; ?>

<?php if ($summary['budget_used'] > 90): ?>

<div class="alert alert-warning">
    <i class="fas fa-exclamation-circle"></i>
    Project has consumed more than 90% of its budget.
</div>

<?php endif; ?>