<?php if (!empty($_SESSION['error'])) : ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])) : ?>
    <div class="alert alert-success">
        <?= $_SESSION['success'] ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">

    <div>
        <h2 class="mb-0">
            <i class="fas fa-project-diagram"></i>
            (<?= count($projects) ?>)
            <?= $showArchived ? 'Archived Projects' : 'Active Projects' ?>
        </h2>
    </div>

    <div class="mt-2 mt-md-0">

        <a href="<?= URLROOT ?>/projects"
            class="btn btn-success">
            Active Projects
        </a>

        <a href="<?= URLROOT ?>/projects/archived"
            class="btn btn-secondary">
            Archived Projects
        </a>

        <!-- <a href="<?//= URLROOT ?>/projects/create"
            class="btn btn-primary">
            <i class="fas fa-plus"></i>
            New Project
        </a> -->

        <?php if (AuthHelper::canView('projects.create')): ?>

<a href="<?= URLROOT ?>/projects/create"
            class="btn btn-primary">
            <i class="fas fa-plus"></i>
            New Project
        </a>

<?php endif; ?>




    </div>

</div>

<div class="card shadow-sm">

    <div class="card-body p-2">

        <div class="table-responsive">

            <table class="table table-striped table-hover table-sm align-middle w-100 mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Project</th>
                <!-- <th>Customer</th> -->
                <th>Type</th>
                <th>Location</th>
                <th>Doc.</th>
                <th><i class="fas fa-tools"></i> Work Status</th>
                <th>Deadline</th>
                <th>Budget LYD</th>
                <th>Costs LYD</th>
                <?php if (empty($showArchived)): ?>
                    <th><i class="fas fa-coins"></i> Finance</th>
                <?php endif; ?>
                <?php if (empty($showArchived)): ?>
                    <th><i class="fas fa-dollar"></i> Cost Status</th>
                <?php endif; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($projects)): ?>
                <tr>
                    <?php
                    $columnCount = empty($showArchived) ? 13 : 11;
                    ?>
                    <td colspan="<?= $columnCount ?>" class="text-center py-5">

                        <i class="fas fa-folder-open fa-3x text-secondary mb-3"></i>

                        <h5 class="mt-3 text-muted">
                            No <?= $showArchived ? 'archived' : 'active' ?> projects available.
                        </h5>

                        <p class="text-muted mb-3">
                            <?= $showArchived
                                ? 'There are currently no archived projects.'
                                : 'Create your first project to get started.' ?>
                        </p>

                        <?php if (empty($showArchived)): ?>
                            <a href="<?= URLROOT ?>/projects/create" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                    
                            </a>
                        <?php endif; ?>

                    </td>
                </tr>

            <?php else: ?>

                <?php foreach ($projects as $project) :

                    $budget = (float)$project->budget;
                    $cost   = (float)($project->total_cost ?? 0);

                    $ratio = ($budget > 0) ? ($cost / $budget) : 0;

                    if ($cost == 0) {
                        $statusColor = 'secondary';
                        $label = 'Not Started';
                    } elseif ($ratio <= 0.8) {
                        $statusColor = 'success';
                        $label = 'Healthy';
                    } elseif ($ratio <= 1) {
                        $statusColor = 'warning';
                        $label = 'Warning';
                    } else {
                        $statusColor = 'danger';
                        $label = 'Over Budget';
                    }

                ?>
                    <tr>
                        <td><?= $project->id ?></td>
                        <td class="text-nowrap"><?= htmlspecialchars($project->title) ?></td>
                        <!-- <td class="text-nowrap"><?//= htmlspecialchars($project->customer_name ?? 'N/A') ?></td> -->
                        <td class="text-nowrap">
                            <?= ucfirst($project->project_type ?? '-') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($project->site_location ?? '-') ?>
                        </td>

                        <td>
                            <a href="<?= URLROOT ?>/projects/documents/<?= $project->id ?>"
                                class="btn btn-sm btn-dark"
                                style="white-space: nowrap;">

                                <i class="fas fa-folder"></i>
                                <span class="badge bg-light text-dark ms-1">
                                    <?= $project->document_count ?? 0 ?>
                                </span>

                            </a>
                        </td>
                        <td>
                            <?php // Project Status
                            $statusColors = [
                                'planning'     => 'secondary',
                                'in_progress'  => 'warning',
                                'testing'      => 'info',
                                'completed'    => 'success',
                                'cancelled'    => 'danger'
                            ];
                            $badge =
                                $statusColors[$project->status]
                                ?? 'secondary';
                            ?>
                            <div style="min-width: 6rem;">
                                <span class="badge bg-<?= $badge ?>">
                                    <?= ucwords(str_replace('_', ' ', $project->status)) ?>
                                </span>
                        </td>
                        <?php
                        $deadline = strtotime($project->deadline);
                        $today    = strtotime(date('Y-m-d'));
                        $daysRemaining = floor(($deadline - $today) / 86400);
                        $formattedDate = date('d M Y', $deadline);
                        ?>
                        <td>
                            <?php if ($project->status == 'completed'): ?>
                                <span class="badge bg-success">
                                    Completed · <?= $formattedDate ?>
                                </span>
                            <?php elseif ($daysRemaining < 0): ?>
                                <span class="badge bg-danger">
                                    <?= $formattedDate ?> · Overdue by <?= abs($daysRemaining) ?> day<?= abs($daysRemaining) != 1 ? 's' : '' ?>
                                </span>

                            <?php elseif ($daysRemaining == 0): ?>

                                <span class="badge bg-danger">
                                    <?= $formattedDate ?> · Due Today
                                </span>

                            <?php elseif ($daysRemaining <= 3): ?>

                                <span class="badge bg-danger">
                                    <?= $formattedDate ?> · <?= $daysRemaining ?> day<?= $daysRemaining != 1 ? 's' : '' ?> left
                                </span>

                            <?php elseif ($daysRemaining <= 7): ?>

                                <span class="badge bg-warning text-dark">
                                    <?= $formattedDate ?> · <?= $daysRemaining ?> days left
                                </span>

                            <?php elseif ($daysRemaining <= 14): ?>

                                <span class="badge bg-info text-dark">
                                    <?= $formattedDate ?> · <?= $daysRemaining ?> days left
                                </span>

                            <?php else: ?>

                                <span class="badge bg-success">
                                    <?= $formattedDate ?> · <?= $daysRemaining ?> days left
                                </span>

                            <?php endif; ?>

                        </td>
                        <td class="text-nowrap"><?= number_format($project->budget, 0) ?></td>

                        <td class="text-nowrap"><?= number_format($cost, 0) ?></td>

                        <?php if (empty($showArchived)): ?>
                            <!-- ✅ Finance columns , Hide for Archived projects-->
                            <td>
                                <a href="<?= URLROOT ?>/project-costs/<?= $project->id ?>"
                                    class="btn btn-sm btn-info text-nowrap">
                                    Details
                                </a>
                            </td>

                            <td>
                                <div style="min-width: 6rem;">
                                    <div class="progress" style="height: 0.5rem;">
                                        <div class="progress-bar bg-<?= $statusColor ?>"
                                            style="width: <?= min(100, $ratio * 100) ?>%">
                                        </div>
                                    </div>
                                    <small class="text-<?= $statusColor ?>">
                                        <?= round($ratio * 100) ?>% (<?= $label ?>)
                                    </small>
                                </div>
                            </td>
                        <?php endif; ?>

                        <td class="text-nowrap">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= URLROOT ?>/projects/edit/<?= $project->id ?>"
                                    class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <?php if (empty($showArchived)): ?>

                                    <a href="<?= URLROOT ?>/projects/archive/<?= $project->id ?>"
                                        class="btn btn-sm btn-secondary"
                                        onclick="return confirm('Archive this project?')">
                                        Archive
                                    </a>

                                <?php endif; ?>

                                <?php if (!empty($showArchived)): ?>

                                    <a href="<?= URLROOT ?>/projects/restore/<?= $project->id ?>"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Restore this project?')">
                                        Restore
                                    </a>

                                <?php endif; ?>

                                <!-- <a href="<?//= URLROOT ?>/projects/delete/<?//= $project->id ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Permanently delete this project?')">
                                    Delete
                                </a> -->

<?php if ($_SESSION['role_id'] == 1): ?>

    <a href="<?= URLROOT ?>/projects/delete/<?= $project->id ?>"
       class="btn btn-sm btn-danger"
       onclick="return confirm('Permanently delete this project?')">    
        Delete

    </a>

<?php endif; ?>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>
               </table>

        </div>

    </div>

</div>