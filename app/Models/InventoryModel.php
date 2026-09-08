<?php
require_once '../app/Core/Model.php';

class InventoryModel extends Model
{
    public function getLowStockAlerts()
{
    return $this->db->query("
        SELECT
            i.*,
            COALESCE(SUM(ls.quantity),0) AS current_stock
        FROM inventory i
        LEFT JOIN inventory_location_stock ls
            ON ls.inventory_id = i.id
        GROUP BY i.id
        HAVING current_stock < i.min_stock
        ORDER BY current_stock ASC
    ")->fetchAll();
}

public function getByLocation($location_id)
{
    $sql = "
        SELECT 
            i.*,
            s.quantity AS available_qty,
            s.location_id
        FROM inventory i
        JOIN inventory_location_stock s 
            ON s.inventory_id = i.id
        WHERE s.location_id = :location_id
    ";

  return $this->db->query($sql, $params)->fetchAll();
}

    // public function getStock($category = null)
    // {
    //     $sql = "
    //     SELECT
    //         inventory.*,
    //        b.brand_name AS brand_name,
    //         c.country_name AS brand_country,
    //         c.country_code AS country_code,

    //         (
    //             SELECT COALESCE(SUM(ir.quantity), 0)

    //             FROM inventory_reservations ir

    //             WHERE ir.inventory_id = inventory.id
    //             AND ir.status = 'ACTIVE'

    //         ) AS reserved_qty,

    //         (
    //             inventory.quantity -

    //             (
    //                 SELECT COALESCE(SUM(ir.quantity), 0)

    //                 FROM inventory_reservations ir

    //                 WHERE ir.inventory_id = inventory.id
    //                 AND ir.status = 'ACTIVE'
    //             )

    //         ) AS available_qty

    //     FROM inventory
    //     LEFT JOIN brands b ON b.id = inventory.brand_id
    //     LEFT JOIN countries c  ON c.id = b.country_id

    //     WHERE inventory.quantity >= 0
    // ";

    //     $params = [];

    //     if ($category) {

    //         $sql .= " AND inventory.category = ?";

    //         $params[] = $category;
    //     }

    //     $sql .= " ORDER BY inventory.category, inventory.name";

    //     return $this->db->query($sql, $params)->fetchAll();
    // }

    public function getStock($category = null)
{
    $sql = "
        SELECT
            inventory.*,

            b.brand_name AS brand_name,

            c.country_name AS brand_country,

            c.country_code AS country_code,


            /*
            |----------------------------------------------------------
            | TOTAL PHYSICAL STOCK FROM ALL LOCATIONS
            |----------------------------------------------------------
            */

            (
                SELECT COALESCE(
                    SUM(ils.quantity),
                    0
                )

                FROM inventory_location_stock ils

                WHERE ils.inventory_id = inventory.id
            ) AS location_total,


            /*
            |----------------------------------------------------------
            | LOCATION BREAKDOWN
            |
            | Example:
            | MAIN: 250 | MUSRATA: 100 | EX-WH: 100
            |----------------------------------------------------------
            */

            (
                SELECT GROUP_CONCAT(

                    CONCAT(
                        il.code,
                        ': ',
                        FORMAT(ils.quantity, 2)
                    )

                    ORDER BY il.code

                    SEPARATOR ' | '

                )

                FROM inventory_location_stock ils

                INNER JOIN inventory_locations il
                    ON il.id = ils.location_id

                WHERE ils.inventory_id = inventory.id
                AND ils.quantity > 0

            ) AS location_breakdown,


            /*
            |----------------------------------------------------------
            | ACTIVE RESERVED QUANTITY
            |----------------------------------------------------------
            */

            (
                SELECT COALESCE(
                    SUM(ir.quantity),
                    0
                )

                FROM inventory_reservations ir

                WHERE ir.inventory_id = inventory.id
                AND ir.status = 'ACTIVE'

            ) AS reserved_qty,


            /*
            |----------------------------------------------------------
            | AVAILABLE QUANTITY
            |
            | Based on actual total physical stock in locations.
            |----------------------------------------------------------
            */

            (
                (
                    SELECT COALESCE(
                        SUM(ils.quantity),
                        0
                    )

                    FROM inventory_location_stock ils

                    WHERE ils.inventory_id = inventory.id
                )

                -

                (
                    SELECT COALESCE(
                        SUM(ir.quantity),
                        0
                    )

                    FROM inventory_reservations ir

                    WHERE ir.inventory_id = inventory.id
                    AND ir.status = 'ACTIVE'
                )
            ) AS available_qty


        FROM inventory

        LEFT JOIN brands b
            ON b.id = inventory.brand_id

        LEFT JOIN countries c
            ON c.id = b.country_id

        WHERE inventory.quantity >= 0
    ";

    $params = [];

    if ($category) {

        $sql .= "
            AND inventory.category = ?
        ";

        $params[] = $category;
    }

    $sql .= "
        ORDER BY
            inventory.category,
            inventory.name
    ";

    return $this->db
        ->query(
            $sql,
            $params
        )
        ->fetchAll();
}

public function getStockByLocation($location_id)
{
    return $this->db->query(
        "
        SELECT 
            i.*,
            s.quantity AS available_qty,
            s.location_id,

            (
                SELECT COALESCE(SUM(ir.quantity),0)
                FROM inventory_reservations ir
                WHERE ir.inventory_id = i.id
                AND ir.status = 'ACTIVE'
            ) AS reserved_qty,

            (
                s.quantity - (
                    SELECT COALESCE(SUM(ir.quantity),0)
                    FROM inventory_reservations ir
                    WHERE ir.inventory_id = i.id
                    AND ir.status = 'ACTIVE'
                )
            ) AS available_qty_calc

        FROM inventory i
        JOIN inventory_location_stock s 
            ON s.inventory_id = i.id
        WHERE s.location_id = ?
        ",
        [$location_id]
    )->fetchAll();
}

public function getAll()
{
    return $this->db->query("
        SELECT

            i.*,

            COALESCE(SUM(ls.quantity),0) AS quantity,

            COALESCE(SUM(ls.quantity),0) AS available_qty

        FROM inventory i

        LEFT JOIN inventory_location_stock ls
            ON ls.inventory_id = i.id

        GROUP BY i.id

        ORDER BY i.name
    ")->fetchAll();
}

// public function getAll()
// {
//     return $this->db->query(
//         "
//         SELECT
//             i.*,
//             COALESCE(SUM(ls.quantity),0) AS quantity
//         FROM inventory i
//         LEFT JOIN inventory_location_stock ls
//             ON ls.inventory_id = i.id
//         GROUP BY i.id
//         ORDER BY i.name
//         "
//     )->fetchAll();
// }

    public function getInventoryValue()
{
    return $this->db->query("
        SELECT
            SUM(quantity * cost_price) AS total_value
        FROM inventory
    ")->fetch()->total_value;
}

  public function getById($id)
{
    return $this->db->query(
        "
        SELECT
            i.*,
            COALESCE(SUM(ls.quantity),0) AS quantity
        FROM inventory i
        LEFT JOIN inventory_location_stock ls
            ON ls.inventory_id=i.id
        WHERE i.id=?
        GROUP BY i.id
        ",
        [$id]
    )->fetch();
}
    
    public function create($data)
{
     $this->db->query(
        "INSERT INTO inventory 
        (name, category, sku, brand_id, country_id, location_id,
         quantity, min_stock, cost_price,
         base_unit, allow_fraction, sale_unit, units_per_sale,
         price_per_base, price_per_sale)

        VALUES (?, ?, ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?)",
        [
            $data['name'],
            $data['category'],
            $data['sku'],
            $data['brand_id'],
            $data['country_id'],
            $data['location_id'],   

            $data['quantity'],
            $data['min_stock'],
            $data['cost_price'],

            $data['base_unit'] ?? 'unit',
            $data['allow_fraction'] ?? 0,
            $data['sale_unit'] ?? null,
            $data['units_per_sale'] ?? 1,

            $data['price_per_base'] ?? 0,
            $data['price_per_sale'] ?? 0
        ]
    )->rowCount() > 0;

     return $this->db->lastInsertId();
}

    public function getByBarcode($barcode)
    {
        return $this->db->query(

            "
            SELECT
                inventory.*,
                b.brand_name AS brand_name,
                c.country_name AS brand_country,
                    c.country_code AS country_code,
    
                (
                    SELECT COALESCE(SUM(ir.quantity), 0)
    
                    FROM inventory_reservations ir
    
                    WHERE ir.inventory_id = inventory.id
                    AND ir.status = 'ACTIVE'
    
                ) AS reserved_qty,
    
                (
                    inventory.quantity -
    
                    (
                        SELECT COALESCE(SUM(ir.quantity), 0)
    
                        FROM inventory_reservations ir
    
                        WHERE ir.inventory_id = inventory.id
                        AND ir.status = 'ACTIVE'
                    )
    
                ) AS available_qty
    
            FROM inventory
            LEFT JOIN brands b ON b.id = inventory.brand_id
            LEFT JOIN countries c  ON c.id = b.country_id    
            WHERE inventory.sku = ?
            LIMIT 1
            ",
            [$barcode]

        )->fetch();
    }

    public function update($id, $data)
    {
        $existing = $this->getById($id);

        return $this->db->query(
            "UPDATE inventory SET 
            name = ?, 
            category = ?, 
            sku = ?, 
              brand_id = ?,
              country_id = ?,
            quantity = ?, 
            min_stock = ?, 
            cost_price = ?, 
            base_unit = ?, 
            allow_fraction = ?, 
            sale_unit = ?, 
            units_per_sale = ?, 
            price_per_base = ?, 
            price_per_sale = ?
         WHERE id = ?",
            [
                $data['name'] ?? $existing->name,
                $data['category'] ?? $existing->category,
                $data['sku'] ?? $existing->sku,
                $data['brand_id'] ?? $existing->brand_id,
                 $data['country_id'] ?? $existing->country_id,
                $data['quantity'] ?? $existing->quantity,
                $data['min_stock'] ?? $existing->min_stock,
                $data['cost_price'] ?? $existing->cost_price,
                $data['base_unit'] ?? $existing->base_unit,
                $data['allow_fraction'] ?? $existing->allow_fraction,
                $data['sale_unit'] ?? $existing->sale_unit,
                $data['units_per_sale'] ?? $existing->units_per_sale,
                $data['price_per_base'] ?? $existing->price_per_base,
                $data['price_per_sale'] ?? $existing->price_per_sale,
                $id
            ]
        )->rowCount() > 0;
    }

    public function delete($id)
    {
        return $this->db->query("DELETE FROM inventory WHERE id = ?", [$id])->rowCount() > 0;
    }

       // reduce avoiding (Race Condition)
    public function reduceStockSafe($id, $qty)
    {
        $stmt = $this->db->query(
            "UPDATE inventory 
         SET quantity = quantity - ? 
         WHERE id = ? AND quantity >= ?",
            [$qty, $id, $qty]
        );

        return $stmt->rowCount() > 0;
    }

    // address the RACE CONDITION in inventory reservation
    public function reduceAvailableStockSafe($id, $qty)
    {
        $stmt = $this->db->query(

            "
        UPDATE inventory

        SET quantity = quantity - ?

        WHERE id = ?

        AND
        (
            quantity -

            (
                SELECT COALESCE(SUM(ir.quantity), 0)

                FROM inventory_reservations ir

                WHERE ir.inventory_id = inventory.id
                AND ir.status = 'ACTIVE'
            )

        ) >= ?
        ",
            [$qty, $id, $qty]

        );

        return $stmt->rowCount() > 0;
    }


    public function skuExists($sku)
    {
        $stmt = $this->db->query(
            "SELECT id FROM inventory WHERE sku = ? LIMIT 1",
            [$sku]
        );

        return $stmt->fetch() ? true : false;
    }

    public function getAvailableStock($inventory_id)
    {
        $item = $this->getById($inventory_id);

        if (!$item) {
            return 0;
        }

        $reservationModel =
            new InventoryReservationModel();

        $reserved =
            $reservationModel
            ->getActiveReservedQty($inventory_id);

        return (float)$item->quantity - $reserved;
    }

    // Reserved quantity
    public function getReservedQty($inventory_id)
    {
        $result = $this->db->query(
            "
        SELECT
            COALESCE(SUM(quantity),0) AS total

        FROM inventory_reservations

        WHERE inventory_id = ?
        AND status = 'ACTIVE'
        ",
            [$inventory_id]
        )->fetch();

        return (float)$result->total;
    }

public function reduceAvailableStockByLocationSafe($inventory_id, $location_id, $qty)
{
    $stmt = $this->db->query(
        "
        UPDATE inventory_location_stock
        SET quantity = quantity - ?
        WHERE inventory_id = ?
        AND location_id = ?
        AND quantity >= ?
        ",
        [$qty, $inventory_id, $location_id, $qty]
    );

    return $stmt->rowCount() > 0;
}

    public function getAvailableQty($inventory_id)
    {
        $item = $this->getById($inventory_id);

        if (!$item) {
            return 0;
        }

        $reserved =
            $this->getReservedQty($inventory_id);

        return
            (float)$item->quantity
            -
            $reserved;
    }

public function getBySku($sku)
{
    return $this->db->query(
        "SELECT id, name, sku
         FROM inventory
         WHERE sku = ?
         LIMIT 1",
        [$sku]
    )->fetch();
}

public function getByIdAndLocation($id, $location_id)
{
    $stmt = $this->db->query(
        "
        SELECT
            inventory.*,

            ils.quantity AS location_qty,
            ils.location_id,

            b.brand_name AS brand_name,
            c.country_name AS brand_country,
            c.country_code AS country_code,

            (
                SELECT COALESCE(SUM(ir.quantity),0)

                FROM inventory_reservations ir

                WHERE ir.inventory_id = inventory.id
                AND ir.status = 'ACTIVE'
            ) AS reserved_qty,

            (
                ils.quantity -

                (
                    SELECT COALESCE(SUM(ir.quantity),0)

                    FROM inventory_reservations ir

                    WHERE ir.inventory_id = inventory.id
                    AND ir.status = 'ACTIVE'
                )

            ) AS available_qty

        FROM inventory

        INNER JOIN inventory_location_stock ils
            ON ils.inventory_id = inventory.id

        LEFT JOIN brands b
            ON b.id = inventory.brand_id

        LEFT JOIN countries c
            ON c.id = b.country_id

        WHERE inventory.id = ?
        AND ils.location_id = ?

        LIMIT 1
        ",
        [$id, $location_id]
    );

    return $stmt->fetch();
}

public function getMaterialItems()
{
    return $this->db->query(
        "
        SELECT
            id,
            sku,
            name,
            base_unit,
            available_qty
        FROM inventory_stock_view
        ORDER BY name
        "
    )->fetchAll();
}

public function getLocationBreakdown(int $inventory_id)
{
    return $this->db->query(
        "
        SELECT
            il.id AS location_id,

            il.code AS location_code,

            il.name AS location_name,

            COALESCE(
                ils.quantity,
                0
            ) AS physical_qty,

            /* ACTIVE RESERVED QUANTITY FOR THIS ITEM + LOCATION */
            COALESCE(
                (
                    SELECT SUM(ir.quantity)

                    FROM inventory_reservations ir

                    WHERE ir.inventory_id = ?

                    AND ir.location_id = il.id

                    AND ir.status = 'ACTIVE'
                ),
                0
            ) AS reserved_qty,

            /* AVAILABLE QUANTITY */
            (
                COALESCE(
                    ils.quantity,
                    0
                )

                -

                COALESCE(
                    (
                        SELECT SUM(ir.quantity)

                        FROM inventory_reservations ir

                        WHERE ir.inventory_id = ?

                        AND ir.location_id = il.id

                        AND ir.status = 'ACTIVE'
                    ),
                    0
                )
            ) AS available_qty

        FROM inventory_locations il

        LEFT JOIN inventory_location_stock ils

            ON ils.location_id = il.id

            AND ils.inventory_id = ?

        ORDER BY il.code
        ",
        [
            $inventory_id,
            $inventory_id,
            $inventory_id
        ]
    )->fetchAll();
}

}
