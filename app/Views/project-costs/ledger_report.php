<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <title>Project Financial Ledger</title>
    <style>
        body {
            font-family: 'Tajawal', 'Roboto', sans-serif;
            font-size: 14px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company {
            font-size: 24px;
            font-weight: bold;
        }

        .report-title {
            font-size: 18px;
            margin-top: 5px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px;
        }

        .summary {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f2f2f2;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        .text-right {
            text-align: right;
        }

        .credit {
            color: green;
            font-weight: bold;
        }

        .debit {
            color: red;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
        }

        .balance-positive {
            color: green;
            font-weight: bold;
        }

        .balance-negative {
            color: red;
            font-weight: bold;
        }

        table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        table tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Logo -->
    <div style="display:flex; justify-content:space-between; align-items:left; margin-bottom:20px;">

        <div>
            <?php if (!empty($settings->logo)): ?>
                <img src="<?= URLROOT ?>/<?= $settings->logo ?>"
                    style="height:100px;">
            <?php endif; ?>
        </div>

        <div style="text-align:left;">
            <h2><?= htmlspecialchars($settings->company_name) ?></h2>
            <div><?= htmlspecialchars($settings->address) ?></div>
            <div><?= htmlspecialchars($settings->contacts) ?></div>
        </div>

    </div>
    <div class="header">
        <div class="company">CONSTRUCT PRO</div>

        <div class="report-title">
            PROJECT FINANCIAL LEDGER REPORT
        </div>
    </div>

    <table class="info-table">

        <tr>
            <td><strong>Project:</strong></td>
            <td><strong><?= $project->title ?></strong></td>
            <td><strong>Status:</strong></td>
            <td><?= $project->status ?></td>
        </tr>

        <tr>
            <td><strong>Customer:</strong></td>
            <td><?= $project->customer_name ?></td>
            <td><strong>Deadline:</strong></td>
            <td><?= $project->deadline ?></td>
        </tr>
    </table>
    <div class="summary">
        <strong>Total Advances:</strong>
        <?= number_format($summary->total_advances, 2) ?>
        &nbsp;&nbsp;&nbsp;
        <strong>Total Costs:</strong>
        <?= number_format($summary->total_costs, 2) ?>
        &nbsp;&nbsp;&nbsp;
        <strong>Balance:</strong>
        <?= number_format($summary->balance, 2) ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ledger as $row): ?>
                <tr>
                    <td>
                        <?= date('Y-m-d', strtotime($row->created_at)); ?></td>
                    <td>
                        <?= $row->entry_type === 'advance'
                            ? '<span class="badge bg-success">Advance</span>'
                            : '<span class="badge bg-danger">Cost</span>' ?>
                    </td>

                    <td><?= htmlspecialchars($row->description) ?></td>

                    <td class="text-right">
                        <?= !empty($row->quantity)
                            ? number_format($row->quantity, 2)
                            : '-' ?>
                    </td>

                    <td><?= number_format($row->debit, 2) ?></td>

                    <td><?= number_format($row->credit, 2) ?></td>

                    <?php
                    $balanceClass = ($row->balance_after < 0)
                        ? 'balance-negative'
                        : 'balance-positive';
                    ?>
                    <td class="<?= $balanceClass ?>">
                        <?= number_format($row->balance_after, 2) ?>
                    </td>
                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

    <div class="footer">

        Printed:

        <?= date('Y-m-d H:i') ?>

    </div>
    <!-- <script>
window.onload = function () {
    window.print();
};
</script> -->

</body>

</html>