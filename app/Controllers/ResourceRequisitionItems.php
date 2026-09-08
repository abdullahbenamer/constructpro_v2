<?php

class ResourceRequisitionItems extends Controller
{

    private $itemModel;


    public function __construct()
    {
        $this->itemModel = $this->model('ResourceRequisitionItem');
    }

    /**
     * Ensure the parent requisition is still editable
     */
    private function validateDraftRequisition($requisition_id)
    {
        $reqModel = $this->model('ResourceRequisition');

        $requisition = $reqModel->getById($requisition_id);

        if (!$requisition || $requisition->status != 'DRAFT') {

            header(
                'Location: ' .
                    URLROOT .
                    '/ResourceRequisitions/details/' .
                    $requisition_id
            );

            exit;
        }

        return $requisition;
    }

    /**
     * CREATE ITEM PAGE
     */
    public function create($requisition_id)
    {
        AuthHelper::can('projects.view');

        $this->validateDraftRequisition($requisition_id);

        $resourceModel = $this->model('Resource');
        $inventoryModel = $this->model('Inventory');

        $data = [

            'requisition_id' => $requisition_id,

            'resources' => $resourceModel->getNonMaterialResources(),

            'inventory' => $inventoryModel->getAll()

        ];

        $this->view('resource-requisition-items/create', $data);
    }

    /**
     * STORE ITEM
     */
public function store()
{
    AuthHelper::can('projects.view');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );
        exit;
    }
//   echo '<pre>';
//     print_r($_POST);
//     echo '</pre>';
//     exit;
    $requisitionId = (int)($_POST['requisition_id'] ?? 0);

    $this->validateDraftRequisition($requisitionId);

    $resourceSource = $_POST['resource_source'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | DETERMINE RESOURCE ID
    |--------------------------------------------------------------------------
    */

    if ($resourceSource === 'INVENTORY') {

        $resourceId =
            (int)($_POST['inventory_id'] ?? 0);

        if ($resourceId <= 0) {
            $_SESSION['error'] =
                'Please select a material item.';

            header(
                'Location: ' .
                URLROOT .
                '/ResourceRequisitionItems/create/' .
                $requisitionId
            );

            exit;
        }

    } elseif ($resourceSource === 'RESOURCE') {

        $resourceId =
            (int)($_POST['non_inventory_resource'] ?? 0);

        if ($resourceId <= 0) {
            $_SESSION['error'] =
                'Please select a resource.';

            header(
                'Location: ' .
                URLROOT .
                '/ResourceRequisitionItems/create/' .
                $requisitionId
            );

            exit;
        }

    } else {

        $_SESSION['error'] =
            'Invalid resource source.';

        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionItems/create/' .
            $requisitionId
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ITEM
    |--------------------------------------------------------------------------
    */

    $data = [

        'requisition_id' =>
            $requisitionId,

        'resource_source' =>
            $resourceSource,

        'resource_id' =>
            $resourceId,

        'description' =>
            trim($_POST['description'] ?? ''),

        'quantity' =>
            (float)($_POST['quantity'] ?? 0),

        'uom' =>
            trim($_POST['uom'] ?? ''),

        'remarks' =>
            trim($_POST['remarks'] ?? '')
    ];

    $itemModel =
        $this->model('ResourceRequisitionItem');

    if ($itemModel->create($data)) {

        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions/details/' .
            $requisitionId
        );

        exit;
    }

    $_SESSION['error'] =
        'Unable to create requisition item.';

    header(
        'Location: ' .
        URLROOT .
        '/ResourceRequisitionItems/create/' .
        $requisitionId
    );

    exit;
}

public function edit($id)
{
    AuthHelper::can('projects.view');

    $item = $this->itemModel->getById($id);

    if (!$item) {

        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | PARENT REQUISITION MUST STILL BE DRAFT
    |--------------------------------------------------------------------------
    */

    $this->validateDraftRequisition(
        $item->requisition_id
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD BOTH RESOURCE TYPES
    |--------------------------------------------------------------------------
    */

    $resourceModel = $this->model('Resource');
    $inventoryModel = $this->model('Inventory');


    $data = [

        'item' => $item,

        /*
        | Non-material resources
        */
        'resources' =>
            $resourceModel->getNonMaterialResources(),

        /*
        | Material inventory
        */
        'inventory' =>
            $inventoryModel->getAll()

    ];


    $this->view(
        'resource-requisition-items/edit',
        $data
    );
}

    /**
     * Update Item
     */
    public function update($id)
    {

        AuthHelper::can('projects.view');


        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            header(
                'Location: ' .
                    URLROOT .
                    '/ResourceRequisitions'
            );

            exit;
        }



        $item = $this->itemModel->getById($id);



        if (!$item) {

            header(
                'Location: ' .
                    URLROOT .
                    '/ResourceRequisitions'
            );

            exit;
        }



        $this->validateDraftRequisition(
            $item->requisition_id
        );



        $data = [

            'description' => $_POST['description'],

            'quantity' => $_POST['quantity'],

            'remarks' => $_POST['remarks']

        ];



        $this->itemModel->update(
            $id,
            $data
        );



        header(
            'Location: ' .
                URLROOT .
                '/ResourceRequisitions/details/' .
                $item->requisition_id
        );

        exit;
    }

    /**
     * Delete Item
     */
    /**
     * Delete Item
     */
    public function delete($id)
    {

        AuthHelper::can('projects.view');


        $item = $this->itemModel->getById($id);


        if (!$item) {

            header(
                'Location: ' .
                    URLROOT .
                    '/ResourceRequisitions'
            );

            exit;
        }



        $this->validateDraftRequisition(
            $item->requisition_id
        );



        $this->itemModel->delete($id);



        header(
            'Location: ' .
                URLROOT .
                '/ResourceRequisitions/details/' .
                $item->requisition_id
        );

        exit;
    }
}
