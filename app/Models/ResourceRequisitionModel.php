<?php

require_once '../app/Core/Model.php';

class ResourceRequisitionModel extends Model
{
    /*
    |------------------------------------------------------
    | Next Requisition Number
    |-----------------------------------------------------------
    */
    public function nextNumber()
    {
        return 'REQ-' . date('ymdHis');
    }

    /*
    |---------------------------------------------------------
    | Get All
    |--------------------------------------------------------
    */
    public function getAll()
    {
        return $this->db->query("
            SELECT
                rr.*,
                p.title AS project_name,
                u.full_name AS requested_by_name

            FROM resource_requisitions rr

            LEFT JOIN projects p
                ON rr.project_id = p.id

            LEFT JOIN users u
                ON rr.requested_by = u.id

            ORDER BY rr.id DESC
        ")->fetchAll();
    }

    /*
    |-----------------------------------------------------------
    | Get By ID
    |----------------------------------------------------------
    */
 public function getById($id)
{
    return $this->db->query("
        SELECT

            rr.*,
            rr.req_number AS requisition_no,

            p.title AS project_name,

            l.name AS target_warehouse_name,

            u1.full_name AS requested_by_name,

            u2.full_name AS submitted_by_name,

            u3.full_name AS approved_by_name

        FROM resource_requisitions rr

        LEFT JOIN projects p
            ON p.id = rr.project_id

        LEFT JOIN inventory_locations l
            ON l.id = rr.target_warehouse_id

        LEFT JOIN users u1
            ON u1.id = rr.requested_by

        LEFT JOIN users u2
            ON u2.id = rr.submitted_by

        LEFT JOIN users u3
            ON u3.id = rr.approved_by

        WHERE rr.id = ?

        LIMIT 1
    ", [$id])->fetch();
}

    /*
    |---------------------------------------------------------
    | Create
    |-----------------------------------------------------------
    */
public function create($data)
{
    $this->db->query("
        INSERT INTO resource_requisitions
        (
            req_number,
            project_id,
            request_date,
            required_date,
            priority,
            target_warehouse_id,
            delivery_method,
            remarks,
            requested_by,
            status
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ", [

        $data['req_number'],
        $data['project_id'],
        $data['request_date'],
        $data['required_date'],
        $data['priority'],
        $data['target_warehouse_id'],
        $data['delivery_method'],
        $data['remarks'],
        $_SESSION['user_id'],
        'DRAFT'

    ]);

    return $this->db->lastInsertId();
}
    /*  
    |----------------------------------------------------
    | Update
    |----------------------------------------------------
    */
public function update($id, $data)
{
    return $this->db->query("
        UPDATE resource_requisitions

        SET

            project_id           = ?,
            request_date         = ?,
            required_date        = ?,
            priority             = ?,
            target_warehouse_id  = ?,
            delivery_method      = ?,
            remarks              = ?

        WHERE id = ?
    ", [

        $data['project_id'],
        $data['request_date'],
        $data['required_date'],
        $data['priority'],
        $data['target_warehouse_id'],
        $data['delivery_method'],
        $data['remarks'],
        $id

    ]);
}

    /**
 * SUBMIT REQUISITION
 */

public function submit($id, $user_id)
{
    // Update requisition status
    $result = $this->db->query(
        "
        UPDATE resource_requisitions
        SET
            status = 'SUBMITTED',
            submitted_by = ?,
            submitted_at = NOW()
        WHERE id = ?
          AND status = 'DRAFT'
        ",
        [
            $user_id,
            $id
        ]
    );

    // Record submission in approval history
    $this->db->query(
        "
        INSERT INTO resource_requisition_approvals
        (
            requisition_id,
            action,
            action_by,
            remarks,
            action_date
        )
        VALUES
        (
            ?,
            'SUBMITTED',
            ?,
            NULL,
            NOW()
        )
        ",
        [
            $id,
            $user_id
        ]
    );

    return $result;
}

    /*
    |-------------------------------------------------------
    | Delete
    |--------------------------------------------------------
    */
    public function delete($id)
    {
        return $this->db->query("
            DELETE
            FROM resource_requisitions
            WHERE id = ?
        ", [$id]);
    }

    public function approve($id, $user_id, $remarks = null)
{
    /*
    |--------------------------------------------------------------------------
    | UPDATE REQUISITION
    |--------------------------------------------------------------------------
    */

    $result = $this->db->query(

        "
        UPDATE resource_requisitions
        SET

            status = 'APPROVED',

            approved_by = ?,

            approved_at = NOW(),

            approval_remarks = ?

        WHERE id = ?
          AND status = 'SUBMITTED'
        ",

        [

            $user_id,
            $remarks,
            $id

        ]

    );

    /*
    |--------------------------------------------------------------------------
    | RECORD APPROVAL HISTORY
    |--------------------------------------------------------------------------
    */

    $this->db->query(

        "
        INSERT INTO resource_requisition_approvals
        (
            requisition_id,
            action,
            action_by,
            remarks,
            action_date
        )
        VALUES
        (
            ?,
            'APPROVED',
            ?,
            ?,
            NOW()
        )
        ",

        [

            $id,
            $user_id,
            $remarks

        ]

    );

    return $result;
}

public function reject($id, $user_id, $remarks = null)
{
    /*
    |--------------------------------------------------------------------------
    | UPDATE REQUISITION
    |--------------------------------------------------------------------------
    */

    $result = $this->db->query(

        "
        UPDATE resource_requisitions
        SET

            status = 'REJECTED',

            approved_by = ?,

            approved_at = NOW(),

            approval_remarks = ?

        WHERE id = ?
          AND status = 'SUBMITTED'
        ",

        [

            $user_id,
            $remarks,
            $id

        ]

    );

    /*
    |--------------------------------------------------------------------------
    | RECORD REJECTION HISTORY
    |--------------------------------------------------------------------------
    */

    $this->db->query(

        "
        INSERT INTO resource_requisition_approvals
        (
            requisition_id,
            action,
            action_by,
            remarks,
            action_date
        )
        VALUES
        (
            ?,
            'REJECTED',
            ?,
            ?,
            NOW()
        )
        ",

        [

            $id,
            $user_id,
            $remarks

        ]

    );

    return $result;
}

public function getApprovalHistory($requisition_id)
{
    return $this->db->query("
        SELECT
            rra.*,
            u.full_name AS action_by_name

        FROM resource_requisition_approvals rra

        LEFT JOIN users u
            ON u.id = rra.action_by

        WHERE rra.requisition_id = ?

        ORDER BY rra.action_date ASC, rra.id ASC
    ", [
        $requisition_id
    ])->fetchAll();
}

}