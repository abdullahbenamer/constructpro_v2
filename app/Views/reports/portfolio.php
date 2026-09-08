<div class="row mb-4">

    <div class="col-md-2">
        <div class="card border-primary">
            <div class="card-body">
                <h6>Total Projects</h6>
                <h3><?= $portfolio->total_projects ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-success">
            <div class="card-body">
                <h6>Active</h6>
                <h3><?= $portfolio->active_projects ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-secondary">
            <div class="card-body">
                <h6>Archived</h6>
                <h3><?= $portfolio->archived_projects ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-info">
            <div class="card-body">
                <h6>Completed</h6>
                <h3><?= $portfolio->completed_projects ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-warning">
            <div class="card-body">
                <h6>Due Soon</h6>
                <h3><?= $deadlines->due_soon ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-danger">
            <div class="card-body">
                <h6>Overdue</h6>
                <h3><?= $deadlines->overdue ?></h3>
            </div>
        </div>
    </div>

</div>