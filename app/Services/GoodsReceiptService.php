<?php

require_once '../app/Services/BaseService.php';

class GoodsReceiptService extends BaseService
{
    private PurchaseOrderModel $poModel;
    private GoodsReceiptModel $grnModel;
    private GoodsReceiptItemModel $grnItemModel;
    private InventoryService $inventoryService;
    private SupplierLedgerModel $ledgerModel;

    public function __construct(
        PurchaseOrderModel $poModel,
        GoodsReceiptModel $grnModel,
        GoodsReceiptItemModel $grnItemModel,
        SupplierLedgerModel $ledgerModel,
        InventoryService $inventoryService
    ) {
        parent::__construct();

        $this->poModel           = $poModel;
        $this->grnModel          = $grnModel;
        $this->grnItemModel      = $grnItemModel;
        $this->ledgerModel       = $ledgerModel;
        $this->inventoryService  = $inventoryService;
    }

    /**
     * ERP Goods Receipt Workflow
     */
    public function receive(array $data): int
    {
        return $this->transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | 1. VALIDATE INPUT
            |--------------------------------------------------------------------------
            */

            $poId        = (int)($data['po_id'] ?? 0);
            $supplierId  = (int)($data['supplier_id'] ?? 0);
            $inventoryId = (int)($data['inventory_id'] ?? 0);
            $locationId  = (int)($data['location_id'] ?? 0);
            $quantity    = (float)($data['quantity'] ?? 0);
            $unitCost    = (float)($data['unit_cost'] ?? 0);

            if ($poId <= 0) {
                throw new Exception('Invalid purchase order.');
            }

            if ($supplierId <= 0) {
                throw new Exception('Invalid supplier.');
            }

            if ($inventoryId <= 0) {
                throw new Exception('Invalid inventory item.');
            }

            if ($locationId <= 0) {
                throw new Exception('Invalid warehouse location.');
            }

            if ($quantity <= 0) {
                throw new Exception(
                    'Received quantity must be greater than zero.'
                );
            }

            if ($unitCost < 0) {
                throw new Exception(
                    'Unit cost cannot be negative.'
                );
            }

            /*
|--------------------------------------------------------------------------
| 2. VALIDATE PURCHASE ORDER
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
| PO must be available for receiving
|--------------------------------------------------------------------------
|
| draft     = not approved yet
| approved  = available
| partial   = available
| received  = fully received
| cancelled = cancelled
|
*/

            if (!in_array(
                $po->status,
                ['approved', 'partial'],
                true
            )) {
                throw new Exception(
                    'This Purchase Order is not available for receiving.'
                );
            }

            if ($supplierId !== (int)$po->supplier_id) {
                throw new Exception(
                    'The selected supplier does not match the Purchase Order supplier.'
                );
            }


            /*
|--------------------------------------------------------------------------
| 3. VALIDATE PO ITEM
|--------------------------------------------------------------------------
*/

            $poItem = $this->poModel->getPOItem(
                $poId,
                $inventoryId
            );

            if (!$poItem) {
                throw new Exception(
                    'The selected inventory item does not belong to this Purchase Order.'
                );
            }


            /*
|--------------------------------------------------------------------------
| 4. VALIDATE REMAINING QUANTITY
|--------------------------------------------------------------------------
*/

            $orderedQuantity =
                (float)$poItem->quantity;

            $receivedQuantity =
                (float)($poItem->received_quantity ?? 0);

            $remainingQuantity =
                $orderedQuantity - $receivedQuantity;


            if ($remainingQuantity <= 0) {

                throw new Exception(
                    'This PO item has already been fully received.'
                );
            }


            if ($quantity > $remainingQuantity) {

                throw new Exception(
                    'Cannot receive ' .
                        number_format($quantity, 2) .
                        ' units. Only ' .
                        number_format($remainingQuantity, 2) .
                        ' units remain on the purchase order.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 5. CALCULATE TOTAL
            |--------------------------------------------------------------------------
            */

            $total = $quantity * $unitCost;

            /*
            |--------------------------------------------------------------------------
            | 6. CREATE GRN HEADER
            |--------------------------------------------------------------------------
            */

            $grnId = $this->grnModel->create([

                'grn_number' =>
                $this->grnModel->nextNumber(),

                'purchase_order_id' =>
                $poId,

                'supplier_id' =>
                $supplierId,

                'receipt_date' =>
                date('Y-m-d'),

                'subtotal' =>
                $total,

                'total_amount' =>
                $total,

                'remarks' =>
                $data['notes'] ?? ''

            ]);

            /*
            |--------------------------------------------------------------------------
            | 7. CREATE GRN ITEM
            |--------------------------------------------------------------------------
            */

            $this->grnItemModel->create([

                'goods_receipt_id' =>
                $grnId,

                'purchase_order_item_id' =>
                $poItem->id,

                'inventory_id' =>
                $inventoryId,

                'location_id' =>
                $locationId,

                'quantity' =>
                $quantity,

                'unit_cost' =>
                $unitCost,

                'total_cost' =>
                $total
            ]);

            /*
            |--------------------------------------------------------------------------
            | 8. RECEIVE INVENTORY
            |--------------------------------------------------------------------------
            |
            | InventoryService is responsible for:
            |
            |   Warehouse stock +
            |   Global inventory synchronization +
            |   IN movement
            |
            */

            $this->inventoryService->receive([

                'inventory_id' =>
                $inventoryId,

                'location_id' =>
                $locationId,

                'quantity' =>
                $quantity,

                'unit_cost' =>
                $unitCost,

                'supplier_id' =>
                $supplierId,

                'reference' =>
                'GRN-' . $grnId . ' / PO-' . $po->po_number,

                'notes' =>
                $data['notes'] ?? '',

                'created_by' =>
                $_SESSION['user_id'] ?? null

            ]);

            /*
            |--------------------------------------------------------------------------
            | 9. UPDATE PO RECEIVED QUANTITY
            |--------------------------------------------------------------------------
            */

            $this->poModel->receiveItem(
                $poId,
                $inventoryId,
                $quantity
            );

            /*
            |--------------------------------------------------------------------------
            | 10. UPDATE PO RECEIVING STATUS
            |--------------------------------------------------------------------------
            */

            $this->poModel->updateReceivingStatus(
                $poId
            );

            /*
            |--------------------------------------------------------------------------
            | 11. SUPPLIER LEDGER
            |--------------------------------------------------------------------------
            */

            $this->ledgerModel->add([

                'supplier_id' =>
                $supplierId,

                'type' =>
                'GRN',

                'reference_type' =>
                'GoodsReceipt',

                'reference_id' =>
                $grnId,

                'amount' =>
                $total,

                'direction' =>
                'DEBIT'

            ]);

            /*
            |--------------------------------------------------------------------------
            | 12. RETURN GRN ID
            |--------------------------------------------------------------------------
            */

            return $grnId;
        });
    }
}
