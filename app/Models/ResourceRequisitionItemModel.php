<?php
require_once '../app/Core/Model.php';
class ResourceRequisitionItemModel
{

    private $db;


    public function __construct()
    {
        $this->db = new Database;
    }


    public function getByRequisition($requisition_id)
    {
        return $this->db->query(
            "
        SELECT

            ri.*,

            /*
            |--------------------------------------------------------------------------
            | RESOURCE / INVENTORY NAME
            |--------------------------------------------------------------------------
            */

            CASE
                WHEN ri.resource_source = 'INVENTORY'
                    THEN i.name
                ELSE r.resource_name
            END AS resource_name,

            /*
            |--------------------------------------------------------------------------
            | RESOURCE / INVENTORY CODE
            |--------------------------------------------------------------------------
            */

            CASE
                WHEN ri.resource_source = 'INVENTORY'
                    THEN i.sku
                ELSE r.resource_code
            END AS resource_code,

            /*
            |--------------------------------------------------------------------------
            | UOM
            |--------------------------------------------------------------------------
            */

            CASE
                WHEN ri.resource_source = 'INVENTORY'
                    THEN i.base_unit
                ELSE u.unit_name
            END AS uom,

            /*
            |--------------------------------------------------------------------------
            | RESOURCE CATEGORY
            |--------------------------------------------------------------------------
            */

            rc.category_name,

            /*
            |--------------------------------------------------------------------------
            | CURRENT GLOBAL INVENTORY AVAILABILITY
            |--------------------------------------------------------------------------
            */

           CASE
    WHEN ri.resource_source = 'INVENTORY'
        THEN i.quantity
    ELSE NULL
END AS available_qty

        FROM resource_requisition_items ri

        /*
        |--------------------------------------------------------------------------
        | INVENTORY
        |--------------------------------------------------------------------------
        */

        LEFT JOIN inventory i
            ON i.id = ri.resource_id
           AND ri.resource_source = 'INVENTORY'

        /*
        |--------------------------------------------------------------------------
        | RESOURCE
        |--------------------------------------------------------------------------
        */

        LEFT JOIN resources r
            ON r.id = ri.resource_id
           AND ri.resource_source = 'RESOURCE'

        /*
        |--------------------------------------------------------------------------
        | UNIT
        |--------------------------------------------------------------------------
        */

        LEFT JOIN units u
            ON u.id = r.unit_id

        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        LEFT JOIN resource_categories rc
            ON rc.id = r.category_id

        WHERE ri.requisition_id = ?

        ORDER BY ri.id ASC
        ",
            [
                $requisition_id
            ]
        )->fetchAll();
    }
    /**
     * Add requisition item
     */

    public function create(array $data)
    {
        return $this->db->query(
            "
        INSERT INTO resource_requisition_items
        (
            requisition_id,
            resource_source,
            inventory_id,
            resource_id,
            description,
            quantity,
            uom,
            remarks
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ",
            [
                $data['requisition_id'],

                $data['resource_source'],

                $data['inventory_id'] ?? null,

                $data['resource_id'] ?? null,

                $data['description'],

                $data['quantity'],

                $data['uom'],

                $data['remarks']
            ]
        );
    }


    /**
     * GET ITEM BY ID
     */

    /**
     * GET ITEM BY ID
     */
    public function getById($id)
    {
        return $this->db->query(
            "
        SELECT

            ri.*,

            /*
            |--------------------------------------------------------------------------
            | RESOURCE / INVENTORY CODE
            |--------------------------------------------------------------------------
            */

            CASE
                WHEN ri.resource_source = 'INVENTORY'
                THEN i.sku
                ELSE r.resource_code
            END AS resource_code,


            /*
            |--------------------------------------------------------------------------
            | RESOURCE / INVENTORY NAME
            |--------------------------------------------------------------------------
            */

            CASE
                WHEN ri.resource_source = 'INVENTORY'
                THEN i.name
                ELSE r.resource_name
            END AS resource_name,


            /*
            |--------------------------------------------------------------------------
            | DESCRIPTION
            |--------------------------------------------------------------------------
            */

            CASE
                WHEN ri.resource_source = 'INVENTORY'
                THEN i.name
                ELSE r.description
            END AS resource_description,


            /*
            |--------------------------------------------------------------------------
            | UOM
            |--------------------------------------------------------------------------
            */

            CASE
                WHEN ri.resource_source = 'INVENTORY'
                THEN i.base_unit
                ELSE u.unit_name
            END AS unit_name,


            /*
            |--------------------------------------------------------------------------
            | CATEGORY
            |--------------------------------------------------------------------------
            */

            rc.category_name

        FROM resource_requisition_items ri

        /*
        |--------------------------------------------------------------------------
        | INVENTORY
        |--------------------------------------------------------------------------
        */

        LEFT JOIN inventory i
            ON i.id = ri.resource_id
           AND ri.resource_source = 'INVENTORY'


        /*
        |--------------------------------------------------------------------------
        | RESOURCE
        |--------------------------------------------------------------------------
        */

        LEFT JOIN resources r
            ON r.id = ri.resource_id
           AND ri.resource_source = 'RESOURCE'


        /*
        |--------------------------------------------------------------------------
        | RESOURCE UNIT
        |--------------------------------------------------------------------------
        */

        LEFT JOIN units u
            ON u.id = r.unit_id


        /*
        |--------------------------------------------------------------------------
        | RESOURCE CATEGORY
        |--------------------------------------------------------------------------
        */

        LEFT JOIN resource_categories rc
            ON rc.id = r.category_id


        WHERE ri.id = ?

        LIMIT 1
        ",
            [$id]
        )->fetch();
    }

    /**
     * UPDATE ITEM
     */
    public function update($id, $data)
    {
        return $this->db->query(
            "
        UPDATE resource_requisition_items

        SET
            description = '{$data['description']}',
            quantity = '{$data['quantity']}',
            remarks = '{$data['remarks']}'

        WHERE id = '$id'
        "
        );
    }

    /**
     * DELETE ITEM
     */
    public function delete($id)
    {

        return $this->db->query(

            "
        DELETE FROM resource_requisition_items

        WHERE id = '$id'

        "

        );
    }

    // find available item qty in stock
    public function getStockAvailability($resource_id)
    {
        return $this->db->query(
            "
        SELECT 
            SUM(quantity) AS available_qty

        FROM inventory_location_stock

        WHERE inventory_id = ?
        ",
            [$resource_id]

        )->fetch();
    }
}
