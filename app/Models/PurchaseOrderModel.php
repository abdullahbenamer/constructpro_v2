<?php
require_once '../app/Core/Model.php';

class PurchaseOrderModel extends Model
{

        public function getAll()
{
    return $this->db->query(
        "
        SELECT
            po.*,
            s.company_name AS supplier_name,

            COUNT(poi.id) AS item_count

        FROM purchase_orders po

        JOIN suppliers s
            ON s.id = po.supplier_id

        LEFT JOIN purchase_order_items poi
            ON poi.purchase_order_id = po.id

        GROUP BY
            po.id

        ORDER BY
            po.created_at DESC
        "
    )->fetchAll();
}

    /*
    |--------------------------------------------------------------------------
    | GET BY ID
    |--------------------------------------------------------------------------
    */

/*
|--------------------------------------------------------------------------
| GET BY ID
|--------------------------------------------------------------------------
*/

public function getById($id)
{
    return $this->db->query(
        "
        SELECT

            po.*,

            s.company_name AS supplier_name,

            /* PROJECT */
            p.title AS project_name,
            p.site_location AS project_site_location,

            /* PROJECT MANAGER */
            pm.full_name AS project_manager_name,
            pm.mobile AS project_manager_mobile,

            /* TARGET WAREHOUSE */
            l.code AS target_warehouse_code,
            l.name AS target_warehouse_name,
            l.address AS target_warehouse_address,
            l.mobile AS target_warehouse_mobile,

            /* STOREKEEPER */
            sk.full_name AS storekeeper_name,
            sk.mobile AS storekeeper_mobile

        FROM purchase_orders po

        JOIN suppliers s
            ON s.id = po.supplier_id

        LEFT JOIN projects p
            ON p.id = po.project_id

        LEFT JOIN users pm
            ON pm.id = p.project_manager_id

        LEFT JOIN inventory_locations l
            ON l.id = po.target_warehouse_id

        LEFT JOIN users sk
            ON sk.id = l.storekeeper_id

        WHERE po.id = ?

        LIMIT 1
        ",
        [$id]
    )->fetch();
}

    /*
    |--------------------------------------------------------------------------
    | CREATE PURCHASE ORDER
    |--------------------------------------------------------------------------
    */

/*
|--------------------------------------------------------------------------
| CREATE PURCHASE ORDER
|--------------------------------------------------------------------------
*/

public function create($data)
{
    $this->db->query(
        "
        INSERT INTO purchase_orders
        (
            po_number,
            supplier_id,
            project_id,
            requisition_id,
            target_warehouse_id,
            delivery_method,
            order_date,
            expected_date,
            notes,
            created_by
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ",
        [
            $data['po_number'],
            $data['supplier_id'],
            $data['project_id'] ?? null,
            $data['requisition_id'] ?? null,
            $data['target_warehouse_id'] ?? null,
            $data['delivery_method'] ?? 'WAREHOUSE',
            $data['order_date'],
            $data['expected_date'],
            $data['notes'],
            $_SESSION['user_id']
        ]
    );

    return $this->db->lastInsertId();
}

    public function addItem($data)
    {
        return $this->db->query(
            "
            INSERT INTO purchase_order_items
            (
                purchase_order_id,
                inventory_id,
                quantity,
                unit_cost,
                total_cost,
                notes
            )
            VALUES (?, ?, ?, ?, ?, ?)
            ",
            [
                $data['purchase_order_id'],
                $data['inventory_id'],
                $data['quantity'],
                $data['unit_cost'],
                $data['total_cost'],
                $data['notes']
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GET PO ITEMS
    |--------------------------------------------------------------------------
    */

    public function getItems($po_id)
{
    return $this->db->query(
        "
        SELECT
            poi.*,
            i.name,
            i.sku,
            i.base_unit

        FROM purchase_order_items poi

        JOIN inventory i
            ON i.id = poi.inventory_id

        WHERE poi.purchase_order_id = ?

        ORDER BY poi.id ASC
        ",
        [$po_id]
    )->fetchAll();
}

    /*
    |--------------------------------------------------------------------------
    | UPDATE TOTALS
    |--------------------------------------------------------------------------
    */

  public function updateTotals($purchase_order_id)
{
    $result = $this->db->query(
        "
        SELECT 
            COALESCE(SUM(quantity * unit_cost), 0) AS total
        FROM purchase_order_items
        WHERE purchase_order_id = ?
        ",
        [$purchase_order_id]
    )->fetch();

    $subtotal = (float)$result->total;

    $this->db->query(
        "
        UPDATE purchase_orders
        SET 
            subtotal = ?,
            total_amount = ?
        WHERE id = ?
        ",
        [$subtotal, $subtotal, $purchase_order_id]
    );
}

    public function approve($id, $user_id)
{
    return $this->db->query(
        "
        UPDATE purchase_orders
        SET
            status = 'approved',
            approved_by = ?,
            approved_at = NOW()
        WHERE id = ?
        ",
        [$user_id, $id]
    );
}

public function cancel($id)
{
    return $this->db->query(
        "
        UPDATE purchase_orders
        SET status = 'cancelled'
        WHERE id = ?
        ",
        [$id]
    );
}

public function isEditable($id)
{
    $po = $this->getById($id);

    if (!$po) {
        return false;
    }

    return $po->status === 'draft';
}

public function items($po_id)
{
  
    $data = $this->db->query("
        SELECT
            poi.id,
            poi.inventory_id,
            poi.quantity,
            poi.received_quantity,
            poi.unit_cost,

            i.name,
            i.sku,
            i.base_unit

        FROM purchase_order_items poi

        JOIN inventory i
            ON i.id = poi.inventory_id

        WHERE poi.purchase_order_id = ?
    ", [$po_id])->fetchAll();

    echo json_encode($data);
    exit;
}

public function getOpenPurchaseOrders()
{
    return $this->db->query("
        SELECT po.*, s.company_name
        FROM purchase_orders po
        LEFT JOIN suppliers s ON s.id = po.supplier_id
        WHERE po.status IN ('approved', 'partial')
        ORDER BY po.id DESC
    ")->fetchAll();
}

public function getPOItems($po_id)
{
    return $this->db->query("
        SELECT
            poi.*,
            i.name,
            i.sku,
            i.base_unit
        FROM purchase_order_items poi
        INNER JOIN inventory i ON i.id = poi.inventory_id
        WHERE poi.purchase_order_id = ?
        AND poi.quantity > poi.received_quantity
        ORDER BY i.name
    ", [$po_id])->fetchAll();
}

public function getPOItem($po_id, $inventory_id)
{
    return $this->db->query("
        SELECT *
        FROM purchase_order_items
        WHERE purchase_order_id = ?
        AND inventory_id = ?
    ", [$po_id, $inventory_id])->fetch();
}

public function receiveItem($po_id, $inventory_id, $quantity)
{
    $this->db->query("
        UPDATE purchase_order_items
        SET received_quantity = received_quantity + ?
        WHERE purchase_order_id = ?
        AND inventory_id = ?
    ", [$quantity, $po_id, $inventory_id]);

    $this->updatePOStatus($po_id);
}

public function updatePOStatus($po_id)
{
    $items = $this->db->query("
        SELECT
            SUM(quantity) AS ordered,
            SUM(received_quantity) AS received
        FROM purchase_order_items
        WHERE purchase_order_id = ?
    ", [$po_id])->fetch();

    if ((float)$items->received <= 0) {
        $status = 'approved';
    } elseif ((float)$items->received < (float)$items->ordered) {
        $status = 'partial';
    } else {
        $status = 'received';
    }

    $this->db->query("
        UPDATE purchase_orders
        SET status = ?, received_at = IF(? = 'received', NOW(), received_at)
        WHERE id = ?
    ", [$status, $status, $po_id]);
}

public function getRemainingQuantity($po_id, $inventory_id)
{
    $item = $this->getPOItem($po_id, $inventory_id);

    if (!$item) return 0;

    return $item->quantity - $item->received_quantity;
}

public function countBySupplier($supplier_id)
{
    return $this->db->query(
        "SELECT COUNT(*) AS total FROM purchase_orders WHERE supplier_id = ?",
        [$supplier_id]
    )->fetch()->total ?? 0;
}

public function sumBySupplier($supplier_id)
{
    return $this->db->query(
        "SELECT SUM(total_amount) AS total FROM purchase_orders WHERE supplier_id = ?",
        [$supplier_id]
    )->fetch()->total ?? 0;
}

public function getBySupplier($supplier_id)
{
    return $this->db->query(
        "SELECT * FROM purchase_orders WHERE supplier_id = ? ORDER BY created_at DESC",
        [$supplier_id]
    )->fetchAll();
}

public function updateReceivingStatus($po_id)
{
    $row = $this->db->query("
        SELECT 
            SUM(quantity) as ordered_qty,
            SUM(received_quantity) as received_qty
        FROM purchase_order_items
        WHERE purchase_order_id = ?
    ", [$po_id])->fetch();

    if (!$row) {
        return false;
    }

    $ordered  = (float)$row->ordered_qty;
    $received = (float)$row->received_qty;

    if ($received <= 0) {
        $status = 'OPEN';
    } elseif ($received < $ordered) {
        $status = 'PARTIAL';
    } else {
        $status = 'RECEIVED';
    }

    $this->db->query("
        UPDATE purchase_orders
        SET receiving_status = ?
        WHERE id = ?
    ", [$status, $po_id]);

    return true;
}
}