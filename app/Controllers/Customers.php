<?php
// Remove CrudController require - use direct methods
class Customers extends Controller
{
    public function index()
    {
        AuthHelper::can('customers.view');
    
        $model = $this->model('Customer');
        $data['customers'] = $model->getCustomers('active');
        $data['title'] = 'Customers';
        $this->view('customers/index', $data);
    }

    // CREATE (inherited from CrudController but direct)
    public function create()
    {
        if ($_POST) {
            $model = $this->model('Customer');
            if ($model->create($_POST)) {
                header('Location: ' . URLROOT . '/customers');
                exit;
            }
        }
        $this->view('customers/create');
    }

    // EDIT
    public function edit($id) {
        $model = $this->model('Customer');
    
        if ($_POST) {
            if ($model->update($id, $_POST)) {
                header('Location: ' . URLROOT . '/customers');
                exit;
            }
        }
    
        $item = $model->getById($id);
    
        if (!$item) {
            header('Location: ' . URLROOT . '/customers');
            exit;
        }
    
        $data['customer'] = $item; 
        $this->view('customers/edit', $data);
    }

//  delete a Customer
    public function delete($id)
{
    AuthHelper::can('customers.delete');

    $model = $this->model('Customer');

    try {

        if ($model->delete($id)) {
            FlashHelper::success('Customer deleted successfully.');
        } else {
            FlashHelper::warning('Customer not found.');
        }

    } catch (PDOException $e) {

        // MySQL foreign key violation
        if ($e->getCode() == '23000') {

            FlashHelper::error(
                'This customer cannot be deleted because it is linked to one or more projects.'
            );

        } else {

            FlashHelper::error(
                'Unable to delete customer.'
            );
         
        }
    }

    header('Location: ' . URLROOT . '/customers');
    exit;
}

    public function info($id)
{
    $model = $this->model('Customer');

    $customer = $model->getById($id);

    if (!$customer) {
        FlashHelper::error('Customer not found');
        header('Location: ' . URLROOT . '/customers');
        exit;
    }

    $data['customer'] = $customer;

    $this->view('customers/customer_info', $data);
}

public function details($id)
{
    $customerModel = $this->model('CustomerModel');

    $customer = $customerModel->getCustomerById($id);
    $projects = $customerModel->getCustomerProjects($id);

    if (!$customer) {
        header("Location: " . URLROOT . "/customers");
        exit;
    }

    $data = [
        'customer' => $customer,
        'projects' => $projects
    ];

    $this->view('customers/details', $data);
}
}
