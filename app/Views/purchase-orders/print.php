<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Purchase Order - <?= htmlspecialchars($po->po_number) ?>
    </title>

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 30px;
        }

        .document {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            gap: 40px;
        }


        /* COMPANY */

        .company-block {
            flex: 1;
            line-height: 1.5;
        }

        .company-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .company-detail {
            font-size: 12px;
            color: #444;
            margin-top: 3px;
        }


        /* PO INFORMATION */

        .po-block {
            width: 270px;
            text-align: left;
        }

        .document-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .po-block .meta {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
        }

        .meta {
            margin-top: 8px;
            font-size: 13px;
        }

        .supplier-box {
            border: 1px solid #ccc;
            padding: 12px;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background: #eee;
        }

        .text-end {
            text-align: right;
        }

        .total-row th,
        .total-row td {
            font-weight: bold;
        }

        .notes {
            margin-top: 25px;
            border: 1px solid #ccc;
            padding: 12px;
        }

        .signatures {
            margin-top: 70px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 30%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 45px;
            padding-top: 6px;
        }

        .actions {
            margin-bottom: 20px;
        }

        @media print {

            body {
                padding: 0;
            }

            .actions {
                display: none;
            }

            .document {
                max-width: none;
            }

        }
    </style>

</head>

<body>

    <div class="actions">

        <button onclick="window.print()">
            Print Purchase Order
        </button>

        <button onclick="window.close()">
            Close
        </button>

    </div>


    <div class="document">

        <div class="header">

            <!-- COMPANY INFORMATION -->
            <div class="company-block">

                <div class="company-name">
                    <?= htmlspecialchars(
                        $settings->company_name ?? 'Company Name'
                    ) ?>
                </div>

                <?php if (!empty($settings->address)): ?>

                    <div class="company-detail">
                        <?= nl2br(
                            htmlspecialchars($settings->address)
                        ) ?>
                    </div>

                <?php endif; ?>

                <?php if (!empty($settings->contacts)): ?>

                    <div class="company-detail">
                        <?= nl2br(
                            htmlspecialchars($settings->contacts)
                        ) ?>
                    </div>

                <?php endif; ?>

            </div>


            <!-- PURCHASE ORDER INFORMATION -->
            <div class="po-block">

                <div class="document-title">
                    PURCHASE ORDER
                </div>

                <div class="meta">
                    <strong>PO Number:</strong>
                    <?= htmlspecialchars($po->po_number) ?>
                </div>

                <div class="meta">
                    <strong>Order Date:</strong>
                    <?= htmlspecialchars($po->order_date) ?>
                </div>

                <div class="meta">
                    <strong>Expected Date:</strong>
                    <?= htmlspecialchars(
                        $po->expected_date ?? '-'
                    ) ?>
                </div>

            </div>

        </div>


        <div class="supplier-box">

            <strong>SUPPLIER</strong>

            <div style="margin-top: 8px;">

                <?= htmlspecialchars($po->supplier_name) ?>

            </div>

        </div>

<!-- DELIVERY / SHIP TO -->

<div
    class="supplier-box"
    style="margin-bottom: 25px;">

    <strong>
        DELIVERY / SHIP TO
    </strong>

    <?php if (
        $po->delivery_method === 'DIRECT_TO_PROJECT_SITE'
    ): ?>

        <div style="margin-top: 10px;">

            <div class="meta">
                <strong>Project:</strong>
                <?= htmlspecialchars(
                    $po->project_name ?? '-'
                ) ?>
            </div>

            <div class="meta">
                <strong>Delivery Method:</strong>
                Direct to Project Site
            </div>

            <div class="meta">

                <strong>Delivery Location:</strong><br>

                <?= nl2br(
                    htmlspecialchars(
                        $po->project_site_location ?? '-'
                    )
                ) ?>

            </div>

            <div class="meta">

                <strong>Site Contact:</strong>
                <?= htmlspecialchars(
                    $po->project_manager_name ?? '-'
                ) ?>

            </div>

            <div class="meta">

                <strong>Contact Number:</strong>
                <?= htmlspecialchars(
                    $po->project_manager_mobile ?? '-'
                ) ?>

            </div>

        </div>


    <?php elseif (
        $po->delivery_method === 'WAREHOUSE'
    ): ?>

        <div style="margin-top: 10px;">

            <div class="meta">

                <strong>Warehouse:</strong>

                <?= htmlspecialchars(
                    $po->target_warehouse_code ?? ''
                ) ?>

                <?php if (!empty($po->target_warehouse_name)): ?>

                    -
                    <?= htmlspecialchars(
                        $po->target_warehouse_name
                    ) ?>

                <?php endif; ?>

            </div>

            <div class="meta">

                <strong>Delivery Method:</strong>
                Warehouse

            </div>

            <div class="meta">

                <strong>Delivery Location:</strong><br>

                <?= nl2br(
                    htmlspecialchars(
                        $po->target_warehouse_address ?? '-'
                    )
                ) ?>

            </div>

            <?php if (!empty($po->target_warehouse_mobile)): ?>

                <div class="meta">

                    <strong>Warehouse Contact Number:</strong>

                    <?= htmlspecialchars(
                        $po->target_warehouse_mobile
                    ) ?>

                </div>

            <?php endif; ?>


            <?php if (!empty($po->storekeeper_name)): ?>

                <div class="meta">

                    <strong>Storekeeper:</strong>

                    <?= htmlspecialchars(
                        $po->storekeeper_name
                    ) ?>

                    <?php if (!empty($po->storekeeper_mobile)): ?>

                        -
                        <?= htmlspecialchars(
                            $po->storekeeper_mobile
                        ) ?>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>


    <?php else: ?>

        <div style="margin-top: 10px;">

            Delivery information not specified.

        </div>

    <?php endif; ?>

</div>
        <table>

            <thead>

                <tr>

                    <th style="width: 40px;">
                        #
                    </th>

                    <th>
                        Item
                    </th>

                    <th>
                        SKU
                    </th>

                    <th class="text-end">
                        Quantity
                    </th>

                    <th class="text-end">
                        Unit Cost
                    </th>

                    <th class="text-end">
                        Total
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php if (!empty($items)): ?>

                    <?php $n = 1; ?>

                    <?php foreach ($items as $item): ?>

                        <tr>

                            <td>
                                <?= $n++ ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($item->name) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($item->sku) ?>
                            </td>

                            <td class="text-end">

                                <?= number_format(
                                    (float)$item->quantity,
                                    2
                                ) ?>

                            </td>

                            <td class="text-end">

                                <?= number_format(
                                    (float)$item->unit_cost,
                                    2
                                ) ?>

                            </td>

                            <td class="text-end">

                                <?= number_format(
                                    (float)$item->quantity *
                                        (float)$item->unit_cost,
                                    2
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6"
                            style="text-align:center;">

                            No items.

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

            <tfoot>

                <tr class="total-row">

                    <th colspan="5"
                        class="text-end">

                        GRAND TOTAL

                    </th>

                    <th class="text-end">

                        <?= number_format(
                            (float)$po->total_amount,
                            2
                        ) ?>

                    </th>

                </tr>

            </tfoot>

        </table>


        <?php if (!empty($po->notes)): ?>

            <div class="notes">

                <strong>
                    Notes
                </strong>

                <div style="margin-top:8px;">

                    <?= nl2br(
                        htmlspecialchars($po->notes)
                    ) ?>

                </div>

            </div>

        <?php endif; ?>


        <div class="signatures">

            <div class="signature">

                <div class="signature-line">
                    Prepared By
                </div>

            </div>

            <div class="signature">

                <div class="signature-line">
                    Approved By
                </div>

            </div>

            <div class="signature">

                <div class="signature-line">
                    Supplier
                </div>

            </div>

        </div>

    </div>

</body>

</html>