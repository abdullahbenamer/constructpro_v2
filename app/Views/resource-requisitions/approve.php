<div class="container-fluid mt-4">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                <i class="fas fa-check-circle"></i>
                Approval Decision
            </h4>

            <small class="text-muted">
                Review and approve or reject this resource requisition
            </small>

        </div>

        <a href="<?= URLROOT ?>/ResourceRequisitions/details/<?= $data['requisition']->id ?>"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Back

        </a>

    </div>


    <!-- REQUISITION SUMMARY -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <strong>
                <i class="fas fa-file-alt"></i>
                Requisition Summary
            </strong>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">

                    <label class="text-muted">
                        Requisition No.
                    </label>

                    <div class="fw-bold">

                        <?= htmlspecialchars(
                            $data['requisition']->requisition_no
                        ) ?>

                    </div>

                </div>


                <div class="col-md-4 mb-3">

                    <label class="text-muted">
                        Project
                    </label>

                    <div class="fw-bold">

                        <?= htmlspecialchars(
                            $data['requisition']->project_name ?? '-'
                        ) ?>

                    </div>

                </div>

<div class="col-md-4 mb-3">

    <label class="text-muted">
        Priority
    </label>

    <div>

        <?php if ($data['requisition']->priority === 'HIGH'): ?>

            <span class="badge bg-danger">
                HIGH
            </span>

        <?php elseif ($data['requisition']->priority === 'MEDIUM'): ?>

            <span class="badge bg-warning text-dark">
                MEDIUM
            </span>

        <?php elseif ($data['requisition']->priority === 'LOW'): ?>

            <span class="badge bg-secondary">
                LOW
            </span>

        <?php else: ?>

            <span class="text-muted">
                Not Set
            </span>

        <?php endif; ?>

    </div>

</div>

            </div>


            <div class="row">

                <div class="col-md-4">

                    <label class="text-muted">
                        Requested By
                    </label>

                    <div>

                        <?= htmlspecialchars(
                            $data['requisition']->requested_by_name ?? '-'
                        ) ?>

                    </div>

                </div>


                <div class="col-md-4">

                    <label class="text-muted">
                        Required Date
                    </label>

                    <div>

                        <?= htmlspecialchars(
                            $data['requisition']->required_date
                        ) ?>

                    </div>

                </div>


                <div class="col-md-4">

                    <label class="text-muted">
                        Status
                    </label>

                    <div>

                        <span class="badge bg-primary">

                            <?= htmlspecialchars(
                                $data['requisition']->status
                            ) ?>

                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- APPROVAL FORM -->

    <div class="card shadow-sm">

        <div class="card-header">

            <strong>
                <i class="fas fa-gavel"></i>
                Approval Decision
            </strong>

        </div>

        <div class="card-body">

            <form method="POST"
                  action="<?= URLROOT ?>/ResourceRequisitions/processApproval/<?= $data['requisition']->id ?>">

                <!-- APPROVER REMARKS -->

                <div class="mb-4">

                    <label class="form-label">

                        Approval Remarks

                    </label>

                    <textarea
                        name="remarks"
                        class="form-control"
                        rows="4"
                        placeholder="Enter approval or rejection remarks..."></textarea>

                </div>


                <!-- ACTION BUTTONS -->

                <div class="d-flex justify-content-between">

                    <a href="<?= URLROOT ?>/ResourceRequisitions/details/<?= $data['requisition']->id ?>"
                       class="btn btn-secondary">

                        Cancel

                    </a>


                    <div>

                        <button
                            type="submit"
                            name="action"
                            value="REJECT"
                            class="btn btn-danger"
                            onclick="return confirm('Reject this requisition?');">

                            <i class="fas fa-times-circle"></i>
                            Reject

                        </button>


                        <button
                            type="submit"
                            name="action"
                            value="APPROVE"
                            class="btn btn-success"
                            onclick="return confirm('Approve this requisition?');">

                            <i class="fas fa-check-circle"></i>
                            Approve

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>