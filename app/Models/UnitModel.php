<?php

require_once '../app/Core/Model.php';

class UnitModel extends Model
{

    /**
     * GET ALL UNITS
     */
    public function getAll()
    {

        return $this->db->query(
            "
            SELECT *
            FROM units
            ORDER BY unit_name
            "
        )->fetchAll();

    }



    /**
     * GET UNIT BY ID
     */
    public function getById($id)
    {

        return $this->db->query(
            "
            SELECT *
            FROM units
            WHERE id = '$id'
            "
        )->fetch();

    }



    /**
     * CREATE UNIT
     */
    public function create($data)
    {

        return $this->db->query(
            "
            INSERT INTO units
            (
                unit_code,
                unit_name,
                unit_name_a,
                description,
                status
            )

            VALUES

            (
                '{$data['unit_code']}',
                '{$data['unit_name']}',
                '{$data['unit_name_a']}',
                '{$data['description']}',
                '{$data['status']}'
            )
            "
        );

    }



    /**
     * UPDATE UNIT
     */
    public function update($id, $data)
    {

        return $this->db->query(
            "
            UPDATE units

            SET

                unit_code      = '{$data['unit_code']}',
                unit_name      = '{$data['unit_name']}',
                unit_name_a    = '{$data['unit_name_a']}',
                description    = '{$data['description']}',
                status         = '{$data['status']}'

            WHERE id = '$id'
            "
        );

    }



    /**
     * DELETE UNIT
     */
    public function delete($id)
    {

        return $this->db->query(
            "
            DELETE
            FROM units
            WHERE id = '$id'
            "
        );

    }

}