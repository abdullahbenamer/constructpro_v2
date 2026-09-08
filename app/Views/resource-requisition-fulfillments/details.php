
<div class="container-fluid py-4">

    <!-- ================================================================
         PAGE HEADER
    ================================================================= -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="mb-1">
                <i class="fas fa-clipboard-check me-2"></i>
                RESOURCE REQUISITION FULFILLMENT
            </h3>

            <div class="text-muted">

                Fulfillment #<?= $fulfillment->id ?>

            </div>

        </div>
       
    </div>

    <!-- ================================================================
         SUCCESS / ERROR MESSAGES
    ================================================================= -->

    <?php if (!empty($_SESSION['success'])): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?= $_SESSION['success']; ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>


    <?php if (!empty($_SESSION['error'])): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <?= $_SESSION['error']; ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>


    <!-- ================================================================
         FULFILLMENT INFORMATION
    ================================================================= -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-light">

            <strong>
                FULFILLMENT INFORMATION
            </strong>

        </div>


        <div class="card-body">

            <div class="row">

                <!-- FULFILLMENT ID -->

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">

                        FULFILLMENT ID

                    </label>

                    <div class="fw-bold">

                        #<?= $fulfillment->id ?>

                    </div>

                </div>


                <!-- REQUISITION -->

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">

                        REQUISITION

                    </label>

                    <div class="fw-bold">

                        <?= htmlspecialchars(
                            $fulfillment->requisition_number
                            ?? ('RR #' . $fulfillment->requisition_id)
                        ) ?>

                    </div>

                </div>


                <!-- PROJECT -->

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">

                        PROJECT

                    </label>

                    <div class="fw-bold">

                        <?= htmlspecialchars(
                            $fulfillment->project_name
                            ?? '-'
                        ) ?>

                    </div>

                </div>


                <!-- DATE -->

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">

                        FULFILLMENT DATE

                    </label>

                    <div class="fw-bold">

                        <?= !empty($fulfillment->fulfillment_date)
                            ? date(
                                'd M Y',
                                strtotime(
                                    $fulfillment->fulfillment_date
                                )
                            )
                            : '-'
                        ?>

                    </div>

                </div>


                <!-- FULFILLED BY -->

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">

                        FULFILLED BY

                    </label>

                    <div class="fw-bold">

                        <?= htmlspecialchars(
                            $fulfillment->fulfilled_by_name
                            ?? $fulfillment->fulfilled_by
                            ?? '-'
                        ) ?>

                    </div>

                </div>


                <!-- CREATED AT -->

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">

                        CREATED AT

                    </label>

                    <div class="fw-bold">

                        <?= !empty($fulfillment->created_at)
                            ? date(
                                'd M Y H:i',
                                strtotime(
                                    $fulfillment->created_at
                                )
                            )
                            : '-'
                        ?>

                    </div>

                </div>


                <!-- STATUS -->

                <div class="col-md-3 mb-3">

                    <label class="text-muted small">

                        STATUS

                    </label>

                    <div>

                        <span class="badge bg-success">

                            PROCESSED

                        </span>

                    </div>

                </div>

            </div>


            <!-- REMARKS -->

            <?php if (!empty($fulfillment->remarks)): ?>

                <hr>

                <label class="text-muted small">

                    REMARKS

                </label>

                <div>

                    <?= nl2br(
                        htmlspecialchars(
                            $fulfillment->remarks
                        )
                    ) ?>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ================================================================
         FULFILLED ITEMS
    ================================================================= -->

    <div class="card shadow-sm">

        <div class="card-header bg-light">

            <strong>

                <i class="fas fa-boxes me-2"></i>

                FULFILLED MATERIALS

            </strong>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                MATERIAL
                            </th>

                            <th>
                                SKU
                            </th>

                            <th>
                                LOCATION
                            </th>

                            <th class="text-end">
                                QUANTITY
                            </th>

                            <th>
                                UOM
                            </th>

                            <th class="text-end">
                                UNIT COST
                            </th>

                            <th class="text-end">
                                TOTAL COST
                            </th>

                            <th>
                                REMARKS
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if (!empty($items)): ?>

                            <?php
                            $grand_total = 0;
                            ?>

                            <?php foreach ($items as $index => $item): ?>

                                <?php

                                $quantity =
                                    (float) (
                                        $item->fulfilled_quantity
                                        ?? 0
                                    );

                                $unit_cost =
                                    (float) (
                                        $item->unit_cost
                                        ?? 0
                                    );

                                $total =
                                    $quantity
                                    *
                                    $unit_cost;

                                $grand_total +=
                                    $total;

                                ?>

                                <tr>

                                    <!-- NUMBER -->

                                    <td>

                                        <?= $index + 1 ?>

                                    </td>


                                    <!-- MATERIAL -->

                                    <td>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $item->inventory_name
                                                ?? $item->requisition_description
                                                ?? '-'
                                            ) ?>

                                        </strong>

                                    </td>


                                    <!-- SKU -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $item->sku
                                            ?? '-'
                                        ) ?>

                                    </td>


                                    <!-- LOCATION -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $item->location_name
                                            ?? '-'
                                        ) ?>

                                    </td>


                                    <!-- QUANTITY -->

                                    <td class="text-end fw-bold">

                                        <?= number_format(
                                            $quantity,
                                            2
                                        ) ?>

                                    </td>


                                    <!-- UOM -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $item->base_unit
                                            ?? '-'
                                        ) ?>

                                    </td>


                                    <!-- UNIT COST -->

                                    <td class="text-end">

                                        <?= number_format(
                                            $unit_cost,
                                            2
                                        ) ?>

                                    </td>


                                    <!-- TOTAL COST -->

                                    <td class="text-end fw-bold">

                                        <?= number_format(
                                            $total,
                                            2
                                        ) ?>

                                    </td>


                                    <!-- REMARKS -->

                                    <td>

                                        <?= !empty($item->remarks)
                                            ? nl2br(
                                                htmlspecialchars(
                                                    $item->remarks
                                                )
                                            )
                                            : '-'
                                        ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>


                            <!-- =================================================
                                 GRAND TOTAL
                            ================================================== -->

                            <tr class="table-light">

                                <td
                                    colspan="7"
                                    class="text-end fw-bold"
                                >

                                    TOTAL MATERIAL COST

                                </td>

                                <td class="text-end fw-bold">

                                    <?= number_format(
                                        $grand_total,
                                        2
                                    ) ?>

                                </td>

                                <td></td>

                            </tr>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-4"
                                >

                                    NO FULFILLMENT ITEMS FOUND.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- ================================================================
         ACTION BUTTONS
    ================================================================= -->

    <div class="mt-4 d-flex gap-2">

              <a
            href="<?= URLROOT ?>/ResourceRequisitions/details/<?= $fulfillment->requisition_id ?>"
            class="btn btn-outline-primary"
        >

            <i class="fas fa-clipboard-list me-1"></i>

            VIEW REQUISITION

        </a>

    </div>

</div>
