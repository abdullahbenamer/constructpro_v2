<?php
require_once '../app/Services/BaseService.php';

class InventoryService extends BaseService
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
 * Receive stock into a warehouse.
 */

public function receive(array $data): bool
{
    $inventoryId = (int)($data['inventory_id'] ?? 0);
    $locationId  = (int)($data['location_id'] ?? 0);
    $quantity    = (float)($data['quantity'] ?? 0);
    $unitCost    = (float)($data['unit_cost'] ?? 0);

    if ($inventoryId <= 0) {
        throw new Exception('Invalid inventory item.');
    }

    if ($locationId <= 0) {
        throw new Exception('Invalid warehouse location.');
    }

    if ($quantity <= 0) {
        throw new Exception('Invalid quantity.');
    }

    if ($unitCost < 0) {
        throw new Exception('Unit cost cannot be negative.');
    }

    /*
    |--------------------------------------------------------------------------
    | RECEIVE PHYSICAL STOCK
    |--------------------------------------------------------------------------
    */

    $success = $this->stockModel->adjustStock(
        $inventoryId,
        $locationId,
        $quantity
    );

    if (!$success) {
        throw new Exception(
            'Unable to add stock to the warehouse.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE CURRENT INVENTORY COST
    |--------------------------------------------------------------------------
    |
    | The latest actual received purchase cost becomes the
    | current cost_price for future inventory usage.
    |
    */

    $this->db->query(
        "UPDATE inventory
         SET cost_price = ?
         WHERE id = ?",
        [
            $unitCost,
            $inventoryId
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | RECORD INVENTORY MOVEMENT
    |--------------------------------------------------------------------------
    */

    $this->movementModel->addMovement([
        'inventory_id' => $inventoryId,
        'location_id'  => $locationId,
        'type'         => 'IN',
        'quantity'     => $quantity,
        'unit_cost'    => $unitCost,
        'supplier_id'  => $data['supplier_id'] ?? null,
        'reference'    => $data['reference'] ?? null,
        'notes'        => $data['notes'] ?? '',
        'created_by'   => $data['created_by']
            ?? $_SESSION['user_id']
            ?? null
    ]);

    return true;
}

    /**
     * Issue stock from a warehouse.
     */
  /**
 * Issue stock from a warehouse.
 */
public function issue(array $data): bool
{
    return $this->transaction(function () use ($data) {

        $inventoryId = (int)($data['inventory_id'] ?? 0);
        $locationId  = (int)($data['location_id'] ?? 0);
        $quantity    = (float)($data['quantity'] ?? 0);

        if ($inventoryId <= 0) {
            throw new Exception('Invalid inventory item.');
        }

        if ($locationId <= 0) {
            throw new Exception('Invalid warehouse location.');
        }

        if ($quantity <= 0) {
            throw new Exception('Invalid quantity.');
        }

        /*
        |--------------------------------------------------------------------------
        | REMOVE PHYSICAL STOCK
        |--------------------------------------------------------------------------
        */

        $success = $this->stockModel->adjustStock(
            $inventoryId,
            $locationId,
            -$quantity
        );

        if (!$success) {
            throw new Exception(
                'Not enough stock in the selected warehouse.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RECORD MOVEMENT
        |--------------------------------------------------------------------------
        */

        $this->movementModel->addMovement([

            'inventory_id' => $inventoryId,

            'location_id' => $locationId,

            'type' => 'OUT',

            'quantity' => $quantity,

            'unit_cost' => (float)($data['unit_cost'] ?? 0),

            'supplier_id' => $data['supplier_id'] ?? null,

            'reference' => $data['reference'] ?? null,

            'notes' => $data['notes'] ?? '',

            'created_by' =>
                $data['created_by']
                ?? $this->currentUserId()

        ]);

        return true;
    });
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

    /**
 * Inventory adjustment.
 *
 * Positive quantity = increase stock
 * Negative quantity = decrease stock
 */
public function adjust(array $data): bool
{
    return $this->transaction(function () use ($data) {

        $inventoryId = (int)($data['inventory_id'] ?? 0);
        $locationId  = (int)($data['location_id'] ?? 0);
        $delta       = (float)($data['delta'] ?? 0);

        if ($inventoryId <= 0) {
            throw new Exception('Invalid inventory item.');
        }

        if ($locationId <= 0) {
            throw new Exception('Invalid warehouse location.');
        }

        if ($delta == 0) {
            throw new Exception(
                'Adjustment quantity cannot be zero.'
            );
        }

        /*
|--------------------------------------------------------------------------
| CHECK ACTIVE RESERVATION
|--------------------------------------------------------------------------
| A stock decrease can Not consume quantities already reserved
| for active reservations at this location.
|--------------------------------------------------------------------------
*/

if ($delta < 0) {

    $reservationModel =
        new InventoryReservationModel();

    $reservedQty =
        $reservationModel->getReservedQuantity(
            $inventoryId,
            $locationId
        );

    $stock =
        $this->stockModel->getStock(
            $inventoryId,
            $locationId
        );

    $physicalQty =
        (float)($stock->quantity ?? 0);

    $availableQty =
        $physicalQty - $reservedQty;

    if (abs($delta) > $availableQty) {

        throw new Exception(
            'Adjustment would exceed available stock. '
            . 'Available after reservations: '
            . number_format(
                max(0, $availableQty),
                2
            )
        );
    }
}
        /*
        |--------------------------------------------------------------------------
        | ADJUST PHYSICAL STOCK
        |--------------------------------------------------------------------------
        */

        $success = $this->stockModel->adjustStock(
            $inventoryId,
            $locationId,
            $delta
        );

        if (!$success) {

            if ($delta < 0) {
                throw new Exception(
                    'Adjustment would result in insufficient stock.'
                );
            }

            throw new Exception(
                'Unable to adjust inventory stock.'
            );
        }

       
  /*
|--------------------------------------------------------------------------
| RECORD MOVEMENT
|--------------------------------------------------------------------------
*/

$this->movementModel->addMovement([

    'inventory_id' => $inventoryId,

    'location_id' => $locationId,

    'type' => 'ADJUSTMENT',

    'quantity' => $delta,

    'unit_cost' => (float)($data['unit_cost'] ?? 0),

    'supplier_id' => $data['supplier_id'] ?? null,

    'reference' => $data['reference'] ?? null,

    'notes' => $data['notes'] ?? 'Inventory adjustment',

    'created_by' =>
        $data['created_by']
        ?? $this->currentUserId()

]);

        return true;
    });
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
