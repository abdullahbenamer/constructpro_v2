<?php

class InventoryLocations extends Controller
{
    public function index()
    {
        $model = $this->model('InventoryLocation');

        $data['locations'] = $model->getAll();

        $this->view('inventory-locations/index', $data);
    }

    public function create()
    {
        $model = $this->model('InventoryLocation');

        $storekeepers = $model->getStorekeepers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $storekeeper_id = $_POST['storekeeper_id'] ?? null;

            if ($storekeeper_id === '' || !is_numeric($storekeeper_id)) {
                $storekeeper_id = null;
            }

            $result = $model->create([
                'code'           => trim($_POST['code']),
                'name'           => trim($_POST['name']),
                'address'        => trim($_POST['address'] ?? ''),
                'storekeeper_id' => $storekeeper_id,
                'mobile'         => trim($_POST['mobile'] ?? ''),
                'notes'          => trim($_POST['notes'] ?? '')
            ]);

        if ($result['success']) {

    $model->saveLocationUsers(
        $result['id'],
        $_POST['user_locations'] ?? []
    );

    header('Location: ' . URLROOT . '/inventorylocations');
    exit;
}

            $data['error'] = $result['message'] ?? 'Unable to save location';
        }

        $data['storekeepers'] = $storekeepers;
        $data['users'] = $model->getUsers();

        $this->view('inventory-locations/create', $data);
    }

    public function details($id)
    {
        $locationModel = $this->model('InventoryLocation');
        $stockModel = $this->model('InventoryLocationStock');

        $location = $locationModel->getById($id);

        if (!$location) {
            die("Location not found");
        }

        $items = $stockModel->getLocationInventory($id);

        $data['location'] = $location;
        $data['items'] = $items;
        $data['locations'] = $locationModel->getAll();

        $this->view('inventory-locations/view', $data);
    }

    public function edit($id)
    {
        $model = $this->model('InventoryLocation');

        $location = $model->getById($id);

        if (!$location) {
            header('Location: ' . URLROOT . '/inventorylocations');
            exit;
        }

        $storekeepers = $model->getStorekeepers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $storekeeper_id =
                $_POST['storekeeper_id'] ?? null;

            if ($storekeeper_id === '' || !is_numeric($storekeeper_id)) {
                $storekeeper_id = null;
            }

            $result = $model->update($id, [

                'code' => trim($_POST['code']),
                'name' => trim($_POST['name']),
                'address' => trim($_POST['address'] ?? ''),
                'storekeeper_id' => $storekeeper_id,
                'mobile' => trim($_POST['mobile'] ?? ''),
                'notes' => trim($_POST['notes'] ?? '')
            ]);

      if ($result['success']) {

    $model->saveLocationUsers(
        $id,
        $_POST['user_locations'] ?? []
    );

    $_SESSION['success'] = 'Location updated successfully';

    header('Location: ' . URLROOT . '/inventorylocations');
    exit;
}

            $data['error'] =
                $result['message'];
        }

        $assigned = $model->getLocationUsers($id);

$assignedUsers = array_map(function($row){
    return $row->user_id;
}, $assigned);

$data['location'] = $location;
$data['storekeepers'] = $storekeepers;
$data['users'] = $model->getUsers();
$data['assignedUsers'] = $assignedUsers;

        $this->view(
            'inventory-locations/edit',
            $data
        );
    }

    public function delete($id)
    {
        $model = $this->model('InventoryLocation');

        if ($model->hasStock($id)) {

            FlashHelper::error(
                'Cannot delete location because it contains stock.'
            );

            header(
                'Location: ' . URLROOT . '/inventorylocations'
            );

            exit;
        }

        $model->delete($id);

        FlashHelper::success(
            'Location deleted successfully.'
        );

        header(
            'Location: ' . URLROOT . '/inventorylocations'
        );

        exit;
    }
}
