<?php

class Technicians extends Controller
{
    public function index()
    {
        $model = $this->model('Technician');

        $data['technicians'] =
            $model->getAll();

        $this->view('technicians/index', $data);
    }

    public function details($id)
    {
        $model = $this->model('Technician');

        $technician =
            $model->getById($id);

        if (!$technician) {

            header(
                'Location: ' .
                URLROOT .
                '/technicians'
            );

            exit;
        }

        $data['technician'] =
            $technician;

        $this->view(
            'technicians/details',
            $data
        );
    }
}