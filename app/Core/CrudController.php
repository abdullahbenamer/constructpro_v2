<?php
class CrudController extends Controller {
    protected $model_name;
    protected $singular;
    
    public function __construct($model_name, $singular) {
        $this->model_name = $model_name;
        $this->singular = $singular;
    }
    
    public function index() {
        $model = $this->model($this->model_name);
        $data[$this->singular . 's'] = $model->getAll();
        $data['title'] = ucfirst($this->singular);
        $this->view($this->singular . '/index', $data);
    }
    
    public function create() {
        if ($_POST) {
            $model = $this->model($this->model_name);
            if ($model->create($_POST)) {
                header('Location: ' . URLROOT . '/' . $this->singular);
                exit;
            }
        }
        $this->view($this->singular . '/create');
    }
    
    public function edit($id) {
        $model = $this->model($this->model_name);
        if ($_POST) {
            if ($model->update($id, $_POST)) {
                header('Location: ' . URLROOT . '/' . $this->singular);
                exit;
            }
        }
        $data[$this->singular] = $model->getById($id);
        $this->view($this->singular . '/edit', $data);
    }
    
    public function delete($id) {
        $model = $this->model($this->model_name);
        $model->delete($id);
        header('Location: ' . URLROOT . '/' . $this->singular);
        exit;
    }
}