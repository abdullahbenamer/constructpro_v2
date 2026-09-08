<?php

require_once '../app/Core/Model.php';


class ResourceModel extends Model
{


    /**
     * GET ALL RESOURCES
     */
   public function getAll()
{
    return $this->db->query("
        SELECT

            r.*,

            rc.category_name,

            u.unit_name

        FROM resources r

        LEFT JOIN resource_categories rc
            ON rc.id = r.category_id

        LEFT JOIN units u
            ON u.id = r.unit_id

        ORDER BY r.resource_name
    ")->fetchAll();
}


    /**
     * GET RESOURCE BY ID
     */
    public function getById($id)
    {

        return $this->db->query(
            "
            SELECT

                r.*,

                rc.category_name,

                u.unit_name


            FROM resources r


            LEFT JOIN resource_categories rc

                ON rc.id = r.category_id



            LEFT JOIN units u

                ON u.id = r.unit_id



            WHERE r.id = '$id'

            "
        )->fetch();

    }





    /**
     * CREATE RESOURCE
     */
    public function create($data)
    {

        return $this->db->query(
            "
            INSERT INTO resources

            (

                resource_code,

                resource_name,

                resource_name_a,

                category_id,

                resource_type,

                unit_id,

                description,

                status

            )


            VALUES


            (

                '{$data['resource_code']}',

                '{$data['resource_name']}',

                '{$data['resource_name_a']}',

                '{$data['category_id']}',

                '{$data['resource_type']}',

                '{$data['unit_id']}',

                '{$data['description']}',

                '{$data['status']}'

            )

            "
        );

        

    }





    /**
     * UPDATE RESOURCE
     */
    public function update($id,$data)
    {

        return $this->db->query(
            "
            UPDATE resources

            SET


                resource_code =
                '{$data['resource_code']}',



                resource_name =
                '{$data['resource_name']}',



                resource_name_a =
                '{$data['resource_name_a']}',



                category_id =
                '{$data['category_id']}',



                resource_type =
                '{$data['resource_type']}',



                unit_id =
                '{$data['unit_id']}',



                description =
                '{$data['description']}',



                status =
                '{$data['status']}'



            WHERE id='$id'

            "
        );

    }





    /**
     * DELETE RESOURCE
     */
    public function delete($id)
    {

        return $this->db->query(
            "
            DELETE FROM resources

            WHERE id='$id'

            "
        );

    }

public function getNonMaterialResources()
{
    return $this->db->query(
        "
        SELECT

            r.*,
            rc.category_name,
            u.unit_name

        FROM resources r

        LEFT JOIN resource_categories rc
            ON rc.id = r.category_id

        LEFT JOIN units u
            ON u.id = r.unit_id

        WHERE r.status = 'ACTIVE'
          AND r.resource_type <> 'MATERIAL'

        ORDER BY
            r.resource_type,
            r.resource_name
        "
    )->fetchAll();
}

}