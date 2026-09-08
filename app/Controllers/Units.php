<?php

class Units extends Controller
{

    /**
     * LIST
     */
    public function index()
    {

        AuthHelper::can('projects.view');

        $model = $this->model('UnitModel');

        $data = [

            'units' => $model->getAll()

        ];

        $this->view('units/index', $data);

    }



    /**
     * CREATE PAGE
     */
    public function create()
    {

        AuthHelper::can('projects.view');

        $this->view('units/create');

    }



    /**
     * STORE
     */
    public function store()
    {

        AuthHelper::can('projects.view');

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            header('Location: ' . URLROOT . '/Units');
            exit;

        }

        $model = $this->model('UnitModel');

        $data = [

            'unit_code'   => trim($_POST['unit_code']),
            'unit_name'   => trim($_POST['unit_name']),
            'unit_name_a' => trim($_POST['unit_name_a']),
            'description' => trim($_POST['description']),
            'status'      => $_POST['status']

        ];

        $model->create($data);

        header('Location: ' . URLROOT . '/Units');
        exit;

    }



    /**
     * EDIT PAGE
     */
    public function edit($id)
    {

        AuthHelper::can('projects.view');

        $model = $this->model('UnitModel');

        $data = [

            'unit' => $model->getById($id)

        ];

        $this->view('units/edit', $data);

    }



    /**
     * UPDATE
     */
    public function update($id)
    {

        AuthHelper::can('projects.view');

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            header('Location: ' . URLROOT . '/Units');
            exit;

        }

        $model = $this->model('UnitModel');

        $data = [

            'unit_code'   => trim($_POST['unit_code']),
            'unit_name'   => trim($_POST['unit_name']),
            'unit_name_a' => trim($_POST['unit_name_a']),
            'description' => trim($_POST['description']),
            'status'      => $_POST['status']

        ];

        $model->update($id, $data);

        header('Location: ' . URLROOT . '/Units');
        exit;

    }



    /**
     * DELETE
     */
    public function delete($id)
    {

        AuthHelper::can('projects.view');

        $model = $this->model('UnitModel');

        $model->delete($id);

        header('Location: ' . URLROOT . '/Units');
        exit;

    }

}