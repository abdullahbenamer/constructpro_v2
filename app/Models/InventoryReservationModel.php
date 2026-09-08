<?php
require_once '../app/Core/Model.php';

class InventoryReservationModel extends Model
{
   public function create($data)
{
    $inventory_id = (int)$data['inventory_id'];
    $location_id  = (int)$data['location_id'];
    $quantity     = (float)$data['quantity'];

    /*
    |--------------------------------------------------------------------------
    | VALIDATE QUANTITY
    |--------------------------------------------------------------------------
    */

    if ($quantity <= 0) {
        throw new Exception(
            'Reservation quantity must be greater than zero.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET PHYSICAL STOCK
    |--------------------------------------------------------------------------
    */

    $stockModel =
        new InventoryLocationStockModel();

    $stock = $stockModel->getStock(
        $inventory_id,
        $location_id
    );

    $physicalQty =
        (float)($stock->quantity ?? 0);


    /*
    |--------------------------------------------------------------------------
    | GET ALREADY RESERVED QUANTITY
    |--------------------------------------------------------------------------
    */

    $reservedQty =
        $this->getReservedQuantity(
            $inventory_id,
            $location_id
        );


    /*
    |--------------------------------------------------------------------------
    | CALCULATE AVAILABLE QUANTITY
    |--------------------------------------------------------------------------
    */

    $availableQty =
        $physicalQty - $reservedQty;


    /*
    |--------------------------------------------------------------------------
    | PREVENT OVER-RESERVATION
    |--------------------------------------------------------------------------
    */

    if ($quantity > $availableQty) {

        throw new Exception(
            'Insufficient available stock. '
            . 'Available to reserve: '
            . number_format(
                max(0, $availableQty),
                2
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE RESERVATION
    |--------------------------------------------------------------------------
    */

    return $this->db->query(
        "
        INSERT INTO inventory_reservations
        (
            inventory_id,
            location_id,
            project_id,
            required_by_date,
            quantity,
            reference,
            notes,
            created_by
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ",
        [
            $inventory_id,
            $location_id,
            $data['project_id'],
            $data['required_by_date'],
            $quantity,
            $data['reference'] ?? null,
            $data['notes'] ?? null,
            $_SESSION['user_id'] ?? null
        ]
    );
}

    public function getActiveReservedQty($inventory_id)
    {
        $result = $this->db->query(
            "
            SELECT COALESCE(SUM(quantity),0) AS total

            FROM inventory_reservations

            WHERE inventory_id = ?
            AND status = 'ACTIVE'
            ",
            [$inventory_id]
        )->fetch();

        return (float)$result->total;
    }

    public function getAll()
    {
        return $this->db->query(
            "
           SELECT
    r.*,

    i.name AS item_name,
    
    i.sku,

    p.title AS project_name,

    u.full_name AS created_by_name

FROM inventory_reservations r

LEFT JOIN inventory i
    ON r.inventory_id = i.id

LEFT JOIN projects p
    ON r.project_id = p.id

LEFT JOIN users u
    ON r.created_by = u.id

ORDER BY r.created_at DESC
            "
        )->fetchAll();
    }

    public function fulfill($id)
    {
        // Get reservation
        $reservation = $this->db->query(
    
            "
            SELECT *
            FROM inventory_reservations
            WHERE id = ?
            LIMIT 1
            ",
            [$id]
    
        )->fetch();
    
        if (!$reservation) {
            return false;
        }
    
        // Already processed
        if ($reservation->status !== 'ACTIVE') {
            return false;
        }
    
        // Reduce physical stock
        $updated = $this->db->query(
    
            "
            UPDATE inventory
    
            SET quantity = quantity - ?
    
            WHERE id = ?
            AND quantity >= ?
            ",
            [
                $reservation->quantity,
                $reservation->inventory_id,
                $reservation->quantity
            ]
    
        );
    
        // If stock update failed
        if ($updated->rowCount() <= 0) {
            return false;
        }
    
        // Mark reservation fulfilled
        return $this->db->query(
    
            "
            UPDATE inventory_reservations
    
            SET status = 'FULFILLED'
    
            WHERE id = ?
            ",
            [$id]
    
        );
    }

    public function markFulfilled(int $id): bool
{
    $result = $this->db->query(
        "
        UPDATE inventory_reservations

        SET status = 'FULFILLED'

        WHERE id = ?

        AND status = 'ACTIVE'
        ",
        [$id]
    );

    return $result->rowCount() > 0;
}

    public function cancel($id)
    {
        return $this->db->query(
            "
            UPDATE inventory_reservations
            SET status = 'CANCELLED'
            WHERE id = ?
            ",
            [$id]
        );
    }

    public function getById($id)
{
    return $this->db->query(

        "
        SELECT *
        FROM inventory_reservations
        WHERE id = ?
        LIMIT 1
        ",
        [$id]

    )->fetch();
}

public function update($id, $data)
{
    return $this->db->query(

        "
        UPDATE inventory_reservations

        SET
            inventory_id = ?,
            location_id = ?,
            project_id = ?,
            quantity = ?,
            reference = ?,
            notes = ?

        WHERE id = ?
        ",
        [
            $data['inventory_id'],
            $data['location_id'],
            $data['project_id'],
            $data['quantity'],
            $data['reference'],
            $data['notes'],
            $id
        ]

    );
}

public function delete($id)
{
    return $this->db->query(

        "
        DELETE FROM inventory_reservations
        WHERE id = ?
        ",
        [$id]

    );
}

public function getReservedQuantity(
    int $inventory_id,
    int $location_id
): float {

    $result = $this->db->query(
        "
        SELECT
            COALESCE(SUM(quantity), 0) AS reserved_qty

        FROM inventory_reservations

        WHERE inventory_id = ?

        AND location_id = ?

        AND status = 'ACTIVE'
        ",
        [
            $inventory_id,
            $location_id
        ]
    )->fetch();

    return (float)(
        $result->reserved_qty ?? 0
    );
}

}