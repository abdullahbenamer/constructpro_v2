<?php

require_once '../app/Core/Model.php';
require_once '../app/Models/ProjectLedgerModel.php';

class ResourceRequisitionFulfillmentModel extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GET REQUISITION
    |--------------------------------------------------------------------------
    */

/*
|--------------------------------------------------------------------------
| GET REQUISITION
|--------------------------------------------------------------------------
*/

public function getRequisition($requisition_id)
{
    return $this->db->query(
        "
        SELECT
            rr.*,

            p.title AS project_name,

            l.name AS target_warehouse_name

        FROM resource_requisitions rr

        LEFT JOIN projects p
            ON p.id = rr.project_id

        LEFT JOIN inventory_locations l
            ON l.id = rr.target_warehouse_id

        WHERE rr.id = ?

        LIMIT 1
        ",
        [
            $requisition_id
        ]
    )->fetch();
}


    /*
|--------------------------------------------------------------------------
| GET FULFILLABLE REQUISITION ITEMS
|--------------------------------------------------------------------------
|
| Returns all requisition items that still have a remaining quantity.
|
| INVENTORY items:
|   rri.inventory_id -> inventory.id
|
| RESOURCE items:
|   rri.resource_id -> resources.id
|
*/

  public function getFulfillableItems($requisition_id)
{
    return $this->db->query(
        "
        SELECT

            rri.id,

            rri.requisition_id,

            rri.resource_source,


            /*
            |--------------------------------------------------------------------------
            | EFFECTIVE INVENTORY ID
            |
            | Supports old records where INVENTORY items were stored
            | in resource_id instead of inventory_id.
            |--------------------------------------------------------------------------
            */

            COALESCE(
                rri.inventory_id,
                CASE
                    WHEN rri.resource_source = 'INVENTORY'
                    THEN rri.resource_id
                    ELSE NULL
                END
            ) AS inventory_id,


            /*
            |--------------------------------------------------------------------------
            | ORIGINAL RESOURCE ID
            |--------------------------------------------------------------------------
            */

            rri.resource_id,


            rri.description,

            rri.uom,

            rri.quantity,


            /*
            |--------------------------------------------------------------------------
            | ACTUAL FULFILLED QUANTITY
            |--------------------------------------------------------------------------
            */

            COALESCE(
                fulfilled.fulfilled_qty,
                0
            ) AS fulfilled_quantity,


            /*
            |--------------------------------------------------------------------------
            | REMAINING QUANTITY
            |--------------------------------------------------------------------------
            */

            (
                rri.quantity
                -
                COALESCE(
                    fulfilled.fulfilled_qty,
                    0
                )
            ) AS remaining_quantity,


            rri.estimated_unit_cost,

            rri.estimated_total,

            rri.remarks,

            rri.status,


            /*
            |--------------------------------------------------------------------------
            | INVENTORY DETAILS
            |--------------------------------------------------------------------------
            */

           i.sku,

i.name AS inventory_name,

i.base_unit AS inventory_uom,

i.cost_price AS current_cost,


            /*
            |--------------------------------------------------------------------------
            | RESOURCE DETAILS
            |--------------------------------------------------------------------------
            */

            r.resource_code,

            r.resource_name,

            r.resource_type,

            r.description AS resource_description


        FROM resource_requisition_items rri


        /*
        |--------------------------------------------------------------------------
        | INVENTORY
        |--------------------------------------------------------------------------
        */

        LEFT JOIN inventory i

            ON rri.resource_source = 'INVENTORY'

            AND i.id = COALESCE(
                rri.inventory_id,
                rri.resource_id
            )


        /*
        |--------------------------------------------------------------------------
        | RESOURCE
        |--------------------------------------------------------------------------
        */

        LEFT JOIN resources r

            ON rri.resource_source = 'RESOURCE'

            AND r.id = rri.resource_id


        /*
        |--------------------------------------------------------------------------
        | ACTUAL FULFILLMENT HISTORY
        |--------------------------------------------------------------------------
        */

        LEFT JOIN
        (
            SELECT

                requisition_item_id,

                SUM(
                    fulfilled_quantity
                ) AS fulfilled_qty

            FROM resource_requisition_fulfillment_items

            GROUP BY requisition_item_id

        ) fulfilled

            ON fulfilled.requisition_item_id = rri.id


        WHERE

            rri.requisition_id = ?

            AND rri.status IN (
                'OPEN',
                'PARTIAL'
            )

            AND
            (
                rri.quantity
                >
                COALESCE(
                    fulfilled.fulfilled_qty,
                    0
                )
            )


        ORDER BY

            rri.id ASC
        ",
        [
            $requisition_id
        ]
    )->fetchAll();
}


    /*
    |--------------------------------------------------------------------------
    | GET ALL REQUISITION ITEMS
    |--------------------------------------------------------------------------
    */

    public function getRequisitionItems($requisition_id)
    {
        return $this->db->query(
            "
        SELECT

            rri.*,

            CASE

                WHEN rri.resource_source = 'INVENTORY'
                THEN i.name

                ELSE r.resource_name

            END AS resource_name,

            CASE

                WHEN rri.resource_source = 'INVENTORY'
                THEN i.sku

                ELSE r.resource_code

            END AS resource_code,

            COALESCE(
                fulfilled.fulfilled_qty,
                0
            ) AS fulfilled_qty,

            (
                rri.quantity -
                COALESCE(
                    fulfilled.fulfilled_qty,
                    0
                )
            ) AS remaining_qty

        FROM resource_requisition_items rri

        LEFT JOIN inventory i
            ON i.id = rri.inventory_id
            AND rri.resource_source = 'INVENTORY'

        LEFT JOIN resources r
            ON r.id = rri.resource_id
            AND rri.resource_source = 'RESOURCE'

        LEFT JOIN
        (
            SELECT

                requisition_item_id,

                SUM(fulfilled_quantity) AS fulfilled_qty

            FROM resource_requisition_fulfillment_items

            GROUP BY requisition_item_id

        ) fulfilled
            ON fulfilled.requisition_item_id = rri.id

        WHERE rri.requisition_id = ?

        ORDER BY rri.id ASC
        ",
            [
                $requisition_id
            ]
        )->fetchAll();
    }

    // --------------------
    public function getRequisitionItem($id)
    {
        return $this->db->query(
            "
        SELECT

            rri.*,

            COALESCE(
                fulfilled.fulfilled_qty,
                0
            ) AS actual_fulfilled_qty,

            (
                rri.quantity -
                COALESCE(
                    fulfilled.fulfilled_qty,
                    0
                )
            ) AS remaining_qty

        FROM resource_requisition_items rri

        LEFT JOIN
        (
            SELECT

                requisition_item_id,

                SUM(
                    fulfilled_quantity
                ) AS fulfilled_qty

            FROM resource_requisition_fulfillment_items

            GROUP BY requisition_item_id

        ) fulfilled

            ON fulfilled.requisition_item_id = rri.id

        WHERE rri.id = ?

        LIMIT 1
        ",
            [
                $id
            ]
        )->fetch();
    }

    /*
    |--------------------------------------------------------------------------
    | GET INVENTORY LOCATIONS
    |--------------------------------------------------------------------------
    */

    public function getItemLocations($inventory_id)
    {
        return $this->db->query(

            "
        SELECT

            ils.inventory_id,

            ils.location_id,

            ils.quantity AS available_qty,

            il.name AS location_name,

            il.code AS location_code


        FROM inventory_location_stock ils


        INNER JOIN inventory_locations il

            ON il.id = ils.location_id


        WHERE

            ils.inventory_id = ?

            AND ils.quantity > 0


        ORDER BY il.name ASC
        ",

            [
                $inventory_id
            ]

        )->fetchAll();
    }

    /*
    |--------------------------------------------------------------------------
    | GET LOCATION STOCK
    |--------------------------------------------------------------------------
    */

    public function getLocationStock(
        $inventory_id,
        $location_id
    ) {
        return $this->db->query(
            "
            SELECT

                ils.*,

                i.name AS inventory_name,

                i.sku,

                i.base_unit,

             il.name AS location_name

            FROM inventory_location_stock ils

            INNER JOIN inventory i
                ON i.id = ils.inventory_id

            INNER JOIN inventory_locations il
                ON il.id = ils.location_id

            WHERE

                ils.inventory_id = ?

                AND ils.location_id = ?

            LIMIT 1
            ",
            [
                $inventory_id,
                $location_id
            ]
        )->fetch();
    }


    /*
    |--------------------------------------------------------------------------
    | GET FULFILLMENT HISTORY
    |--------------------------------------------------------------------------
    */

    public function getFulfillments($requisition_id)
    {
        return $this->db->query(
            "
            SELECT

                rrf.*,

                u.full_name AS fulfilled_by_name

            FROM resource_requisition_fulfillments rrf

            LEFT JOIN users u
                ON u.id = rrf.fulfilled_by

            WHERE rrf.requisition_id = ?

            ORDER BY
                rrf.fulfillment_date DESC,
                rrf.id DESC
            ",
            [
                $requisition_id
            ]
        )->fetchAll();
    }


    /*
    |--------------------------------------------------------------------------
    | GET SINGLE FULFILLMENT
    |--------------------------------------------------------------------------
    */

    public function getFulfillmentById($fulfillment_id)
    {
        return $this->db->query(
            "
            SELECT

                rrf.*,

                rr.req_number,

                p.title AS project_name,

                u.full_name AS fulfilled_by_name

            FROM resource_requisition_fulfillments rrf

            INNER JOIN resource_requisitions rr
                ON rr.id = rrf.requisition_id

            LEFT JOIN projects p
                ON p.id = rr.project_id

            LEFT JOIN users u
                ON u.id = rrf.fulfilled_by

            WHERE rrf.id = ?

            LIMIT 1
            ",
            [
                $fulfillment_id
            ]
        )->fetch();
    }


    /*
    |--------------------------------------------------------------------------
    | GET FULFILLMENT ITEMS
    |--------------------------------------------------------------------------
    */

    public function getFulfillmentItems($fulfillment_id)
    {
        return $this->db->query(
            "
            SELECT

                rrfi.*,

                rri.description AS requisition_description,

                i.name AS inventory_name,

                i.sku,

                i.base_unit,

          il.name AS location_name

            FROM resource_requisition_fulfillment_items rrfi

            INNER JOIN resource_requisition_items rri
                ON rri.id = rrfi.requisition_item_id

            INNER JOIN inventory i
                ON i.id = rrfi.inventory_id

            INNER JOIN inventory_locations il
                ON il.id = rrfi.location_id

            WHERE rrfi.fulfillment_id = ?

            ORDER BY rrfi.id ASC
            ",
            [
                $fulfillment_id
            ]
        )->fetchAll();
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE FULFILLMENT
    |--------------------------------------------------------------------------
    |
    | Expected $data:
    |
    | [
    |     'requisition_id' => 1,
    |     'fulfilled_by'   => 1,
    |     'remarks'        => '',
    |
    |     'items' => [
    |         [
    |             'requisition_item_id' => 5,
    |             'inventory_id'         => 10,
    |             'location_id'          => 2,
    |             'quantity'             => 5
    |         ]
    |     ]
    | ]
    |
    */

    /*
|--------------------------------------------------------------------------
| CREATE MATERIAL FULFILLMENT
|--------------------------------------------------------------------------
*/

  public function createFulfillment($data)
{
    return $this->db->transaction(
        function ($db) use ($data) {

            /*
            |--------------------------------------------------------------------------
            | GET + LOCK REQUISITION
            |--------------------------------------------------------------------------
            */

            $requisition =
                $db->query(
                    "
                    SELECT *
                    FROM resource_requisitions
                    WHERE id = ?
                    FOR UPDATE
                    ",
                    [
                        $data['requisition_id']
                    ]
                )->fetch();


            if (!$requisition) {

                throw new Exception(
                    'Resource requisition not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE FULFILLMENT HEADER
            |--------------------------------------------------------------------------
            */

            $db->query(
                "
                INSERT INTO resource_requisition_fulfillments
                (
                    requisition_id,
                    fulfillment_no,
                    fulfillment_date,
                    fulfilled_by,
                    remarks
                )
                VALUES
                (
                    ?, ?, ?, ?, ?
                )
                ",
                [
                    $data['requisition_id'],
                    $data['fulfillment_no'],
                    $data['fulfillment_date'],
                    $data['fulfilled_by'],
                    $data['remarks']
                ]
            );


            $fulfillment_id =
                (int) $db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | PROCESS FULFILLMENT ITEMS
            |--------------------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {


                /*
                |--------------------------------------------------------------------------
                | BASIC VALUES
                |--------------------------------------------------------------------------
                */

                $requisition_item_id =
                    (int) (
                        $item['requisition_item_id']
                        ?? 0
                    );


                $quantity =
                    (float) (
                        $item['quantity']
                        ?? 0
                    );


                $location_id =
                    (int) (
                        $item['location_id']
                        ?? 0
                    );


                /*
                |--------------------------------------------------------------------------
                | SKIP INVALID / ZERO QUANTITY
                |--------------------------------------------------------------------------
                */

                if (
                    $requisition_item_id <= 0
                    ||
                    $quantity <= 0
                ) {

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | GET + LOCK REQUISITION ITEM
                |--------------------------------------------------------------------------
                */

                $reqItem =
                    $db->query(
                        "
                        SELECT *
                        FROM resource_requisition_items
                        WHERE id = ?
                        AND requisition_id = ?
                        FOR UPDATE
                        ",
                        [
                            $requisition_item_id,
                            $data['requisition_id']
                        ]
                    )->fetch();


                if (!$reqItem) {

                    throw new Exception(
                        'Invalid requisition item.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CHECK REMAINING QUANTITY
                |--------------------------------------------------------------------------
                */

                $already_fulfilled =
                    (float) $reqItem->fulfilled_quantity;


                $requested_quantity =
                    (float) $reqItem->quantity;


                $remaining_quantity =
                    $requested_quantity
                    -
                    $already_fulfilled;


                if (
                    $quantity > $remaining_quantity
                ) {

                    throw new Exception(
                        'Fulfillment quantity exceeds the remaining quantity for: '
                        .
                        $reqItem->description
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | INVENTORY MATERIAL
                |--------------------------------------------------------------------------
                */

                if (
                    $reqItem->resource_source === 'INVENTORY'
                ) {


                    /*
                    |--------------------------------------------------------------------------
                    | DETERMINE INVENTORY ID
                    |
                    | Supports your old records where inventory_id may be NULL
                    | and resource_id contains the inventory ID.
                    |--------------------------------------------------------------------------
                    */

                    $inventory_id =
                        (int) (
                            $reqItem->inventory_id
                            ? $reqItem->inventory_id
                            : $reqItem->resource_id
                        );


                    if ($inventory_id <= 0) {

                        throw new Exception(
                            'Inventory item is missing for requisition item: '
                            .
                            $reqItem->description
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | LOCATION REQUIRED
                    |--------------------------------------------------------------------------
                    */

                    if ($location_id <= 0) {

                        throw new Exception(
                            'Please select an inventory location for: '
                            .
                            $reqItem->description
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GET + LOCK LOCATION STOCK
                    |--------------------------------------------------------------------------
                    */

                    $locationStock =
                        $db->query(
                            "
                            SELECT *
                            FROM inventory_location_stock
                            WHERE
                                inventory_id = ?
                                AND location_id = ?
                            FOR UPDATE
                            ",
                            [
                                $inventory_id,
                                $location_id
                            ]
                        )->fetch();


                    if (!$locationStock) {

                        throw new Exception(
                            'Inventory item is not available in the selected location.'
                        );
                    }


                    $available_qty =
                        (float) $locationStock->quantity;


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK LOCATION STOCK
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $quantity > $available_qty
                    ) {

                        throw new Exception(
                            'Insufficient stock in selected location for: '
                            .
                            $reqItem->description
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GET + LOCK INVENTORY
                    |--------------------------------------------------------------------------
                    */

                    $inventory =
                        $db->query(
                            "
                            SELECT *
                            FROM inventory
                            WHERE id = ?
                            FOR UPDATE
                            ",
                            [
                                $inventory_id
                            ]
                        )->fetch();


                    if (!$inventory) {

                        throw new Exception(
                            'Inventory item not found.'
                        );
                    }


                    $global_before =
                        (float) $inventory->quantity;


                    /*
                    |--------------------------------------------------------------------------
                    | CHECK GLOBAL INVENTORY
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $quantity > $global_before
                    ) {

                        throw new Exception(
                            'Insufficient global inventory stock for: '
                            .
                            $reqItem->description
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CALCULATE NEW BALANCES
                    |--------------------------------------------------------------------------
                    */

                    $location_after =
                        $available_qty
                        -
                        $quantity;


                    $global_after =
                        $global_before
                        -
                        $quantity;


                    /*
                    |--------------------------------------------------------------------------
                    | UNIT COST
                    |--------------------------------------------------------------------------
                    */

                    $unit_cost =
                        (float) (
                            $inventory->cost_price
                            ?? 0
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | DEDUCT LOCATION STOCK
                    |--------------------------------------------------------------------------
                    */

                    $db->query(
                        "
                        UPDATE inventory_location_stock
                        SET quantity = ?
                        WHERE
                            inventory_id = ?
                            AND location_id = ?
                        ",
                        [
                            $location_after,
                            $inventory_id,
                            $location_id
                        ]
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | DEDUCT GLOBAL INVENTORY
                    |--------------------------------------------------------------------------
                    */

                    $db->query(
                        "
                        UPDATE inventory
                        SET quantity = ?
                        WHERE id = ?
                        ",
                        [
                            $global_after,
                            $inventory_id
                        ]
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE INVENTORY MOVEMENT
                    |--------------------------------------------------------------------------
                    */

                    $reference =
                        $data['fulfillment_no'];


                    $notes =
                        'Resource requisition fulfillment: '
                        .
                        $requisition->req_number;


                    $db->query(
                        "
                        INSERT INTO inventory_movements
                        (
                            inventory_id,
                            location_id,
                            type,
                            quantity,
                            unit_cost,
                            movement_by,
                            balance_after,
                            global_balance_after,
                            reference,
                            notes,
                            created_by
                        )
                        VALUES
                        (
                            ?, ?, 'OUT', ?, ?, ?, ?, ?, ?, ?, ?
                        )
                        ",
                        [
                            $inventory_id,
                            $location_id,
                            $quantity,
                            $unit_cost,
                            $data['fulfilled_by'],
                            $location_after,
                            $global_after,
                            $reference,
                            $notes,
                            $data['fulfilled_by']
                        ]
                    );


                    $inventory_movement_id =
                        (int) $db->lastInsertId();


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE PROJECT COST
                    |--------------------------------------------------------------------------
                    */

                    $db->query(
                        "
                        INSERT INTO project_costs
                        (
                            project_id,
                            inventory_id,
                            location_id,
                            cost_type,
                            description,
                            quantity,
                            unit_price
                        )
                        VALUES
                        (
                            ?, ?, ?, 'materials', ?, ?, ?
                        )
                        ",
                        [
                            $requisition->project_id,
                            $inventory_id,
                            $location_id,
                            $reqItem->description,
                            $quantity,
                            $unit_cost
                        ]
                    );


                    $project_cost_id =
                        (int) $db->lastInsertId();

/*
|--------------------------------------------------------------------------
| CREATE PROJECT LEDGER ENTRY
|--------------------------------------------------------------------------
*/

$ledgerModel = new ProjectLedgerModel();

$ledgerModel->addEntry([

    'project_id'  => $requisition->project_id,

    'entry_type'  => 'cost',

    'ref_table'   => 'project_costs',

    'ref_id'      => $project_cost_id,

    'description' =>
        'RR Fulfillment: ' .
        $reqItem->description,

    'debit'       =>
        $quantity * $unit_cost,

    'credit'      => 0
]);
                    /*
                    |--------------------------------------------------------------------------
                    | CREATE FULFILLMENT ITEM
                    |--------------------------------------------------------------------------
                    */

                    $db->query(
                        "
                        INSERT INTO resource_requisition_fulfillment_items
                        (
                            fulfillment_id,
                            requisition_item_id,
                            inventory_id,
                            location_id,
                            fulfilled_quantity,
                            unit_cost,
                            remarks,
                            inventory_movement_id,
                            project_cost_id
                        )
                        VALUES
                        (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )
                        ",
                        [
                            $fulfillment_id,
                            $reqItem->id,
                            $inventory_id,
                            $location_id,
                            $quantity,
                            $unit_cost,
                            $item['remarks'] ?? null,
                            $inventory_movement_id,
                            $project_cost_id
                        ]
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE REQUISITION ITEM
                    |--------------------------------------------------------------------------
                    */

                    $new_fulfilled_quantity =
                        $already_fulfilled
                        +
                        $quantity;


                    $new_status =
                        (
                            $new_fulfilled_quantity
                            >=
                            $requested_quantity
                        )
                        ? 'FULFILLED'
                        : 'PARTIAL';


                    $db->query(
                        "
                        UPDATE resource_requisition_items
                        SET
                            fulfilled_quantity = ?,
                            status = ?
                        WHERE id = ?
                        ",
                        [
                            $new_fulfilled_quantity,
                            $new_status,
                            $reqItem->id
                        ]
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | NON-INVENTORY RESOURCE
                |--------------------------------------------------------------------------
                */

                elseif (
                    $reqItem->resource_source === 'RESOURCE'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | UNIT COST
                    |--------------------------------------------------------------------------
                    */

                    $unit_cost =
                        (float) (
                            $item['unit_cost']
                            ?? $reqItem->estimated_unit_cost
                            ?? 0
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | GET RESOURCE
                    |--------------------------------------------------------------------------
                    */

                    $resource =
                        $db->query(
                            "
                            SELECT *
                            FROM resources
                            WHERE id = ?
                            ",
                            [
                                $reqItem->resource_id
                            ]
                        )->fetch();


                    if (!$resource) {

                        throw new Exception(
                            'Resource not found.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | MAP RESOURCE TYPE TO PROJECT COST
                    |--------------------------------------------------------------------------
                    */

                    switch ($resource->resource_type) {

                        case 'LABOR':

                            $cost_type = 'labor';

                            break;


                        case 'SERVICE':

                            $cost_type = 'subcontract';

                            break;


                        case 'EQUIPMENT':

                            $cost_type = 'misc';

                            break;


                        default:

                            $cost_type = 'misc';

                            break;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE PROJECT COST
                    |--------------------------------------------------------------------------
                    */

                    $db->query(
                        "
                        INSERT INTO project_costs
                        (
                            project_id,
                            inventory_id,
                            location_id,
                            cost_type,
                            description,
                            quantity,
                            unit_price
                        )
                        VALUES
                        (
                            ?, NULL, NULL, ?, ?, ?, ?
                        )
                        ",
                        [
                            $requisition->project_id,
                            $cost_type,
                            $reqItem->description,
                            $quantity,
                            $unit_cost
                        ]
                    );


                    $project_cost_id =
                        (int) $db->lastInsertId();

/*
|--------------------------------------------------------------------------
| CREATE PROJECT LEDGER ENTRY
|--------------------------------------------------------------------------
*/

$ledgerModel = new ProjectLedgerModel();

$ledgerModel->addEntry([

    'project_id'  => $requisition->project_id,

    'entry_type'  => 'cost',

    'ref_table'   => 'project_costs',

    'ref_id'      => $project_cost_id,

    'description' =>
        'RR Fulfillment: ' .
        $reqItem->description,

    'debit'       =>
        $quantity * $unit_cost,

    'credit'      => 0
]);
                    /*
                    |--------------------------------------------------------------------------
                    | CREATE FULFILLMENT ITEM
                    |--------------------------------------------------------------------------
                    */

                    $db->query(
                        "
                        INSERT INTO resource_requisition_fulfillment_items
                        (
                            fulfillment_id,
                            requisition_item_id,
                            inventory_id,
                            location_id,
                            fulfilled_quantity,
                            unit_cost,
                            remarks,
                            inventory_movement_id,
                            project_cost_id
                        )
                        VALUES
                        (
                            ?, ?, NULL, NULL, ?, ?, ?, NULL, ?
                        )
                        ",
                        [
                            $fulfillment_id,
                            $reqItem->id,
                            $quantity,
                            $unit_cost,
                            $item['remarks'] ?? null,
                            $project_cost_id
                        ]
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE REQUISITION ITEM
                    |--------------------------------------------------------------------------
                    */

                    $new_fulfilled_quantity =
                        $already_fulfilled
                        +
                        $quantity;


                    $new_status =
                        (
                            $new_fulfilled_quantity
                            >=
                            $requested_quantity
                        )
                        ? 'FULFILLED'
                        : 'PARTIAL';


                    $db->query(
                        "
                        UPDATE resource_requisition_items
                        SET
                            fulfilled_quantity = ?,
                            status = ?
                        WHERE id = ?
                        ",
                        [
                            $new_fulfilled_quantity,
                            $new_status,
                            $reqItem->id
                        ]
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | INVALID RESOURCE SOURCE
                |--------------------------------------------------------------------------
                */

                else {

                    throw new Exception(
                        'Invalid resource source.'
                    );
                }

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE OVERALL REQUISITION STATUS
            |--------------------------------------------------------------------------
            |
            | Important:
            | Check ALL requisition items.
            |
            */

            $remaining =
                $db->query(
                    "
                    SELECT COUNT(*) AS remaining_items

                    FROM resource_requisition_items

                    WHERE
                        requisition_id = ?

                        AND status NOT IN
                        (
                            'FULFILLED',
                            'CANCELLED'
                        )
                    ",
                    [
                        $data['requisition_id']
                    ]
                )->fetch();


            $requisition_status =
                (
                    (int) $remaining->remaining_items === 0
                )
                ? 'FULFILLED'
                : 'PARTIAL';


            $db->query(
                "
                UPDATE resource_requisitions
                SET status = ?
                WHERE id = ?
                ",
                [
                    $requisition_status,
                    $data['requisition_id']
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | RETURN FULFILLMENT ID
            |--------------------------------------------------------------------------
            */

            return $fulfillment_id;
        }
    );
}

/*
|--------------------------------------------------------------------------
| CREATE RESOURCE FULFILLMENT
|--------------------------------------------------------------------------
|
| Handles non-inventory resources such as:
|
| LABOR
| EQUIPMENT
| SERVICE
|
| Does NOT:
| - deduct inventory
| - create inventory movements
| - require inventory location
|
| Does:
| - create fulfillment header
| - create fulfillment items
| - update fulfilled quantities
| - update requisition item status
| - create project costs
| - update requisition status
|
*/

public function createResourceFulfillment($data)
{
    try {

        /*
        |--------------------------------------------------------------------------
        | START TRANSACTION
        |--------------------------------------------------------------------------
        */

        $this->db->beginTransaction();


        /*
        |--------------------------------------------------------------------------
        | GET REQUISITION
        |--------------------------------------------------------------------------
        */

        $requisition =
            $this->db->query(
                "
                SELECT *
                FROM resource_requisitions
                WHERE id = ?
                FOR UPDATE
                ",
                [
                    $data['requisition_id']
                ]
            )->fetch();


        if (!$requisition) {

            throw new Exception(
                'Resource requisition not found.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE FULFILLMENT HEADER
        |--------------------------------------------------------------------------
        */

        $this->db->query(
            "
            INSERT INTO resource_requisition_fulfillments
            (
                requisition_id,
                fulfillment_no,
                fulfillment_date,
                fulfilled_by,
                remarks
            )
            VALUES
            (
                ?, ?, ?, ?, ?
            )
            ",
            [
                $data['requisition_id'],
                $data['fulfillment_no'],
                $data['fulfillment_date'],
                $data['fulfilled_by'],
                $data['remarks']
            ]
        );


        $fulfillment_id =
            (int) $this->db->lastInsertId();


        if ($fulfillment_id <= 0) {

            throw new Exception(
                'Failed to create resource fulfillment.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PROCESS RESOURCE ITEMS
        |--------------------------------------------------------------------------
        */

        foreach ($data['items'] as $item) {


            /*
            |--------------------------------------------------------------------------
            | BASIC VALUES
            |--------------------------------------------------------------------------
            */

            $requisition_item_id =
                (int) (
                    $item['requisition_item_id']
                    ?? 0
                );


            $fulfill_quantity =
                (float) (
                    $item['quantity']
                    ?? 0
                );


            /*
            |--------------------------------------------------------------------------
            | SKIP ZERO QUANTITY
            |--------------------------------------------------------------------------
            */

            if (
                $requisition_item_id <= 0
                ||
                $fulfill_quantity <= 0
            ) {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | GET + LOCK REQUISITION ITEM
            |--------------------------------------------------------------------------
            */

            $requisitionItem =
                $this->db->query(
                    "
                    SELECT
                        rri.*,
                        r.resource_type

                    FROM resource_requisition_items rri

                    LEFT JOIN resources r
                        ON r.id = rri.resource_id

                    WHERE
                        rri.id = ?

                        AND rri.requisition_id = ?

                    FOR UPDATE
                    ",
                    [
                        $requisition_item_id,
                        $data['requisition_id']
                    ]
                )->fetch();


            if (!$requisitionItem) {

                throw new Exception(
                    'Invalid requisition item.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ENSURE THIS IS A RESOURCE ITEM
            |--------------------------------------------------------------------------
            */

            if (
                $requisitionItem->resource_source
                !==
                'RESOURCE'
            ) {

                throw new Exception(
                    'Invalid item. Only resource items can be processed here.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CALCULATE CURRENT FULFILLMENT
            |--------------------------------------------------------------------------
            */

            $fulfilled =
                $this->db->query(
                    "
                    SELECT

                        COALESCE(
                            SUM(fulfilled_quantity),
                            0
                        ) AS fulfilled_qty

                    FROM resource_requisition_fulfillment_items

                    WHERE requisition_item_id = ?
                    ",
                    [
                        $requisition_item_id
                    ]
                )->fetch();


            $previously_fulfilled =
                (float) (
                    $fulfilled->fulfilled_qty
                    ?? 0
                );


            $requested_quantity =
                (float) $requisitionItem->quantity;


            $remaining_quantity =
                $requested_quantity
                -
                $previously_fulfilled;


            /*
            |--------------------------------------------------------------------------
            | VALIDATE QUANTITY
            |--------------------------------------------------------------------------
            */

            if (
                $fulfill_quantity
                >
                $remaining_quantity
            ) {

                throw new Exception(
                    'Fulfillment quantity exceeds the remaining quantity for: '
                    .
                    $requisitionItem->description
                );
            }


            /*
            |--------------------------------------------------------------------------
            | DETERMINE UNIT COST
            |--------------------------------------------------------------------------
            */

            $unit_cost =
                isset($item['unit_cost'])
                &&
                $item['unit_cost'] !== ''
                    ? (float) $item['unit_cost']
                    : (float) (
                        $requisitionItem->estimated_unit_cost
                        ?? 0
                    );


            /*
            |--------------------------------------------------------------------------
            | DETERMINE PROJECT COST TYPE
            |--------------------------------------------------------------------------
            */

            switch (
                strtoupper(
                    $requisitionItem->resource_type
                    ?? ''
                )
            ) {

                case 'LABOR':

                    $cost_type = 'labor';

                    break;


                case 'SERVICE':

                    $cost_type = 'subcontract';

                    break;


                case 'EQUIPMENT':

                    $cost_type = 'misc';

                    break;


                default:

                    $cost_type = 'misc';

                    break;
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE PROJECT COST
            |--------------------------------------------------------------------------
            */

            $dbProjectCost =
                $this->db->query(
                    "
                    INSERT INTO project_costs
                    (
                        project_id,
                        inventory_id,
                        location_id,
                        cost_type,
                        description,
                        quantity,
                        unit_price
                    )
                    VALUES
                    (
                        ?, NULL, NULL, ?, ?, ?, ?
                    )
                    ",
                    [
                        $requisition->project_id,
                        $cost_type,
                        $requisitionItem->description,
                        $fulfill_quantity,
                        $unit_cost
                    ]
                );


            $project_cost_id =
                (int) $this->db->lastInsertId();

/*
|--------------------------------------------------------------------------
| CREATE PROJECT LEDGER ENTRY
|--------------------------------------------------------------------------
*/

$ledgerModel = new ProjectLedgerModel();

$ledgerModel->addEntry([

    'project_id'  => $requisition->project_id,

    'entry_type'  => 'cost',

    'ref_table'   => 'project_costs',

    'ref_id'      => $project_cost_id,

    'description' =>
        'RR Fulfillment: ' .
        $requisitionItem->description,

    'debit' =>
        $fulfill_quantity * $unit_cost,

    'credit' => 0

]);
            /*
            |--------------------------------------------------------------------------
            | CREATE FULFILLMENT ITEM
            |--------------------------------------------------------------------------
            */

            $this->db->query(
                "
                INSERT INTO resource_requisition_fulfillment_items
                (
                    fulfillment_id,
                    requisition_item_id,
                    inventory_id,
                    location_id,
                    fulfilled_quantity,
                    unit_cost,
                    remarks,
                    inventory_movement_id,
                    project_cost_id
                )
                VALUES
                (
                    ?, ?, NULL, NULL, ?, ?, ?, NULL, ?
                )
                ",
                [
                    $fulfillment_id,
                    $requisitionItem->id,
                    $fulfill_quantity,
                    $unit_cost,
                    $item['remarks'] ?? null,
                    $project_cost_id
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | UPDATE REQUISITION ITEM
            |--------------------------------------------------------------------------
            */

            $new_fulfilled_quantity =
                $previously_fulfilled
                +
                $fulfill_quantity;


            $new_status =
                (
                    $new_fulfilled_quantity
                    >=
                    $requested_quantity
                )
                    ? 'FULFILLED'
                    : 'PARTIAL';


            $this->db->query(
                "
                UPDATE resource_requisition_items

                SET
                    fulfilled_quantity = ?,
                    status = ?

                WHERE id = ?
                ",
                [
                    $new_fulfilled_quantity,
                    $new_status,
                    $requisitionItem->id
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE MAIN REQUISITION STATUS
        |--------------------------------------------------------------------------
        */

        $remaining =
            $this->db->query(
                "
                SELECT

                    COUNT(*) AS remaining_items

                FROM resource_requisition_items

                WHERE

                    requisition_id = ?

                    AND status NOT IN
                    (
                        'FULFILLED',
                        'CANCELLED'
                    )
                ",
                [
                    $data['requisition_id']
                ]
            )->fetch();


        $requisition_status =
            (
                (int) $remaining->remaining_items === 0
            )
                ? 'FULFILLED'
                : 'PARTIAL';


        $this->db->query(
            "
            UPDATE resource_requisitions

            SET status = ?

            WHERE id = ?
            ",
            [
                $requisition_status,
                $data['requisition_id']
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

        $this->db->commit();


        /*
        |--------------------------------------------------------------------------
        | RETURN FULFILLMENT ID
        |--------------------------------------------------------------------------
        */

        return $fulfillment_id;


    } catch (Throwable $e) {


        /*
        |--------------------------------------------------------------------------
        | ROLLBACK
        |--------------------------------------------------------------------------
        */

        if (
            $this->db->inTransaction()
        ) {

            $this->db->rollBack();
        }


        throw $e;
    }
}
    /*
    |--------------------------------------------------------------------------
    | UPDATE REQUISITION ITEM STATUS
    |--------------------------------------------------------------------------
    */

public function updateRequisitionItemStatus($item_id)
{
    $item =
        $this->db->query(
            "
            SELECT
                quantity,
                status

            FROM resource_requisition_items

            WHERE id = ?
            ",
            [
                $item_id
            ]
        )->fetch();


    if (!$item) {

        return false;
    }


    $fulfilled =
        $this->db->query(
            "
            SELECT

                COALESCE(
                    SUM(fulfilled_quantity),
                    0
                ) AS fulfilled_qty

            FROM resource_requisition_fulfillment_items

            WHERE requisition_item_id = ?
            ",
            [
                $item_id
            ]
        )->fetch();


    $fulfilled_qty =
        (float) $fulfilled->fulfilled_qty;


    $requested_qty =
        (float) $item->quantity;


    if ($fulfilled_qty <= 0) {

        $status = 'OPEN';

    } elseif ($fulfilled_qty < $requested_qty) {

        $status = 'PARTIAL';

    } else {

        $status = 'FULFILLED';
    }


    return $this->db->query(
        "
        UPDATE resource_requisition_items

        SET
            fulfilled_quantity = ?,
            status = ?

        WHERE id = ?
        ",
        [
            $fulfilled_qty,
            $status,
            $item_id
        ]
    );
}


   
    /*
    |--------------------------------------------------------------------------
    | GET ITEM FULFILLMENT SUMMARY
    |--------------------------------------------------------------------------
    */

  public function getItemFulfillmentSummary($item_id)
{
    return $this->db->query(
        "
        SELECT

            rri.id,

            rri.quantity AS requested_qty,

            rri.status,

            COALESCE(
                SUM(rrfi.fulfilled_quantity),
                0
            ) AS fulfilled_qty,

            (
                rri.quantity
                -
                COALESCE(
                    SUM(rrfi.fulfilled_quantity),
                    0
                )
            ) AS remaining_qty

        FROM resource_requisition_items rri

        LEFT JOIN resource_requisition_fulfillment_items rrfi
            ON rrfi.requisition_item_id = rri.id

        WHERE rri.id = ?

        GROUP BY rri.id
        ",
        [
            $item_id
        ]
    )->fetch();
}


    /*
    |--------------------------------------------------------------------------
    | GET REQUISITION FULFILLMENT SUMMARY
    |--------------------------------------------------------------------------
    */

    public function getRequisitionFulfillmentSummary(
        $requisition_id
    ) {
        return $this->db->query(
            "
            SELECT

                COUNT(*) AS total_items,

                SUM(
                    CASE
                        WHEN status = 'OPEN'
                        THEN 1
                        ELSE 0
                    END
                ) AS open_items,

                SUM(
                    CASE
                        WHEN status = 'PARTIAL'
                        THEN 1
                        ELSE 0
                    END
                ) AS partial_items,

                SUM(
                    CASE
                        WHEN status = 'FULFILLED'
                        THEN 1
                        ELSE 0
                    END
                ) AS fulfilled_items

            FROM resource_requisition_items

            WHERE requisition_id = ?
            ",
            [
                $requisition_id
            ]
        )->fetch();
    }

    public function updateRequisitionStatus($requisition_id)
{
    $result =
        $this->db->query(
            "
            SELECT

                COUNT(*) AS total_items,

                SUM(
                    CASE
                        WHEN status = 'FULFILLED'
                        THEN 1
                        ELSE 0
                    END
                ) AS fulfilled_items

            FROM resource_requisition_items

            WHERE requisition_id = ?

            AND status != 'CANCELLED'
            ",
            [
                $requisition_id
            ]
        )->fetch();


    if (!$result) {

        return false;
    }


    if (
        (int) $result->total_items > 0
        &&
        (int) $result->fulfilled_items ===
        (int) $result->total_items
    ) {

        $status = 'FULFILLED';

    } else {

        $status = 'PARTIAL';
    }


    $this->db->query(
        "
        UPDATE resource_requisitions

        SET status = ?

        WHERE id = ?
        ",
        [
            $status,
            $requisition_id
        ]
    );


    return true;
}

public function getFulfillableMaterialItems($requisition_id)
{
    return $this->db->query(
        "
        SELECT

            rri.id,
            rri.requisition_id,
            rri.resource_source,

            COALESCE(
                rri.inventory_id,
                rri.resource_id
            ) AS inventory_id,

            rri.resource_id,
            rri.description,
            rri.uom,
            rri.quantity,

            COALESCE(
                fulfilled.fulfilled_qty,
                0
            ) AS fulfilled_quantity,

            (
                rri.quantity
                -
                COALESCE(
                    fulfilled.fulfilled_qty,
                    0
                )
            ) AS remaining_quantity,

            rri.estimated_unit_cost,
            rri.estimated_total,
            rri.remarks,
            rri.status,

            i.id AS actual_inventory_id,
            i.sku,
            i.name AS inventory_name,
            i.base_unit AS inventory_uom

        FROM resource_requisition_items rri

        INNER JOIN inventory i

            ON i.id = COALESCE(
                rri.inventory_id,
                rri.resource_id
            )

        LEFT JOIN
        (
            SELECT

                requisition_item_id,

                SUM(
                    fulfilled_quantity
                ) AS fulfilled_qty

            FROM resource_requisition_fulfillment_items

            GROUP BY requisition_item_id

        ) fulfilled

            ON fulfilled.requisition_item_id = rri.id

        WHERE

            rri.requisition_id = ?

            AND rri.resource_source = 'INVENTORY'

            AND rri.status IN (
                'OPEN',
                'PARTIAL'
            )

            AND rri.quantity >
                COALESCE(
                    fulfilled.fulfilled_qty,
                    0
                )

        ORDER BY rri.id ASC
        ",
        [
            $requisition_id
        ]
    )->fetchAll();
}


public function getFulfillableResourceItems($requisition_id)
{
    return $this->db->query(
        "
        SELECT

            rri.id,
            rri.requisition_id,
            rri.resource_source,
            rri.inventory_id,
            rri.resource_id,
            rri.description,
            rri.uom,
            rri.quantity,

            COALESCE(
                fulfilled.fulfilled_qty,
                0
            ) AS fulfilled_quantity,

            (
                rri.quantity
                -
                COALESCE(
                    fulfilled.fulfilled_qty,
                    0
                )
            ) AS remaining_quantity,

            rri.estimated_unit_cost,
            rri.estimated_total,
            rri.remarks,
            rri.status,

            r.resource_code,
            r.resource_name,
            r.resource_name_a,
            r.resource_type,
            r.description AS resource_description

        FROM resource_requisition_items rri

        INNER JOIN resources r

            ON r.id = rri.resource_id

        LEFT JOIN
        (
            SELECT

                requisition_item_id,

                SUM(
                    fulfilled_quantity
                ) AS fulfilled_qty

            FROM resource_requisition_fulfillment_items

            GROUP BY requisition_item_id

        ) fulfilled

            ON fulfilled.requisition_item_id = rri.id

        WHERE

            rri.requisition_id = ?

            AND rri.resource_source = 'RESOURCE'

            AND rri.status IN (
                'OPEN',
                'PARTIAL'
            )

            AND rri.quantity >
                COALESCE(
                    fulfilled.fulfilled_qty,
                    0
                )

        ORDER BY rri.id ASC
        ",
        [
            $requisition_id
        ]
    )->fetchAll();
}

}
