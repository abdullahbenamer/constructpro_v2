<?php
// $customers = $this->model('Customer')->getAll();
$customers = $data['customers'];
echo "<p>Customers loaded: " . count($customers) . "</p>";
?>
<h2><i class="fas fa-plus-circle"></i> A New Project</h2>
<form method="POST">

    <!-- GENERAL INFO -->

    <div class="row">

        <div class="col-md-6">
            <label>Customer *</label>
            <select name="customer_id" class="form-select" required>
                <option value="">Select Customer</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer->id ?>">
                        <?= htmlspecialchars($customer->company) ?> - <?= $customer->name ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label>Project Title *</label>
            <input type="text" name="title" class="form-control" required>
        </div>

    </div>

    <!-- ---------- PROJECT CLASSIFICATION ------------- -->

    <div class="row mt-3">

        <div class="col-md-4">
            <label>Project Type *</label>

            <select name="project_type"
                id="projectType"
                class="form-select"
                required>

                <option value="">Select Type</option>

                <option value="Construction">Construction</option>
                <option value="Maintenance">Maintenance</option>
                <option value="Inspection">Inspection</option>
                <option value="Consultancy">Consultancy</option>
                <option value="Other">Other</option>

            </select>
        </div>

        <!-- ///////////// -->
        <div class="mb-3">
            <label class="form-label">PROJECT SCOPE</label>

            <div class="border rounded p-3 bg-light">
                <div class="row g-2">

                    <?php
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

                    <?php foreach ($scopes as $scope): ?>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="scopes[]"
                                    value="<?= htmlspecialchars($scope) ?>"
                                    id="scope_<?= md5($scope) ?>">

                                <label
                                    class="form-check-label"
                                    for="scope_<?= md5($scope) ?>">
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

        <!-- ///////////// -->

<div class="mb-3">
    <label class="form-label">PROJECT CODE</label>

    <input
        type="text"
        class="form-control"
        value="AUTO-GENERATED"
        readonly
    >

    <small class="text-muted">
        Project Code will be generated automatically when the project is created.
    </small>
</div>

        <div class="col-md-2">

            <label>Contract Number</label>

            <input type="text"
                name="contract_number"
                class="form-control">

        </div>

    </div>

    <!-- ------Location + Management---------- -->

    <div class="row mt-3">

        <div class="col-md-6">
            <label>Site Location</label>
            <input type="text" name="site_location" class="form-control">
        </div>

        <div class="col-md-6">

            <label>Project Manager</label>

            <select name="project_manager_id" class="form-select">

                <option value="">
                    -- Select Project Manager --
                </option>

                <?php foreach ($data['users'] as $user): ?>

                    <option value="<?= $user->id ?>">

                        <?= htmlspecialchars($user->full_name) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>


    <!-- ---------Schedule + Priority------------ -->
    <div class="row mt-3">

        <div class="col-md-4">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control">
        </div>

        <div class="col-md-4">
            <label>Deadline *</label>
            <input type="date" name="deadline" class="form-control" required>
        </div>

        <div class="col-md-4">
            <label>Priority</label>
            <select name="priority" class="form-select">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
                <option value="critical">Critical</option>
            </select>
        </div>

    </div>
    <!-- ----------Status + Budget-------------- -->

    <div class="row mt-3">

        <div class="col-md-6">
            <label>Status *</label>
            <select name="status" class="form-select" required>
                <option value="planning">Planning</option>
                <option value="in_progress">In Progress</option>
                <option value="testing">Testing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div class="col-md-6">
            <label>Budget (LYD)</label>
            <input type="number" name="budget" class="form-control" step="0.01">
        </div>

    </div>

    <!-- --------Description --------- -->
    <div class="mt-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save"></i> Create Project
    </button>
    <a href="<?= URLROOT ?>/projects" class="btn btn-secondary btn-lg">Cancel</a>
</form>