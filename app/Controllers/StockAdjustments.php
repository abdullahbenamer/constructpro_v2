<?php

class StockAdjustments extends Controller
{
    public function create($inventoryId = null)
    {
        AuthHelper::can('inventory.adjustment.create');

        $inventoryModel = $this->model('Inventory');
        $locationModel  = $this->model('InventoryLocation');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $inventoryId = (int)($_POST['inventory_id'] ?? 0);
            $locationId  = (int)($_POST['location_id'] ?? 0);

            $adjustmentType =
                strtoupper(
                    trim($_POST['adjustment_type'] ?? '')
                );

            $quantity =
                (float)($_POST['quantity'] ?? 0);

            $reason =
                strtoupper(
                    trim($_POST['reason'] ?? '')
                );

            $notes =
                trim($_POST['notes'] ?? '');

            /*
            |--------------------------------------------------------------------------
            | BASIC VALIDATION
            |--------------------------------------------------------------------------
            */

            if ($inventoryId <= 0) {
                FlashHelper::error(
                    'Please select an inventory item.'
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/stockadjustments/create'
                );

                exit;
            }

            if ($locationId <= 0) {
                FlashHelper::error(
                    'Please select a location.'
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/stockadjustments/create'
                );

                exit;
            }

            if (
                !in_array(
                    $adjustmentType,
                    ['INCREASE', 'DECREASE'],
                    true
                )
            ) {
                FlashHelper::error(
                    'Invalid adjustment type.'
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/stockadjustments/create'
                );

                exit;
            }

            if ($quantity <= 0) {
                FlashHelper::error(
                    'Adjustment quantity must be greater than zero.'
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/stockadjustments/create'
                );

                exit;
            }

            $allowedReasons = [
                'DAMAGED',
                'BROKEN',
                'LOST',
                'FOUND',
                'PHYSICAL_COUNT_CORRECTION',
                'EXPIRED',
                'OTHER'
            ];

            if (!in_array($reason, $allowedReasons, true)) {
                FlashHelper::error(
                    'Please select a valid adjustment reason.'
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/stockadjustments/create'
                );

                exit;
            }

            if (
                $reason === 'OTHER' &&
                $notes === ''
            ) {
                FlashHelper::error(
                    'Please provide notes when the reason is OTHER.'
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/stockadjustments/create'
                );

                exit;
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE SIGNED DELTA
            |--------------------------------------------------------------------------
            */

            $delta =
                $adjustmentType === 'INCREASE'
                ? $quantity
                : -$quantity;

            /*
            |--------------------------------------------------------------------------
            | REFERENCE
            |--------------------------------------------------------------------------
            |
            | Automatically generated.
            |
            */

            $reference =
                'ADJ-' .
                date('ymdHis');

            /*
            |--------------------------------------------------------------------------
            | EXECUTE ADJUSTMENT
            |--------------------------------------------------------------------------
            */

            $service = new InventoryService(

                $this->model('InventoryLocationStock'),

                $this->model('InventoryMovement'),

                $this->model('InventoryTransfer')
            );

            try {

                $service->adjust([

                    'inventory_id' =>
                    $inventoryId,

                    'location_id' =>
                    $locationId,

                    'delta' =>
                    $delta,

                    'reference' =>
                    $reference,

                    'notes' =>
                    $reason .
                        (
                            $notes !== ''
                            ? ' - ' . $notes
                            : ''
                        ),

                    'created_by' =>
                    $_SESSION['user_id'] ?? null

                ]);

                FlashHelper::success(
                    'Stock adjustment posted successfully. ' .
                        'Reference: ' . $reference
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/inventory'
                );

                exit;
            } catch (Throwable $e) {

                FlashHelper::error(
                    $e->getMessage()
                );

                header(
                    'Location: ' .
                        URLROOT .
                        '/stockadjustments/create'
                );

                exit;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD FORM DATA
        |--------------------------------------------------------------------------
        */

        $data['inventory'] =
            $inventoryModel->getAll();

        $data['locations'] =
            $locationModel->getAll();

        $data['selected_inventory_id'] =
            (int)($inventoryId ?? 0);

        $this->view(
            'stock-adjustments/create',
            $data
        );
    }

    public function getLocationStock()
    {
        AuthHelper::can('inventory.adjustment.create');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        header('Content-Type: application/json');

        $inventoryId =
            (int)($_POST['inventory_id'] ?? 0);

        $locationId =
            (int)($_POST['location_id'] ?? 0);

        if (
            $inventoryId <= 0 ||
            $locationId <= 0
        ) {
            echo json_encode([
                'physical_qty' => 0,
                'reserved_qty' => 0,
                'available_qty' => 0
            ]);

            return;
        }

        $stockModel =
            $this->model('InventoryLocationStock');

        $reservationModel =
            $this->model('InventoryReservation');

        $stock =
            $stockModel->getStock(
                $inventoryId,
                $locationId
            );

        $physicalQty =
            (float)($stock->quantity ?? 0);

        $reservedQty =
            (float)$reservationModel
                ->getReservedQuantity(
                    $inventoryId,
                    $locationId
                );

        $availableQty =
            max(
                0,
                $physicalQty - $reservedQty
            );

        echo json_encode([

            'physical_qty' =>
            $physicalQty,

            'reserved_qty' =>
            $reservedQty,

            'available_qty' =>
            $availableQty

        ]);
    }
}
