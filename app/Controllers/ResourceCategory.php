<?php

class ResourceCategories extends Controller
{


    /**
     * LIST RESOURCE CATEGORIES
     */
    public function index()
    {

        AuthHelper::can('projects.view');


        $model = $this->model('ResourceCategoryModel');


        $data = [

            'categories' => $model->getAll()

        ];


        $this->view(
            'resource-categories/index',
            $data
        );

    }





    /**
     * CREATE PAGE
     */
    public function create()
    {

        AuthHelper::can('projects.view');


        $this->view(
            'resource-categories/create'
        );

    }





    /**
     * STORE CATEGORY
     */
    public function store()
    {

        AuthHelper::can('projects.view');


        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            header(
                'Location: ' . URLROOT . '/ResourceCategories'
            );

            exit;

        }



        $model = $this->model('ResourceCategoryModel');



        $data = [

            'category_code'   => trim($_POST['category_code']),

            'category_name'   => trim($_POST['category_name']),

            'category_name_a' => trim($_POST['category_name_a']),

            'description'     => trim($_POST['description']),

            'status'          => $_POST['status']

        ];



        $model->create($data);



        header(
            'Location: ' . URLROOT . '/ResourceCategories'
        );

        exit;

    }





    /**
     * EDIT PAGE
     */
    public function edit($id)
    {

        AuthHelper::can('projects.view');


        $model = $this->model('ResourceCategoryModel');



        $data = [

            'category' => $model->getById($id)

        ];



        $this->view(
            'resource-categories/edit',
            $data
        );

    }





    /**
     * UPDATE CATEGORY
     */
    public function update($id)
    {

        AuthHelper::can('projects.view');


        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            header(
                'Location: ' . URLROOT . '/ResourceCategories'
            );

            exit;

        }



        $model = $this->model('ResourceCategoryModel');



        $data = [

            'category_code'   => trim($_POST['category_code']),

            'category_name'   => trim($_POST['category_name']),

            'category_name_a' => trim($_POST['category_name_a']),

            'description'     => trim($_POST['description']),

            'status'          => $_POST['status']

        ];



        $model->update($id,$data);



        header(
            'Location: ' . URLROOT . '/ResourceCategories'
        );

        exit;

    }





    /**
     * DELETE CATEGORY
     */
    public function delete($id)
    {

        AuthHelper::can('projects.view');


        $model = $this->model('ResourceCategoryModel');



        $model->delete($id);



        header(
            'Location: ' . URLROOT . '/ResourceCategories'
        );

        exit;

    }


}