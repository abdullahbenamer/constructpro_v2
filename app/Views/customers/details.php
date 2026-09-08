<h2>Customer Details</h2>

<div class="card p-3 mb-3">
    <h4><?= htmlspecialchars($data['customer']->company) ?></h4>

    <p><strong>Account Manager:</strong>
        <?= htmlspecialchars($data['customer']->account_manager ?? 'Not assigned') ?>
    </p>
</div>

<h4>Projects</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Project</th>
            <th>Status</th>
            <th>Project Manager</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($data['projects'] as $project): ?>
            <tr>
                <td>
                    <a class="fw-bold text-decoration-none" href="<?= URLROOT ?>/project-costs/index/<?= $project->id ?>">
                        <i class="fas fa-building"></i> - <?= strtoupper(htmlspecialchars($project->title)) ?>
                    </a>
                </td>

                <td>
                    <span class="badge bg-info">
                        <?= strtoupper(htmlspecialchars($project->status)) ?></span>
                </td>
                <td>
                    <?php if (!empty($project->project_manager_id)): ?>
                        <a class="text-decoration-none" href="<?= URLROOT ?>/users/details/<?= $project->project_manager_id ?>">
                            <?= htmlspecialchars($project->project_manager) ?>
                        </a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>

                <td>
                    <a class="btn btn-sm btn-info"
                        href="<?= URLROOT ?>/project-costs/<?= $project->id ?>">Details
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>