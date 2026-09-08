<?php

require_once '../app/Services/BaseService.php';

class PurchaseOrderService extends BaseService
{
    private PurchaseOrderModel $poModel;

    public function __construct(
        PurchaseOrderModel $poModel
    ) {
        parent::__construct();

        $this->poModel = $poModel;
    }

    /**
     * Cancel a Purchase Order.
     *
     * Allowed:
     *   draft
     *   approved
     *   partial
     *
     * Not allowed:
     *   received
     *   cancelled
     *
     * Cancellation does NOT affect:
     *   - inventory quantity
     *   - warehouse quantity
     *   - inventory movements
     *   - GRNs
     *   - supplier ledger
     *
     * receiving_status is intentionally preserved.
     */
    public function cancel(int $poId): bool
    {
        return $this->transaction(function () use ($poId) {

            /*
            |--------------------------------------------------------------------------
            | 1. Validate PO ID
            |--------------------------------------------------------------------------
            */

            if ($poId <= 0) {
                throw new Exception(
                    'Invalid purchase order.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Load Purchase Order
            |--------------------------------------------------------------------------
            */

            $po = $this->poModel->getById($poId);

            if (!$po) {
                throw new Exception(
                    'Purchase Order not found.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Validate current status
            |--------------------------------------------------------------------------
            */

            if ($po->status === 'cancelled') {
                throw new Exception(
                    'Purchase Order is already cancelled.'
                );
            }

            if ($po->status === 'received') {
                throw new Exception(
                    'A fully received Purchase Order cannot be cancelled.'
                );
            }

            if (!in_array(
                $po->status,
                ['draft', 'approved', 'partial'],
                true
            )) {
                throw new Exception(
                    'Purchase Order cannot be cancelled from its current status.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Cancel PO
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | receiving_status is NOT changed.
            |
            | Examples:
            |
            | draft    + OPEN    -> cancelled + OPEN
            | approved + OPEN    -> cancelled + OPEN
            | partial  + PARTIAL -> cancelled + PARTIAL
            |
            */

            $this->poModel->cancel($poId);

            return true;
        });
    }
}