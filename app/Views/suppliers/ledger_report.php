<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Supplier Financial Ledger
    </title>

    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        body {

            font-family:
                'Tajawal',
                'Roboto',
                sans-serif;

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


        .debit {

            color: red;

            font-weight: bold;

        }


        .credit {

            color: green;

            font-weight: bold;

        }


        .balance {

            font-weight: bold;

        }


        .balance-positive {

            color: green;

            font-weight: bold;

        }


        .balance-negative {

            color: red;

            font-weight: bold;

        }


        .footer {

            margin-top: 30px;

            font-size: 11px;

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


<!-- =========================================================
     COMPANY INFORMATION
========================================================= -->

<div
    style="
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        margin-bottom:20px;
    "
>

    <div>

        <?php if (!empty($settings->logo)): ?>

            <img
                src="<?= URLROOT ?>/<?= $settings->logo ?>"
                style="height:100px;"
            >

        <?php endif; ?>

    </div>


    <div style="text-align:left;">

        <h2>

            <?= htmlspecialchars(
                $settings->company_name
            ) ?>

        </h2>


        <div>

            <?= htmlspecialchars(
                $settings->address
            ) ?>

        </div>


        <div>

            <?= htmlspecialchars(
                $settings->contacts
            ) ?>

        </div>

    </div>

</div>


<!-- =========================================================
     REPORT HEADER
========================================================= -->

<div class="header">

    <div class="company">

        CONSTRUCT PRO

    </div>


    <div class="report-title">

        SUPPLIER FINANCIAL LEDGER REPORT

    </div>

</div>


<!-- =========================================================
     SUPPLIER INFORMATION
========================================================= -->

<table class="info-table">

    <tr>

        <td>
            <strong>Supplier:</strong>
        </td>

        <td>
            <strong>
                <?= htmlspecialchars(
                    $supplier->company_name
                ) ?>
            </strong>
        </td>

        <td>
            <strong>Contact Person:</strong>
        </td>

        <td>
            <?= htmlspecialchars(
                $supplier->contact_person ?? '-'
            ) ?>
        </td>

    </tr>


    <tr>

        <td>
            <strong>Phone:</strong>
        </td>

        <td>
            <?= htmlspecialchars(
                $supplier->phone ?? '-'
            ) ?>
        </td>

        <td>
            <strong>Email:</strong>
        </td>

        <td>
            <?= htmlspecialchars(
                $supplier->email ?? '-'
            ) ?>
        </td>

    </tr>


    <tr>

        <td>
            <strong>Address:</strong>
        </td>

        <td colspan="3">
            <?= htmlspecialchars(
                $supplier->address ?? '-'
            ) ?>
        </td>

    </tr>

</table>


<!-- =========================================================
     SUMMARY
========================================================= -->

<div class="summary">

    <strong>
        Total Debit:
    </strong>

    <?= number_format(
        $total_debit,
        2
    ) ?>


    &nbsp;&nbsp;&nbsp;


    <strong>
        Total Credit:
    </strong>

    <?= number_format(
        $total_credit,
        2
    ) ?>


    &nbsp;&nbsp;&nbsp;


    <strong>
        Outstanding Balance:
    </strong>

    <?php

    $balanceClass =
        $balance < 0
        ? 'balance-negative'
        : 'balance-positive';

    ?>

    <span class="<?= $balanceClass ?>">

        <?= number_format(
            $balance,
            2
        ) ?>

    </span>

</div>


<!-- =========================================================
     LEDGER
========================================================= -->

<table>

    <thead>

        <tr>

            <th width="100">
                Date
            </th>

            <th width="100">
                Type
            </th>

            <th>
                Reference
            </th>

            <th>
                Description
            </th>

            <th class="text-right">
                Debit
            </th>

            <th class="text-right">
                Credit
            </th>

            <th class="text-right">
                Balance
            </th>

        </tr>

    </thead>


    <tbody>

        <?php if (!empty($ledger)): ?>


            <?php foreach ($ledger as $row): ?>

                <?php

                $debit =
                    (float)$row->debit;

                $credit =
                    (float)$row->credit;

                $rowBalance =
                    (float)$row->balance;

                $rowBalanceClass =
                    $rowBalance < 0
                    ? 'balance-negative'
                    : 'balance-positive';

                ?>


                <tr>

                    <td>

                        <?= date(
                            'Y-m-d',
                            strtotime(
                                $row->date
                            )
                        ) ?>

                    </td>


                    <td>

                        <?= htmlspecialchars(
                            $row->type ?? ''
                        ) ?>

                    </td>


                    <td>

                        <?= htmlspecialchars(
                            $row->reference ?? ''
                        ) ?>

                    </td>


                    <td>

                        <?php

                        if (
                            ($row->type ?? '') === 'GRN'
                        ) {

                            echo 'Goods Receipt';

                        } elseif (
                            ($row->type ?? '') === 'PAYMENT'
                        ) {

                            echo 'Supplier Payment';

                        } elseif (
                            ($row->type ?? '') === 'RETURN'
                        ) {

                            echo 'Goods Return';

                        } else {

                            echo htmlspecialchars(
                                $row->type ?? ''
                            );

                        }

                        ?>

                    </td>


                    <td class="text-right debit">

                        <?= $debit > 0
                            ? number_format(
                                $debit,
                                2
                            )
                            : '-'
                        ?>

                    </td>


                    <td class="text-right credit">

                        <?= $credit > 0
                            ? number_format(
                                $credit,
                                2
                            )
                            : '-'
                        ?>

                    </td>


                    <td
                        class="
                            text-right
                            <?= $rowBalanceClass ?>
                        "
                    >

                        <?= number_format(
                            $rowBalance,
                            2
                        ) ?>

                    </td>

                </tr>


            <?php endforeach; ?>


        <?php else: ?>


            <tr>

                <td
                    colspan="7"
                    style="text-align:center;"
                >

                    No ledger transactions found.

                </td>

            </tr>


        <?php endif; ?>

    </tbody>

</table>


<!-- =========================================================
     FOOTER
========================================================= -->

<div class="footer">

    Printed:

    <?= date(
        'Y-m-d H:i'
    ) ?>

</div>


</body>

</html>