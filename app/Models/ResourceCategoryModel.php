<?php

require_once '../app/Core/Model.php';


class ResourceCategoryModel extends Model
{


    public function getAll()
    {

        return $this->db->query(
            "
            SELECT *

            FROM resource_categories

            ORDER BY category_name ASC
            "
        )->fetchAll();

    }





    public function getById($id)
    {

        return $this->db->query(
            "
            SELECT *

            FROM resource_categories

            WHERE id = '$id'
            "
        )->fetch();

    }





    public function create($data)
    {

        return $this->db->query(
            "
            INSERT INTO resource_categories
            (
                category_code,
                category_name,
                category_name_a,
                description,
                status
            )

            VALUES

            (
                '{$data['category_code']}',
                '{$data['category_name']}',
                '{$data['category_name_a']}',
                '{$data['description']}',
                '{$data['status']}'
            )
            "
        );

    }





    public function update($id,$data)
    {

        return $this->db->query(
            "
            UPDATE resource_categories

            SET

                category_code =
                '{$data['category_code']}',

                category_name =
                '{$data['category_name']}',

                category_name_a =
                '{$data['category_name_a']}',

                description =
                '{$data['description']}',

                status =
                '{$data['status']}'

            WHERE id='$id'
            "
        );

    }





    public function delete($id)
    {

        return $this->db->query(
            "
            DELETE

            FROM resource_categories

            WHERE id='$id'
            "
        );

    }


}