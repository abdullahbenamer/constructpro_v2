<?php
require_once '../app/Core/Model.php';
require_once '../app/Models/InventoryModel.php';

class InventoryMovementModel extends Model
{

    public function addMovement($data)
    {
        $locationStockModel = new InventoryLocationStockModel($this->db);
        // =========================
        // CLEAN INPUT
        // =========================

        $inventory_id = (int)$data['inventory_id'];
        $type         = strtoupper(trim($data['type']));
        $quantity     = (float)$data['quantity'];
        $location_id  = (int)($data['location_id'] ?? 0);
        $supplier_id = $data['supplier_id'] ?? null;
        $reference = trim($data['reference'] ?? '');
        $notes     = trim($data['notes'] ?? '');
        $created_by = $_SESSION['user_id'] ?? null;

        // =========================
        // VALIDATION
        // =========================

        if ($inventory_id <= 0) {
            die("Invalid inventory item");
        }

        if ($location_id <= 0) {
            die("Invalid location");
        }

        if ($type === 'ADJUSTMENT') {

            if ($quantity == 0) {
                die("Invalid adjustment quantity");
            }
        } elseif ($quantity <= 0) {

            die("Invalid quantity");
        }

        if (!in_array($type, ['IN', 'OUT', 'ADJUSTMENT'])) {
            die("Invalid movement type");
        }

        /*
|--------------------------------------------------------------------------
| Balance After Movement
|--------------------------------------------------------------------------
| Stock has already been updated by the Inventory Engine.
| Therefore we simply read the current location balance.
|--------------------------------------------------------------------------
*/

        $stock = $locationStockModel->getStock(
            $inventory_id,
            $location_id
        );

        $new_balance = (float)($stock->quantity ?? 0);


        /*
|--------------------------------------------------------------------------
| Global Inventory Balance After Movement
|--------------------------------------------------------------------------
| Global inventory has also already been updated by the
| Inventory Engine.
| Therefore we simply read the current global balance.
|--------------------------------------------------------------------------
*/

        $inventoryModel = new InventoryModel($this->db);

        $inventory = $inventoryModel->getById($inventory_id);

        if (!$inventory) {
            throw new Exception(
                'Inventory item not found.'
            );
        }

        $global_balance_after =
            (float)$inventory->quantity;

               // =========================
        // SAVE MOVEMENT
        // =========================

        $this->db->query(
            "INSERT INTO inventory_movements    
    (
        inventory_id,
        location_id,
        type,
        quantity,
        supplier_id,
        balance_after,
        global_balance_after,
        reference,
        notes,
        created_by
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $inventory_id,
                $location_id,
                $type,
                $quantity,
                $supplier_id,
                $new_balance,
                $global_balance_after,
                $reference ?: null,
                $notes ?: null,
                $created_by
            ]
        );

        return true;
    }

    public function getMovements($inventory_id)
    {
        return $this->db->query(
            "SELECT * FROM inventory_movements 
             WHERE inventory_id = ? 
             ORDER BY created_at DESC",
            [$inventory_id]
        )->fetchAll();
    }

    public function getTotalStock($inventory_id)
    {
        $in = $this->db->query(
            "SELECT SUM(quantity) as total FROM inventory_movements 
             WHERE inventory_id = ? AND type = 'IN'",
            [$inventory_id]
        )->fetch()->total ?? 0;

        $out = $this->db->query(
            "SELECT SUM(quantity) as total FROM inventory_movements 
             WHERE inventory_id = ? AND type = 'OUT'",
            [$inventory_id]
        )->fetch()->total ?? 0;

        return $in - $out;
    }

    public function getAllMovements()
    {
        return $this->db->query("
        SELECT 
            im.*,

            i.name AS item_name,

            u.full_name AS user_name,

            l.code AS location_code,
            l.name AS location_name

        FROM inventory_movements im

        LEFT JOIN inventory i
            ON i.id = im.inventory_id

        LEFT JOIN users u
            ON u.id = im.created_by

        LEFT JOIN inventory_locations l
            ON l.id = im.location_id

        ORDER BY im.created_at DESC

    ")->fetchAll();
    }

    public function getMovementsDetailed($inventory_id)
    {
        return $this->db->query(
            "
        SELECT
            im.*,
            s.company_name AS supplier_name

        FROM inventory_movements im

        LEFT JOIN suppliers s
            ON s.id = im.supplier_id

        WHERE im.inventory_id = ?

        ORDER BY im.created_at DESC
        ",
            [$inventory_id]
        )->fetchAll();
    }

    public function getOpenPurchaseOrders()
    {
        return $this->db->query("
        SELECT
            po.*,
            s.company_name

        FROM purchase_orders po

        LEFT JOIN suppliers s
            ON s.id = po.supplier_id

        WHERE po.status IN
        (
            'approved',
            'partial'
        )

        ORDER BY po.id DESC
    ")->fetchAll();
    }

    public function receiveGoods($data)
    {
        return $this->addMovement([

            'inventory_id' => $data['inventory_id'],

            'location_id'  => $data['location_id'],

            'type' => 'IN',

            'quantity' => $data['quantity'],

            'unit_cost' => $data['unit_cost'],

            'supplier_id' => $data['supplier_id'],

            'reference' => $data['reference'],

            'notes' => $data['notes'],

            'movement_by' => $_SESSION['user_id']

        ]);
    }
}
