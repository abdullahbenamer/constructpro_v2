<?php

class Purchases extends Controller
{
    public function index()
    {
        $model = $this->model('Purchase');

        $data['purchases'] = $model->getAll();

        $this->view('purchases/index', $data);
    }
}