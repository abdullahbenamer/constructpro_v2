<h2 class="mb-4">
    <i class="fas fa-clipboard-list text-primary"></i>
    Resource Requisitions
</h2>

<div class="mb-3">

    <a href="<?= URLROOT ?>/resourcerequisitions/create"
        class="btn btn-primary">

        <i class="fas fa-plus"></i>

        New Resource Requisition

    </a>

</div>

<div class="card shadow-sm">

    <div class="card-header bg-primary text-white">

        <strong>Resource Requisition Register</strong>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-striped table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th width="140">Req No.</th>

                        <th>Project</th>

                        <th width="120">Request Date</th>

                        <th width="120">Required Date</th>

                        <th width="120">Priority</th>

                        <th width="130">Status</th>

                        <th>Requested By</th>

                        <th width="180" class="text-center">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($requisitions)): ?>

                        <?php foreach ($requisitions as $req): ?>

                            <?php

                            switch ($req->status) {

                                case 'DRAFT':
                                    $badge = 'secondary';
                                    break;

                                case 'SUBMITTED':
                                    $badge = 'warning';
                                    break;

                                case 'UNDER_REVIEW':
                                    $badge = 'info';
                                    break;

                                case 'APPROVED':
                                    $badge = 'success';
                                    break;

                                case 'PARTIALLY_FULFILLED':
                                    $badge = 'primary';
                                    break;

                                case 'COMPLETED':
                                    $badge = 'dark';
                                    break;

                                case 'REJECTED':
                                    $badge = 'danger';
                                    break;

                                case 'CANCELLED':
                                    $badge = 'danger';
                                    break;

                                default:
                                    $badge = 'secondary';

                            }

                            ?>

                            <tr>

                                <td>

                                    <a href="<?= URLROOT ?>/resourcerequisitions/details/<?= $req->id ?>">

                                        <strong>

                                            <?= htmlspecialchars($req->req_number) ?>

                                        </strong>

                                    </a>

                                </td>

                                <td>

                                    <?= htmlspecialchars($req->project_name ?? '-') ?>

                                </td>

                                <td>

                                    <?= date('d M Y', strtotime($req->request_date)) ?>

                                </td>

                                <td>

                                    <?= !empty($req->required_date)
                                        ? date('d M Y', strtotime($req->required_date))
                                        : '-' ?>

                                </td>

                                <td>

                                   <?php
switch (strtoupper($req->priority)) {

    case 'LOW':
        $priorityClass = 'bg-success';
        break;

    case 'NORMAL':
        $priorityClass = 'bg-primary';
        break;

    case 'HIGH':
        $priorityClass = 'bg-warning text-dark';
        break;

    case 'URGENT':
        $priorityClass = 'bg-danger';
        break;

    case 'CRITICAL':
        $priorityClass = 'bg-dark';
        break;

    default:
        $priorityClass = 'bg-secondary';
}
?>

<span class="badge <?= $priorityClass ?>">
    <?= htmlspecialchars($req->priority) ?>
</span>

                                </td>

                                <td>

                                    <span class="badge bg-<?= $badge ?>">

                                        <?= htmlspecialchars($req->status) ?>

                                    </span>

                                </td>

                                <td>

                                    <?= htmlspecialchars($req->requested_by_name) ?>

                                </td>

                                <td class="text-center">

                                    <a href="<?= URLROOT ?>/resourcerequisitions/details/<?= $req->id ?>"
                                        class="btn btn-sm btn-info">

                                        <!-- <i class="fas fa-eye"></i> -->
                                         View

                                    </a>

                                    <?php if ($req->status == 'DRAFT'): ?>

                                        <a href="<?= URLROOT ?>/resourcerequisitions/edit/<?= $req->id ?>"
                                            class="btn btn-sm btn-warning">

                                            <!-- <i class="fas fa-edit"></i> -->
                                             Edit

                                        </a>

                                        <a href="<?= URLROOT ?>/resourcerequisitions/delete/<?= $req->id ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this requisition?');">

                                            <!-- <i class="fas fa-trash"></i> -->
                                            Delete

                                        </a>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="8" class="text-center py-5 text-muted">

                                <i class="fas fa-clipboard-list fa-3x mb-3"></i>

                                <br>

                                No Resource Requisitions found.

                                <br><br>

                                <a href="<?= URLROOT ?>/resourcerequisitions/create"
                                    class="btn btn-primary">

                                    <i class="fas fa-plus"></i>

                                    Create First Requisition

                                </a>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>