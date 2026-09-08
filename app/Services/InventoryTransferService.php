<?php
 require_once '../app/Core/Model.php';
class InventoryTransferService extends Model
{
    private InventoryLocationStockModel $stockModel;
    private InventoryMovementModel $movementModel;
    private InventoryTransferModel $transferModel;

    public function __construct(
        InventoryLocationStockModel $stockModel,
        InventoryMovementModel $movementModel,
        InventoryTransferModel $transferModel
    ) {
        parent::__construct();

        $this->stockModel = $stockModel;
        $this->movementModel = $movementModel;
        $this->transferModel = $transferModel;
    }

       /**
     * Transfer stock between warehouses.
     */
  public function transfer(array $data): int
{
    $this->db->beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | 1. Validation
        |--------------------------------------------------------------------------
        */

        if ($data['quantity'] <= 0) {
            throw new Exception('Invalid quantity.');
        }

        if ($data['from_location_id'] == $data['to_location_id']) {
            throw new Exception(
                'Source and destination warehouses cannot be the same.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Move Stock
        |--------------------------------------------------------------------------
        */

        $ok = $this->stockModel->transferStock(
            $data['inventory_id'],
            $data['from_location_id'],
            $data['to_location_id'],
            $data['quantity']
        );

        if (!$ok) {
            throw new Exception(
                'Not enough stock in source warehouse.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Save Transfer
        |--------------------------------------------------------------------------
        */

        $transferId = $this->transferModel->create($data);

        /*
        |--------------------------------------------------------------------------
        | 4. OUT Movement
        |--------------------------------------------------------------------------
        */

        $this->movementModel->addMovement([
            'inventory_id' => $data['inventory_id'],
            'location_id'  => $data['from_location_id'],
            'type'         => 'OUT',
            'quantity'     => $data['quantity'],
            'reference'    => $data['reference'],
            'notes'        => 'Warehouse Transfer #' . $transferId,
            'created_by'   => $data['created_by']
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. IN Movement
        |--------------------------------------------------------------------------
        */

        $this->movementModel->addMovement([
            'inventory_id' => $data['inventory_id'],
            'location_id'  => $data['to_location_id'],
            'type'         => 'IN',
            'quantity'     => $data['quantity'],
            'reference'    => $data['reference'],
            'notes'        => 'Warehouse Transfer #' . $transferId,
            'created_by'   => $data['created_by']
        ]);

        $this->db->commit();

        return $transferId;

    } catch (Throwable $e) {

        $this->db->rollBack();

        throw $e;
    }
}

public function reverse(int $transferId): array
{
    $this->db->beginTransaction();

    try {

        /*
        |------------------------------------------------------------------
        | 1. GET ORIGINAL TRANSFER
        |------------------------------------------------------------------
        */

        $transfer = $this->transferModel->getById($transferId);

        if (!$transfer) {
            throw new Exception(
                'Transfer not found.'
            );
        }


        /*
        |------------------------------------------------------------------
        | 2. VALIDATE STATUS
        |------------------------------------------------------------------
        */

        if ($transfer->status !== 'COMPLETED') {
            throw new Exception(
                'Only COMPLETED transfers can be reversed.'
            );
        }


        /*
        |------------------------------------------------------------------
        | 3. REVERSE THE PHYSICAL STOCK
        |
        | Original:
        | FROM → TO
        |
        | Reversal:
        | TO → FROM
        |------------------------------------------------------------------
        */

        $ok = $this->stockModel->transferStock(
            $transfer->inventory_id,
            $transfer->to_location_id,
            $transfer->from_location_id,
            $transfer->quantity
        );

        if (!$ok) {
            throw new Exception(
                'Unable to reverse transfer. '
                . 'Insufficient stock at destination location.'
            );
        }


        /*
        |------------------------------------------------------------------
        | 4. CREATE REVERSAL TRANSFER RECORD
        |------------------------------------------------------------------
        */

        $reversalId = $this->transferModel->create([

            'inventory_id' =>
                $transfer->inventory_id,

            'from_location_id' =>
                $transfer->to_location_id,

            'to_location_id' =>
                $transfer->from_location_id,

            'quantity' =>
                $transfer->quantity,

            'reference' =>
                $transfer->reference,

            'notes' =>
                'Reversal of Transfer #'
                . $transferId,

            'created_by' =>
                $_SESSION['user_id'] ?? null

        ]);


        /*
        |------------------------------------------------------------------
        | 5. CREATE OUT MOVEMENT
        |
        | Reversal takes stock OUT from the original destination.
        |------------------------------------------------------------------
        */

        $this->movementModel->addMovement([

            'inventory_id' =>
                $transfer->inventory_id,

            'location_id' =>
                $transfer->to_location_id,

            'type' =>
                'OUT',

            'quantity' =>
                $transfer->quantity,

            'reference' =>
                $transfer->reference,

            'notes' =>
                'Reversal of Transfer #'
                . $transferId,

            'created_by' =>
                $_SESSION['user_id'] ?? null

        ]);


        /*
        |------------------------------------------------------------------
        | 6. CREATE IN MOVEMENT
        |
        | Reversal puts stock back into the original source.
        |------------------------------------------------------------------
        */

        $this->movementModel->addMovement([

            'inventory_id' =>
                $transfer->inventory_id,

            'location_id' =>
                $transfer->from_location_id,

            'type' =>
                'IN',

            'quantity' =>
                $transfer->quantity,

            'reference' =>
                $transfer->reference,

            'notes' =>
                'Reversal of Transfer #'
                . $transferId,

            'created_by' =>
                $_SESSION['user_id'] ?? null

        ]);


        /*
        |------------------------------------------------------------------
        | 7. MARK ORIGINAL TRANSFER AS REVERSED
        |------------------------------------------------------------------
        */

        $this->db->query(
            "
            UPDATE inventory_transfers

            SET
                status = 'REVERSED',
                reversed_at = NOW(),
                reversed_by = ?,
                reversal_transfer_id = ?

            WHERE id = ?
            AND status = 'COMPLETED'
            ",
            [
                $_SESSION['user_id'] ?? null,
                $reversalId,
                $transferId
            ]
        );


        /*
        |------------------------------------------------------------------
        | 8. COMMIT
        |------------------------------------------------------------------
        */

        $this->db->commit();


        return [
            'success' => true,
            'message' =>
                'Transfer reversed successfully.',
            'reversal_transfer_id' =>
                $reversalId
        ];


    } catch (Throwable $e) {

        $this->db->rollBack();

        throw $e;
    }
}

       /**
     * Current available quantity.
     */
    public function available(
        int $inventory_id,
        int $location_id
    ): float {
        $stock = $this->stockModel->getStock(
            $inventory_id,
            $location_id
        );

        return (float)($stock->quantity ?? 0);
    }
}
