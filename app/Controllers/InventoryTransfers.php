<?php

class InventoryTransfers extends Controller
{
    private $inventoryModel;

    public function __construct()
    {
        $this->inventoryModel = new InventoryModel();
    }

    public function index()
    {
        $transferModel = $this->model('InventoryTransfer');

        $data['transfers'] =
            $transferModel->getAll();

        $this->view(
            'inventory-transfers/index',
            $data
        );
    }

    public function create()
    {
        $inventoryModel = $this->model('Inventory');
        $locationModel  = $this->model('InventoryLocation');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $inventory_id = (int)$_POST['inventory_id'];

            $from_location_id = (int)$_POST['from_location_id'];

            $to_location_id = (int)$_POST['to_location_id'];

            $quantity = (float)$_POST['quantity'];

            $reference = trim($_POST['reference'] ?? '');

            $notes = trim($_POST['notes'] ?? '');
            
            $service = new InventoryService(

                $this->model('InventoryLocationStock'),
                $this->model('InventoryMovement'),
                $this->model('InventoryTransfer')

            );

            try {

                $service->transfer([

                    'inventory_id'      => $inventory_id,

                    'from_location_id'  => $from_location_id,

                    'to_location_id'    => $to_location_id,

                    'quantity'          => $quantity,

                    'reference'         => $reference,

                    'notes'             => $notes,

                    'created_by'        => $_SESSION['user_id']

                ]);

                FlashHelper::success(
                    'Transfer completed successfully.'
                );

                header('Location: ' . URLROOT . '/inventorytransfers');
                exit;
            } catch (Exception $e) {

                FlashHelper::error(
                    $e->getMessage()
                );

                header('Location: ' . URLROOT . '/inventorytransfers/create');
                exit;
            }
        }

        // GET request

        $data['inventory'] =
            $inventoryModel->getAll();

        $data['locations'] =
            $locationModel->getAll();

        $this->view(
            'inventory-transfers/create',
            $data
        );
    }

    public function getLocationStock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        $inventory_id = $_POST['inventory_id'] ?? 0;
        $location_id  = $_POST['location_id'] ?? 0;

        $stockModel = $this->model('InventoryLocationStock');

        $stock = $stockModel->getStock(
            $inventory_id,
            $location_id
        );

        $qty = $stock->quantity ?? 0;

        echo json_encode([
            'quantity' => $qty
        ]);
    }

    public function getBySku()
    {
        header('Content-Type: application/json');

        try {

            $value = $_POST['value'] ?? null;

            if (!$value) {
                echo json_encode(null);
                return;
            }

            $item = $this->inventoryModel->getBySku($value);

            echo json_encode($item ?: null);
        } catch (Throwable $e) {

            http_response_code(500);

            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getItemLocations()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            exit;
        }

        header('Content-Type: application/json');

        $inventory_id = (int)($_POST['inventory_id'] ?? 0);

        $stockModel = $this->model('InventoryLocationStock');

        // $locations = $stockModel->getLocationsByItem($inventory_id);
        $locations = $stockModel->getAvailableItemLocations($inventory_id);

        echo json_encode($locations ?: []);
    }

    public function reverse($id)
{
    try {

        $service = new InventoryTransferService(

            $this->model('InventoryLocationStock'),

            $this->model('InventoryMovement'),

            $this->model('InventoryTransfer')

        );

        $result =
            $service->reverse((int)$id);

        FlashHelper::success(
            $result['message']
        );

    } catch (Throwable $e) {

        FlashHelper::error(
            $e->getMessage()
        );
    }

    header(
        'Location: ' .
        URLROOT .
        '/inventorytransfers'
    );

    exit;
}
}
