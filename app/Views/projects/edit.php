<?php

$projectScopes = [];

foreach ($data['project_scopes'] ?? [] as $row) {
    $projectScopes[] = $row->scope;
}

?>

<h2>
    <i class="fas fa-edit"></i>
    Edit Project #<?= $project->id ?>
</h2>

<form method="POST">

<div class="row">

    <!-- CUSTOMER -->
    <div class="col-md-6">
        <label>Customer</label>
        <select name="customer_id" class="form-select" required>
            <?php foreach ($customers as $c): ?>
                <option value="<?= $c->id ?>"
                    <?= $project->customer_id == $c->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c->company) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

 <!-- PROJECT TYPE -->

<div class="col-md-4">

    <label>Project Type</label>

    <select name="project_type"
            id="projectType"
            class="form-select"
            required>

<option value="Construction"
    <?= ($data['project']->project_type ?? '') === 'Construction' ? 'selected' : '' ?>>
    Construction
</option>

<option value="Maintenance"
    <?= ($data['project']->project_type ?? '') === 'Maintenance' ? 'selected' : '' ?>>
    Maintenance
</option>

<option value="Inspection"
    <?= ($data['project']->project_type ?? '') === 'Inspection' ? 'selected' : '' ?>>
    Inspection
</option>

<option value="Consultancy"
    <?= ($data['project']->project_type ?? '') === 'Consultancy' ? 'selected' : '' ?>>
    Consultancy
</option>

<option value="Other"
    <?= ($data['project']->project_type ?? '') === 'Other' ? 'selected' : '' ?>>
    Other
</option>

    </select>

</div>

<!-- PROJECT SCOPE -->

<div class="mb-3">
    <label class="form-label">PROJECT SCOPE</label>

    <?php
    $projectScopes = [];

    foreach ($data['project_scopes'] ?? [] as $row) {
        $projectScopes[] = $row->scope;
    }

    $scopes = [
        'Civil',
        'Architectural',
        'Structural',
        'MEP',
        'Finishing',
        'Instrumentation & Control',
        'Telecommunications',
        'Other'
    ];
    ?>

    <div class="border rounded p-3 bg-light">
        <div class="row g-2">

            <?php foreach ($scopes as $scope): ?>

                <div class="col-md-6">
                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="scopes[]"
                            value="<?= htmlspecialchars($scope) ?>"
                            id="scope_<?= md5($scope) ?>"
                            <?= in_array($scope, $projectScopes, true) ? 'checked' : '' ?>
                        >

                        <label
                            class="form-check-label"
                            for="scope_<?= md5($scope) ?>"
                        >
                            <?= htmlspecialchars($scope) ?>
                        </label>

                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>

    <small class="text-muted">
        Select one or more applicable project scopes.
    </small>
</div>
</div>

<!-- COMMON FIELDS -->
<div class="mt-3">

    <label>Title</label>
    <input type="text" name="title"
           class="form-control"
           value="<?= htmlspecialchars($project->title) ?>">

    <label class="mt-2">Description</label>
    <textarea name="description" class="form-control">
        <?= htmlspecialchars($project->description) ?>
    </textarea>

</div>

<!-- LOCATION / DATES -->
<div class="row mt-3">

    <div class="col-md-4">
        <label>Site Location</label>
        <input type="text" name="site_location"
               class="form-control"
               value="<?= $project->site_location ?>">
    </div>

    <div class="col-md-4">
        <label>Start Date</label>
        <input type="date" name="start_date"
               class="form-control"
               value="<?= $project->start_date ?>">
    </div>

    <div class="col-md-4">
        <label>Deadline</label>
        <input type="date" name="deadline"
               class="form-control"
               value="<?= $project->deadline ?>">
    </div>

</div>

<!-- MANAGEMENT -->
<div class="row mt-3">

   <div class="col-md-4">

    <label>Project Manager</label>

    <select name="project_manager_id" class="form-select">

        <option value="">-- Select Manager --</option>

        <?php foreach($users as $u): ?>

            <option value="<?= $u->id ?>"
                <?= $project->project_manager_id == $u->id ? 'selected' : '' ?>>

                <?= htmlspecialchars($u->full_name) ?>

            </option>

        <?php endforeach; ?>

    </select>

</div>

    <div class="col-md-4">
        <label>Contract No</label>
        <input type="text" name="contract_number"
               class="form-control"
               value="<?= $project->contract_number ?>">
    </div>

    <div class="mb-3">
    <label class="form-label">PROJECT CODE</label>

    <input
        type="text"
        class="form-control"
        value="<?= htmlspecialchars($data['project']->project_code ?? '') ?>"
        readonly
    >

    <small class="text-muted">
        Project Code is automatically generated and cannot be changed.
    </small>
</div>

</div>

<!-- PRIORITY / STATUS -->
<div class="row mt-3">

    <div class="col-md-6">
        <label>Status</label>
        <select name="status" class="form-select">
            <option value="planning" <?= $project->status=='planning'?'selected':'' ?>>Planning</option>
            <option value="in_progress" <?= $project->status=='in_progress'?'selected':'' ?>>In Progress</option>
            <option value="completed" <?= $project->status=='completed'?'selected':'' ?>>Completed</option>
        </select>
    </div>

    <div class="col-md-6">
        <label>Priority</label>
        <select name="priority" class="form-select">
            <option value="low" <?= $project->priority=='low'?'selected':'' ?>>Low</option>
            <option value="medium" <?= $project->priority=='medium'?'selected':'' ?>>Medium</option>
            <option value="high" <?= $project->priority=='high'?'selected':'' ?>>High</option>
            <option value="critical" <?= $project->priority=='critical'?'selected':'' ?>>Critical</option>
        </select>
    </div>

</div>

<!-- BUDGET -->
<div class="mt-3">
    <label>Budget</label>
    <input type="number" step="0.01"
           name="budget"
           class="form-control"
           value="<?= $project->budget ?>">
</div>

<!-- BUTTON -->
<div class="mt-4">
    <button class="btn btn-success">
        Save Changes
    </button>

    <a href="<?= URLROOT ?>/projects" class="btn btn-secondary">
        Cancel
    </a>
</div>

</form>