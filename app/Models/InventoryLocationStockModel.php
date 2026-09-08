<?php
require_once '../app/Core/Model.php';

class InventoryLocationStockModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GET ITEM STOCK BY LOCATION
    |--------------------------------------------------------------------------
    */

    public function getItemLocations($inventory_id)
    {
        return $this->db->query(
            "SELECT
                ils.*,
                l.code,
                l.name
            FROM inventory_location_stock ils
            JOIN inventory_locations l
                ON l.id = ils.location_id
            WHERE ils.inventory_id = ?
            ORDER BY l.code",
            [$inventory_id]
        )->fetchAll();
    }

/*
|--------------------------------------------------------------------------
| GET ALL LOCATIONS WITH CURRENT STOCK FOR AN ITEM
|--------------------------------------------------------------------------
| Used by Goods Return.
|
| Returns every warehouse, including warehouses where the item
| does not yet have an inventory_location_stock row.
|--------------------------------------------------------------------------
*/

public function getItemLocationsForReturn($inventory_id)
{
    return $this->db->query(
        "
        SELECT
            l.id AS location_id,
            l.code,
            l.name,
            COALESCE(ils.quantity, 0) AS quantity

        FROM inventory_locations l

        LEFT JOIN inventory_location_stock ils
            ON ils.location_id = l.id
            AND ils.inventory_id = ?

        ORDER BY
            l.code
        ",
        [$inventory_id]
    )->fetchAll();
}
 /*
    |--------------------------------------------------------------------------
    | GET ITEM STOCK BY LOCATION for TRANSFER Logic Only
    |--------------------------------------------------------------------------
    */
    public function getAvailableItemLocations($inventory_id)
{
    return $this->db->query(
        "
        SELECT
            ils.*,
            l.code,
            l.name

        FROM inventory_location_stock ils

        JOIN inventory_locations l
            ON l.id = ils.location_id

        WHERE ils.inventory_id = ?
        AND ils.quantity > 0

        ORDER BY ils.quantity DESC
        ",
        [$inventory_id]
    )->fetchAll();
}

       /*
    |--------------------------------------------------------------------------
    | ADD STOCK TO LOCATION
    |--------------------------------------------------------------------------
    */
 // for initial stock when creating inventory item
    public function createInitialStock(
    $inventory_id,
    $location_id,
    $qty
)
{
    return $this->db->query(
        "INSERT INTO inventory_location_stock
        (
            inventory_id,
            location_id,
            quantity
        )
        VALUES (?, ?, ?)",
        [
            $inventory_id,
            $location_id,
            $qty
        ]
    );
}

public function addStock($inventory_id, $location_id, $qty)
{
    return $this->adjustStock(
        $inventory_id,
        $location_id,
        $qty
    );
}

    /*
    |--------------------------------------------------------------------------
    | REMOVE STOCK FROM LOCATION
    |--------------------------------------------------------------------------
    */

public function removeStock($inventory_id, $location_id, $qty)
{
    return $this->adjustStock(
        $inventory_id,
        $location_id,
        -$qty
    );
}

    /*
    |--------------------------------------------------------------------------
    | RECALCULATE MASTER STOCK
    |--------------------------------------------------------------------------
    */

    public function syncInventoryQuantity($inventory_id)
    {
        $result = $this->db->query(
            "SELECT COALESCE(SUM(quantity),0) as total
             FROM inventory_location_stock
             WHERE inventory_id = ?",
            [$inventory_id]
        )->fetch();

        $total = (float)$result->total;

        $this->db->query(
            "UPDATE inventory
             SET quantity = ?
             WHERE id = ?",
            [$total, $inventory_id]
        );

        return $total;
    }

public function getLocationInventory($location_id)
{
    return $this->db->query(
        "
        SELECT
            ils.quantity,

            i.id,
            i.name,
            i.sku,
            i.base_unit,
            i.min_stock,

            COALESCE(
                reservations.reserved_quantity,
                0
            ) AS reserved_quantity,

            (
                ils.quantity
                -
                COALESCE(
                    reservations.reserved_quantity,
                    0
                )
            ) AS available_quantity

        FROM inventory_location_stock ils

        JOIN inventory i
            ON i.id = ils.inventory_id

        LEFT JOIN
        (
            SELECT
                inventory_id,
                location_id,

                SUM(quantity) AS reserved_quantity

            FROM inventory_reservations

            WHERE status = 'ACTIVE'

            GROUP BY
                inventory_id,
                location_id

        ) reservations

            ON reservations.inventory_id = ils.inventory_id

            AND reservations.location_id = ils.location_id

        WHERE ils.location_id = ?

        ORDER BY i.name
        ",
        [$location_id]
    )->fetchAll();
}

/*
    |--------------------------------------------------------------------------
    | GET STOCK from LOCATION
    |--------------------------------------------------------------------------
    */

    public function getStock($inventory_id, $location_id)
{
    return $this->db->query(
        "
        SELECT *
        FROM inventory_location_stock
        WHERE inventory_id = ?
        AND location_id = ?
        LIMIT 1
        ",
        [$inventory_id, $location_id]
    )->fetch();
}

public function getLocations()
{
    return $this->db->query("
        SELECT * FROM inventory_locations ORDER BY name
    ")->fetchAll();
}

public function getLocationById($id)
{
    return $this->db->query(
        "
        SELECT *
        FROM inventory_locations
        WHERE id = ?
        ",
        [$id]
    )->fetch();
}

public function increaseStock($inventory_id, $location_id, $qty)
{
    return $this->adjustStock(
        $inventory_id,
        $location_id,
        $qty
    );
}

public function reduceStock($inventory_id, $location_id, $qty)
{
    return $this->adjustStock(
        $inventory_id,
        $location_id,
        -$qty
    );
}

// Get Global Stock for Inventory Item across all locations
public function getGlobalStock($inventory_id)
{
    $result = $this->db->query(
        "
        SELECT
            COALESCE(SUM(quantity),0) AS qty
        FROM inventory_location_stock
        WHERE inventory_id = ?
        ",
        [$inventory_id]
    )->fetch();

    return (float)$result->qty;
}

// helper for multiple items (this will make the inventory list much faster)
public function getAllGlobalStock()
{
    return $this->db->query(
        "
        SELECT
            inventory_id,
            SUM(quantity) AS quantity
        FROM inventory_location_stock
        GROUP BY inventory_id
        "
    )->fetchAll();
}
// The Universal Method
// This method is the only one that directly changes warehouse stock.
    public function adjustStock($inventory_id, $location_id, $delta)
{
    // Positive delta = add stock
    // Negative delta = remove stock

    if ($delta < 0) {

        // Ensure sufficient stock
        $available = $this->db->query(
            "
            SELECT quantity
            FROM inventory_location_stock
            WHERE inventory_id = ?
              AND location_id = ?
            ",
            [$inventory_id, $location_id]
        )->fetch();

        if (!$available || $available->quantity < abs($delta)) {
            return false;
        }
    }

    // Row exists?
    $exists = $this->db->query(
        "
        SELECT id
        FROM inventory_location_stock
        WHERE inventory_id = ?
          AND location_id = ?
        ",
        [$inventory_id, $location_id]
    )->fetch();

    if ($exists) {

        $this->db->query(
            "
            UPDATE inventory_location_stock
            SET quantity = quantity + ?
            WHERE inventory_id = ?
              AND location_id = ?
            ",
            [
                $delta,
                $inventory_id,
                $location_id
            ]
        );

    } else {

        // Cannot create a new row with negative quantity
        if ($delta < 0) {
            return false;
        }

        $this->db->query(
            "
            INSERT INTO inventory_location_stock
            (
                inventory_id,
                location_id,
                quantity
            )
            VALUES (?, ?, ?)
            ",
            [
                $inventory_id,
                $location_id,
                $delta
            ]
        );
    }
/*
|--------------------------------------------------------------------------
| Synchronize Global Inventory Quantity
|--------------------------------------------------------------------------
*/

$this->syncInventoryQuantity($inventory_id);
    return true;
}

public function transferStock($inventory_id, $from, $to, $qty)
{
    if ($from == $to) {
        return false;
    }

    // Remove from source
    if (!$this->adjustStock($inventory_id, $from, -$qty)) {
        return false;
    }

    // Add to destination
    $this->adjustStock($inventory_id, $to, $qty);

    return true;
}

}