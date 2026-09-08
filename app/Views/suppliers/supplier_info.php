<div class="container-fluid py-4">
    <div>
        <a href="<?= URLROOT ?>/supplierpayments/create/<?= $supplier->id ?>"
            class="btn btn-success">
            <i class="fas fa-money-bill"></i> Record Payment
        </a>
    </div>

    <br>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                <i class="fas fa-info-circle text-primary"></i>
                Supplier Profile
            </h2>

            <p class="text-muted mb-0">
                View supplier information, purchase history and performance.
            </p>
        </div>

        <div class="mt-3 mt-md-0">

            <a href="<?= URLROOT ?>/purchaseorders/create"
                class="btn btn-primary"><i class="fas fa-file-invoice"></i> Create Purchase Order
            </a>
            <a href="<?= URLROOT ?>/suppliers/edit/<?= $supplier->id ?>"
                class="btn btn-warning shadow-sm">

                <i class="fas fa-edit me-1"></i>
                Edit Supplier

            </a>
           <a
    href="<?= URLROOT ?>/suppliers/ledger/<?= $supplier->id ?>"
    class="btn btn-dark"
    target="_blank">

    <i class="fas fa-print me-1"></i>
    Print Supplier Ledger

</a>
            <a href="<?= URLROOT ?>/suppliers"
                class="btn btn-secondary shadow-sm">

                <i class="fas fa-arrow-left me-1"></i>
                Back

            </a>

        </div>

    </div>

    <div class="card border-0 shadow-lg mb-4">

        <div class="card-body bg-primary text-white rounded">

            <div class="row align-items-center">

                <div class="col-md-2 text-center">

                    <div class="display-1">

                        <i class="fas fa-truck me-2"></i>

                    </div>

                </div>

                <div class="col-md-10">

                    <h2 class="fw-bold mb-2">

                        <?= htmlspecialchars($supplier->company_name) ?>

                    </h2>

                    <div class="row">

                        <div class="col-lg-4">

                            <i class="fas fa-user me-2"></i>

                            <?= htmlspecialchars($supplier->contact_person) ?>

                        </div>

                        <div class="col-lg-4">

                            <i class="fas fa-envelope me-2"></i>

                            <?= htmlspecialchars($supplier->email) ?>

                        </div>

                        <div class="col-lg-4">

                            <i class="fas fa-phone me-2"></i>

                            <?= htmlspecialchars($supplier->phone) ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Purchase Orders
                            </small>

                            <h2 class="fw-bold mt-2">

                                <?= $summary['po_count'] ?? 0 ?>

                            </h2>

                        </div>

                        <div class="align-self-center">

                            <i class="fas fa-file-invoice fa-3x text-primary opacity-50"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <div class="col-md-6 col-xl-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">

                                Ordered Value

                            </small>

                            <h4 class="fw-bold mt-2">

                                <?= number_format($summary['ordered_value'] ?? 0, 2, '.', ',') ?>

                            </h4>

                        </div>

                        <div class="align-self-center">

                            <i class="fas fa-coins fa-3x text-success opacity-50"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">

                                Outstanding Balance

                            </small>
                            <h4 class="fw-bold mt-2 text-danger">
                                <?= number_format($summary['balance'] ?? 0, 2, '.', ',') ?> </h4>
                        </div>
                        <div class="align-self-center">

                            <i class="fas fa-calendar-check fa-3x text-danger opacity-50"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-6 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-primary text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-building me-2"></i>

                        Company Information

                    </h5>

                </div>

                <div class="card-body p-0">

                    <table class="table table-striped table-hover mb-0">

                        <tbody>

                            <tr>

                                <th width="35%">
                                    Company
                                </th>

                                <td>

                                    <?= htmlspecialchars($supplier->company_name) ?>

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Contact Person
                                </th>

                                <td>

                                    <?= htmlspecialchars($supplier->contact_person) ?>

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Email
                                </th>

                                <td>

                                    <a href="mailto:<?= htmlspecialchars($supplier->email) ?>">

                                        <?= htmlspecialchars($supplier->email) ?>

                                    </a>

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Phone
                                </th>

                                <td>

                                    <?= htmlspecialchars($supplier->phone) ?>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-lg-6 mb-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-success text-white">

                    <h5 class="mb-0">

                        <i class="fas fa-info-circle me-2"></i>

                        Additional Information

                    </h5>

                </div>

                <div class="card-body p-0">

                    <table class="table table-striped table-hover mb-0">

                        <tbody>

                            <tr>

                                <th width="35%">
                                    Address
                                </th>

                                <td>

                                    <?= htmlspecialchars($supplier->address ?? '-') ?>

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Created
                                </th>

                                <td>

                                    <?= date('d M Y', strtotime($supplier->created_at)) ?>

                                </td>

                            </tr>

                            <tr>

                                <th>
                                    Status
                                </th>

                                <td>

                                    <span class="badge bg-success">

                                        Active

                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                <i class="fas fa-file-invoice me-2"></i>
                Purchase Orders
            </h5>

            <span class="badge bg-light text-dark">
                <?= $summary['po_count'] ?? 0 ?> Record(s)
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="140">PO Number</th>

                            <th>Status</th>

                            <th class="text-end">Total Amount</th>

                            <th width="170">Created</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($purchase_orders)): ?>

                            <?php foreach ($purchase_orders as $po): ?>

                                <tr>

                                    <td>

                                        <a href="<?= URLROOT ?>/purchaseOrders/details/<?= $po->id ?>"
                                            class="fw-semibold text-decoration-none">

                                            <?= htmlspecialchars($po->po_number) ?>

                                        </a>

                                    </td>

                                    <td>

                                        <?php
                                        switch (strtolower($po->status)) {
                                            case 'approved':
                                                $badge = 'success';
                                                break;

                                            case 'pending':
                                                $badge = 'warning';
                                                break;

                                            case 'cancelled':
                                                $badge = 'danger';
                                                break;

                                            case 'draft':
                                                $badge = 'secondary';
                                                break;

                                            default:
                                                $badge = 'primary';
                                        }
                                        ?>

                                        <span class="badge bg-<?= $badge ?>">
                                            <?= htmlspecialchars($po->status) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?= number_format($po->total_amount, 2) ?>
                                    </td>
                                    <td>
                                        <?= date('d M Y', strtotime($po->created_at)) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <br>
                                    No Purchase Orders found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2" class="text-primary fs-5"> Total Purchase Order Value </th>
                            <th colspan="2" class="text-end text-primary fs-5"> <?= number_format($summary['ordered_value'], 2) ?> </th>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                Goods Receipts (GRN)
            </h5>
        </div>

        <div class="card-body">

            <div class="p-3">
                <strong>
                    Total Received Value:
                    <?= number_format($summary['received_value'], 2) ?>
                </strong>
            </div>


        </div>

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    Supplier Payments
                </h5>
            </div>

            <div class="card-body p-0">

                <div class="p-3">
                    <strong>
                        Paid Total:
                        <?= number_format($summary['paid_amount'], 2) ?>
                    </strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>

                            <th>Date</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-end">Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($payments as $pay): ?>
                                <tr>
                                    <td><?= $pay->payment_date ?></td>
                                    <td><?= $pay->method ?></td>
                                    <td><?= $pay->reference ?></td>
                                    <td class="text-end">
                                        <?= number_format($pay->amount, 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- ------------ LEDGER --------------- -->
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        Supplier Ledger
                    </h5>

                    <span class="badge bg-light text-dark">
                        Balance: <?= number_format($summary['balance'], 2) ?>
                    </span>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-striped table-hover align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th width="120">Date</th>
                                    <th>Type</th>
                                    <th>Reference</th>
                                    <th>Description</th>
                                    <th class="text-end">Debit</th>
                                    <th class="text-end">Credit</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $running = 0;

                                // Optional opening balance row (ERP style)
                                $opening = 0;
                                ?>

                                <tr class="table-secondary">
                                    <td colspan="6"><strong>Opening Balance</strong></td>
                                    <td class="text-end fw-bold">
                                        <?= number_format($opening, 2) ?>
                                    </td>
                                </tr>

                                <?php if (!empty($ledger)): ?>

                                    <?php foreach ($ledger as $row): ?>

                                        <?php
                                        $debit  = (float)$row->debit;
                                        $credit = (float)$row->credit;

                                        $running += ($debit - $credit);
                                        ?>
                                        <tr>
                                            <td>
                                                <?= date('d M Y', strtotime($row->date ?? '')) ?>
                                            </td>
                                            <td>
                                                <?php if (($row->type ?? '') === 'GRN'): ?>
                                                    <span class="badge bg-warning text-dark">GRN</span>
                                                <?php elseif (($row->type ?? '') === 'PAYMENT'): ?>
                                                    <span class="badge bg-success">PAYMENT</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        <?= htmlspecialchars($row->type ?? '') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($row->reference ?? '') ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    ($row->type ?? '') == 'GRN'
                                                        ? 'Goods Receipt'
                                                        : 'Supplier Payment'
                                                ) ?>
                                            </td>

                                            <td class="text-end text-danger">
                                                <?= $debit > 0 ? number_format($debit, 2) : '-' ?>
                                            </td>

                                            <td class="text-end text-success">
                                                <?= $credit > 0 ? number_format($credit, 2) : '-' ?>
                                            </td>

                                            <td class="text-end fw-bold">
                                                <?= number_format($running, 2) ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-folder-open fa-2x mb-2"></i>
                                            <br>
                                            No ledger entries found.
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>