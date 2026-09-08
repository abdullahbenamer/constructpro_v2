<?php

class ResourceRequisitionFulfillments extends Controller
{
    private $fulfillmentModel;


    public function __construct()
    {
        $this->fulfillmentModel =
            $this->model(
                'ResourceRequisitionFulfillment'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX: GET AVAILABLE STOCK
    |--------------------------------------------------------------------------
    */
    public function getStockAvailability()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header('Content-Type: application/json');

            echo json_encode([
                'quantity' => 0
            ]);

            exit;
        }


        $inventory_id =
            (int) ($_POST['inventory_id'] ?? 0);


        $location_id =
            (int) ($_POST['location_id'] ?? 0);


        $stock = null;


        if (
            $inventory_id > 0
            &&
            $location_id > 0
        ) {

            $stock =
                $this->fulfillmentModel
                ->getLocationStock(
                    $inventory_id,
                    $location_id
                );
        }


        header('Content-Type: application/json');


        echo json_encode([

            'quantity' =>
            $stock
                ? (float) $stock->quantity
                : 0,

            'inventory_name' =>
            $stock->inventory_name
                ?? null,

            'sku' =>
            $stock->sku
                ?? null,

            'uom' =>
            $stock->base_unit
                ?? null,

            'location_name' =>
            $stock->location_name
                ?? null

        ]);


        exit;
    }

    /*
    |------------------------------------------------------------------
    | INDEX
    | LIST FULFILLMENTS FOR A REQUISITION
    |------------------------------------------------------------------
    */

    public function index($requisition_id)
    {
        AuthHelper::can('projects.view');


        /*
        |--------------------------------------------------------------
        | GET REQUISITION
        |--------------------------------------------------------------
        */

        $requisition =
            $this->fulfillmentModel
            ->getRequisition($requisition_id);


        if (!$requisition) {

            $_SESSION['error'] =
                'Resource requisition not found.';


            header(
                'Location: ' .
                    URLROOT .
                    '/ResourceRequisitions'
            );

            exit;
        }


        /*
        |--------------------------------------------------------------
        | GET FULFILLMENT HISTORY
        |--------------------------------------------------------------
        */

        $fulfillments =
            $this->fulfillmentModel
            ->getByRequisition($requisition_id);


        $data = [

            'requisition' => $requisition,

            'fulfillments' => $fulfillments

        ];


        $this->view(

            'resource-requisition-fulfillments/index',

            $data

        );
    }


    /*
    |------------------------------------------------------------------
    | CREATE
    |------------------------------------------------------------------
    */

   public function create($requisition_id)
{
    AuthHelper::can('projects.view');


    /*
    |--------------------------------------------------------------------------
    | GET REQUISITION
    |--------------------------------------------------------------------------
    */

    $requisition =
        $this->fulfillmentModel
            ->getRequisition($requisition_id);


    if (!$requisition) {

        $_SESSION['error'] =
            'Resource requisition not found.';

        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | ONLY APPROVED / PARTIAL REQUISITIONS
    |--------------------------------------------------------------------------
    */

    if (
        $requisition->status !== 'APPROVED'
        &&
        $requisition->status !== 'PARTIAL'
    ) {

        $_SESSION['error'] =
            'Only approved or partially fulfilled requisitions can be fulfilled.';

        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions/details/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET FULFILLABLE INVENTORY MATERIAL ITEMS ONLY
    |--------------------------------------------------------------------------
    */

   $items =
    $this->fulfillmentModel
    ->getFulfillableMaterialItems(
        $requisition_id
    );

// echo '<pre>';
// print_r($items);
// echo '</pre>';
// exit;
    /*
    |--------------------------------------------------------------------------
    | CHECK IF THERE ARE REMAINING MATERIAL ITEMS
    |--------------------------------------------------------------------------
    */

    if (empty($items)) {

        $_SESSION['error'] =
            'There are no remaining material items to fulfill.';

        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions/details/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD AVAILABLE INVENTORY LOCATIONS
    |--------------------------------------------------------------------------
    */

    foreach ($items as $item) {

        $item->locations =
            $this->fulfillmentModel
                ->getItemLocations(
                    $item->inventory_id
                );
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD VIEW
    |--------------------------------------------------------------------------
    */

    $data = [

        'requisition' =>
            $requisition,

        'items' =>
            $items,

        'fulfillment_number' =>
            ''

    ];


    $this->view(

        'resource-requisition-fulfillments/create',

        $data

    );
}

public function createResource($requisition_id)
{
    AuthHelper::can('projects.view');


    /*
    |--------------------------------------------------------------------------
    | GET REQUISITION
    |--------------------------------------------------------------------------
    */

    $requisition =
        $this->fulfillmentModel
            ->getRequisition(
                $requisition_id
            );


    if (!$requisition) {

        $_SESSION['error'] =
            'Resource requisition not found.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE REQUISITION STATUS
    |--------------------------------------------------------------------------
    */

    if (
        $requisition->status !== 'APPROVED'
        &&
        $requisition->status !== 'PARTIAL'
    ) {

        $_SESSION['error'] =
            'Only approved or partially fulfilled requisitions can be fulfilled.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions/details/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET FULFILLABLE RESOURCE ITEMS
    |--------------------------------------------------------------------------
    |
    | RESOURCE items include:
    |
    | EQUIPMENT
    | LABOR
    | SERVICE
    |
    */

    $items =
        $this->fulfillmentModel
            ->getFulfillableResourceItems(
                $requisition_id
            );

// echo '<pre>';
// print_r($items);
// echo '</pre>';
// exit;
    /*
    |--------------------------------------------------------------------------
    | CHECK IF THERE ARE REMAINING RESOURCE ITEMS
    |--------------------------------------------------------------------------
    */

    if (empty($items)) {

        $_SESSION['error'] =
            'There are no remaining resource items to fulfill.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions/details/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD VIEW
    |--------------------------------------------------------------------------
    */

    $data = [

        'requisition' =>
            $requisition,

        'items' =>
            $items

    ];


    $this->view(

        'resource-requisition-fulfillments/create_resource',

        $data

    );
}

// public function storeResource()
// {
//     AuthHelper::can('projects.view');


//     /*
//     |------------------------------------------------------------------
//     | ONLY POST
//     |------------------------------------------------------------------
//     */

//     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

//         header(
//             'Location: ' .
//             URLROOT .
//             '/ResourceRequisitions'
//         );

//         exit;
//     }


//     /*
//     |------------------------------------------------------------------
//     | REQUISITION ID
//     |------------------------------------------------------------------
//     */

//     $requisition_id =
//         (int) ($_POST['requisition_id'] ?? 0);


//     if ($requisition_id <= 0) {

//         $_SESSION['error'] =
//             'Invalid requisition.';

//         header(
//             'Location: ' .
//             URLROOT .
//             '/ResourceRequisitions'
//         );

//         exit;
//     }


//     /*
//     |------------------------------------------------------------------
//     | GET REQUISITION
//     |------------------------------------------------------------------
//     */

//     $requisition =
//         $this->fulfillmentModel
//             ->getRequisition(
//                 $requisition_id
//             );


//     if (!$requisition) {

//         $_SESSION['error'] =
//             'Resource requisition not found.';

//         header(
//             'Location: ' .
//             URLROOT .
//             '/ResourceRequisitions'
//         );

//         exit;
//     }


//     /*
//     |------------------------------------------------------------------
//     | GET POSTED ITEMS
//     |------------------------------------------------------------------
//     */

//     $postedItems =
//         $_POST['items'] ?? [];


//     /*
//     |------------------------------------------------------------------
//     | PREPARE FULFILLMENT ITEMS
//     |------------------------------------------------------------------
//     */

//     $items = [];


//     foreach ($postedItems as $item_id => $item) {

//         $quantity =
//             (float) ($item['quantity'] ?? 0);


//         /*
//         |--------------------------------------------------------------
//         | SKIP ZERO QUANTITY
//         |--------------------------------------------------------------
//         */

//         if ($quantity <= 0) {

//             continue;
//         }


//         $items[] = [

//             'requisition_item_id' =>
//                 (int) $item_id,

//             'quantity' =>
//                 $quantity,

//             'unit_cost' =>
//                 (float) (
//                     $item['unit_cost']
//                     ??
//                     0
//                 ),

//             'remarks' =>
//                 trim(
//                     $item['remarks']
//                     ??
//                     ''
//                 )
//         ];
//     }


//     /*
//     |------------------------------------------------------------------
//     | NOTHING TO FULFILL
//     |------------------------------------------------------------------
//     */

//     if (empty($items)) {

//         $_SESSION['error'] =
//             'Please enter a fulfillment quantity for at least one resource item.';

//         header(
//             'Location: ' .
//             URLROOT .
//             '/ResourceRequisitionFulfillments/createResource/' .
//             $requisition_id
//         );

//         exit;
//     }


//     /*
//     |------------------------------------------------------------------
//     | GENERATE FULFILLMENT NUMBER
//     |------------------------------------------------------------------
//     */

//     $fulfillment_no =
//         'RR-RES-' .
//         date('YmdHis') .
//         '-' .
//         random_int(100, 999);


//     /*
//     |------------------------------------------------------------------
//     | PREPARE DATA
//     |------------------------------------------------------------------
//     */

//     $data = [

//         'requisition_id' =>
//             $requisition_id,

//         'fulfillment_no' =>
//             $fulfillment_no,

//         'fulfillment_date' =>
//             $_POST['fulfillment_date']
//             ??
//             date('Y-m-d H:i:s'),

//         'fulfilled_by' =>
//             $_SESSION['user_id'],

//         'remarks' =>
//             trim(
//                 $_POST['remarks']
//                 ??
//                 ''
//             ),

//         'items' =>
//             $items
//     ];


//     /*
//     |------------------------------------------------------------------
//     | CREATE RESOURCE FULFILLMENT
//     |------------------------------------------------------------------
//     */

//     try {

//         $fulfillment_id =
//             $this->fulfillmentModel
//                 ->createResourceFulfillment(
//                     $data
//                 );


//         if (
//             empty($fulfillment_id)
//             ||
//             (int) $fulfillment_id <= 0
//         ) {

//             throw new Exception(
//                 'Resource fulfillment was created but no ID was returned.'
//             );
//         }


//         $_SESSION['success'] =
//             'Resource fulfillment completed successfully.';


//         header(
//             'Location: ' .
//             URLROOT .
//             '/ResourceRequisitionFulfillments/details/' .
//             (int) $fulfillment_id
//         );

//         exit;


//     } catch (Throwable $e) {

//         $_SESSION['error'] =
//             $e->getMessage();


//         header(
//             'Location: ' .
//             URLROOT .
//             '/ResourceRequisitionFulfillments/createResource/' .
//             $requisition_id
//         );

//         exit;
//     }
// }
    /*
    |------------------------------------------------------------------
    | STORE
    |------------------------------------------------------------------
    */

 public function store()
{
    AuthHelper::can('projects.view');


    /*
    |--------------------------------------------------------------------------
    | ONLY POST
    |--------------------------------------------------------------------------
    */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | REQUISITION ID
    |--------------------------------------------------------------------------
    */

    $requisition_id =
        (int) ($_POST['requisition_id'] ?? 0);


    if ($requisition_id <= 0) {

        $_SESSION['error'] =
            'Invalid requisition.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET REQUISITION
    |--------------------------------------------------------------------------
    */

    $requisition =
        $this->fulfillmentModel
            ->getRequisition($requisition_id);


    if (!$requisition) {

        $_SESSION['error'] =
            'Resource requisition not found.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE STATUS
    |--------------------------------------------------------------------------
    */

    if (
        $requisition->status !== 'APPROVED'
        &&
        $requisition->status !== 'PARTIAL'
    ) {

        $_SESSION['error'] =
            'This requisition is not available for fulfillment.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions/details/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET POSTED ITEMS
    |
    | Expected structure:
    |
    | items[REQUISITION_ITEM_ID][quantity]
    | items[REQUISITION_ITEM_ID][location_id]  INVENTORY ONLY
    | items[REQUISITION_ITEM_ID][unit_cost]
    | items[REQUISITION_ITEM_ID][remarks]
    |
    |--------------------------------------------------------------------------
    */

    $postedItems =
        $_POST['items'] ?? [];


    if (empty($postedItems)) {

        $_SESSION['error'] =
            'Please enter at least one fulfillment quantity.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionFulfillments/create/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARE ITEMS
    |--------------------------------------------------------------------------
    */

    $items = [];


    foreach ($postedItems as $item_id => $item) {

        $requisition_item_id =
            (int) $item_id;


        /*
        |--------------------------------------------------------------------------
        | VALIDATE ITEM ID
        |--------------------------------------------------------------------------
        */

        if ($requisition_item_id <= 0) {

            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | FULFILLMENT QUANTITY
        |--------------------------------------------------------------------------
        */

        $quantity =
            (float) ($item['quantity'] ?? 0);


        /*
        |--------------------------------------------------------------------------
        | SKIP ZERO QUANTITY
        |--------------------------------------------------------------------------
        */

        if ($quantity <= 0) {

            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | GET ORIGINAL REQUISITION ITEM
        |--------------------------------------------------------------------------
        */

        $reqItem =
            $this->fulfillmentModel
                ->getRequisitionItem(
                    $requisition_item_id
                );


        if (!$reqItem) {

            throw new Exception(
                'Invalid requisition item.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SECURITY CHECK
        |
        | Make sure the item belongs to this requisition.
        |--------------------------------------------------------------------------
        */

        if (
            (int) $reqItem->requisition_id
            !==
            $requisition_id
        ) {

            throw new Exception(
                'Invalid requisition item.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE RESOURCE SOURCE
        |--------------------------------------------------------------------------
        */

        if (
            $reqItem->resource_source !== 'INVENTORY'
            &&
            $reqItem->resource_source !== 'RESOURCE'
        ) {

            throw new Exception(
                'Invalid resource source.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PREVENT FULFILLING MORE THAN REMAINING QUANTITY
        |--------------------------------------------------------------------------
        */

      $remaining_quantity =
    (float) $reqItem->remaining_qty;

        if ($remaining_quantity <= 0) {

            throw new Exception(
                'Item "' .
                $reqItem->description .
                '" has already been fully fulfilled.'
            );
        }


        if ($quantity > $remaining_quantity) {

            throw new Exception(
                'Fulfillment quantity for "' .
                $reqItem->description .
                '" cannot exceed the remaining quantity of ' .
                $remaining_quantity .
                '.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UNIT COST
        |
        | Used for both INVENTORY and RESOURCE fulfillment.
        |--------------------------------------------------------------------------
        */

        $unit_cost =
            isset($item['unit_cost'])
            &&
            $item['unit_cost'] !== ''

            ? (float) $item['unit_cost']

            : (float) $reqItem->estimated_unit_cost;


        if ($unit_cost < 0) {

            throw new Exception(
                'Unit cost cannot be negative for "' .
                $reqItem->description .
                '".'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PREPARE COMMON ITEM DATA
        |--------------------------------------------------------------------------
        */

        $preparedItem = [

            'requisition_item_id' =>
                $requisition_item_id,

            'quantity' =>
                $quantity,

            'unit_cost' =>
                $unit_cost,

            'remarks' =>
                trim(
                    $item['remarks'] ?? ''
                )
        ];


        /*
        |--------------------------------------------------------------------------
        | MATERIAL / INVENTORY
        |
        | Only inventory materials require a warehouse location.
        |--------------------------------------------------------------------------
        */

        if (
            $reqItem->resource_source === 'INVENTORY'
        ) {

            $location_id =
                (int) (
                    $item['location_id']
                    ?? 0
                );


            if ($location_id <= 0) {

                throw new Exception(
                    'Please select an inventory location for material item: ' .
                    $reqItem->description
                );
            }


            $preparedItem['location_id'] =
                $location_id;
        }


        /*
        |--------------------------------------------------------------------------
        | NON-MATERIAL RESOURCE
        |
        | No inventory location is required.
        |--------------------------------------------------------------------------
        */

        elseif (
            $reqItem->resource_source === 'RESOURCE'
        ) {

            $preparedItem['location_id'] =
                null;
        }


        /*
        |--------------------------------------------------------------------------
        | ADD PREPARED ITEM
        |--------------------------------------------------------------------------
        */

        $items[] =
            $preparedItem;
    }


    /*
    |--------------------------------------------------------------------------
    | NOTHING TO FULFILL
    |--------------------------------------------------------------------------
    */

    if (empty($items)) {

        $_SESSION['error'] =
            'Please enter a quantity greater than zero.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionFulfillments/create/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE FULFILLMENT NUMBER
    |--------------------------------------------------------------------------
    */

    $fulfillment_no =
        'RR-FUL-' .
        date('YmdHis') .
        '-' .
        random_int(100, 999);


    /*
    |--------------------------------------------------------------------------
    | PREPARE FULFILLMENT DATA
    |--------------------------------------------------------------------------
    */

    $data = [

        'requisition_id' =>
            $requisition_id,

        'fulfillment_no' =>
            $fulfillment_no,

        'fulfillment_date' =>
            $_POST['fulfillment_date']
                ?? date('Y-m-d H:i:s'),

        'fulfilled_by' =>
            (int) $_SESSION['user_id'],

        'remarks' =>
            trim($_POST['remarks'] ?? ''),

        'items' =>
            $items
    ];


    /*
    |--------------------------------------------------------------------------
    | CREATE FULFILLMENT
    |
    | Model handles the entire fulfillment process
    | inside a database transaction.
    |--------------------------------------------------------------------------
    */

    try {

        $fulfillment_id =
            $this->fulfillmentModel
                ->createFulfillment(
                    $data
                );


        if (
            empty($fulfillment_id)
            ||
            (int) $fulfillment_id <= 0
        ) {

            throw new Exception(
                'Fulfillment was created but no fulfillment ID was returned.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $_SESSION['success'] =
            'Resource requisition fulfilled successfully.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionFulfillments/details/' .
            (int) $fulfillment_id
        );

        exit;

    } catch (Throwable $e) {

        $_SESSION['error'] =
            $e->getMessage();


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionFulfillments/create/' .
            $requisition_id
        );

        exit;
    }
}


    /*
    |------------------------------------------------------------------
    | DETAILS
    |------------------------------------------------------------------
    */

    public function details($fulfillment_id)
    {
        AuthHelper::can('projects.view');


        /*
        |--------------------------------------------------------------
        | GET FULFILLMENT HEADER
        |--------------------------------------------------------------
        */

        $fulfillment =
            $this->fulfillmentModel
            ->getFulfillmentById($fulfillment_id);


        if (!$fulfillment) {

            $_SESSION['error'] =
                'Fulfillment record not found.';


            header(

                'Location: ' .

                    URLROOT .

                    '/ResourceRequisitions'

            );

            exit;
        }


        /*
        |--------------------------------------------------------------
        | GET FULFILLMENT ITEMS
        |--------------------------------------------------------------
        */

        $items =
            $this->fulfillmentModel
            ->getFulfillmentItems($fulfillment_id);


        $data = [

            'fulfillment' => $fulfillment,

            'items' => $items

        ];


        $this->view(

            'resource-requisition-fulfillments/details',

            $data

        );
    }

    public function storeResource()
{
    AuthHelper::can('projects.view');

    /*
    |------------------------------------------------
    | ONLY POST
    |-------------------------------------------------
    */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | REQUISITION ID
    |--------------------------------------------------------------------------
    */

    $requisition_id =
        (int) ($_POST['requisition_id'] ?? 0);


    if ($requisition_id <= 0) {

        $_SESSION['error'] =
            'Invalid requisition.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET REQUISITION
    |--------------------------------------------------------------------------
    */

    $requisition =
        $this->fulfillmentModel
            ->getRequisition(
                $requisition_id
            );


    if (!$requisition) {

        $_SESSION['error'] =
            'Resource requisition not found.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions'
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE REQUISITION STATUS
    |--------------------------------------------------------------------------
    */

    if (
        $requisition->status !== 'APPROVED'
        &&
        $requisition->status !== 'PARTIAL'
    ) {

        $_SESSION['error'] =
            'This requisition is not available for fulfillment.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitions/details/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET POSTED ITEMS
    |--------------------------------------------------------------------------
    */

    $postedItems =
        $_POST['items'] ?? [];


    if (empty($postedItems)) {

        $_SESSION['error'] =
            'Please enter at least one resource fulfillment quantity.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionFulfillments/createResource/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET VALID FULFILLABLE RESOURCE ITEMS
    |--------------------------------------------------------------------------
    |
    | We do not trust posted resource_id, quantity,
    | or remaining quantity from the browser.
    |
    */

    $fulfillableItems =
        $this->fulfillmentModel
            ->getFulfillableResourceItems(
                $requisition_id
            );


    /*
    |--------------------------------------------------------------------------
    | INDEX ITEMS BY REQUISITION ITEM ID
    |--------------------------------------------------------------------------
    */

    $validItems = [];


    foreach ($fulfillableItems as $fulfillableItem) {

        $validItems[
            (int) $fulfillableItem->id
        ] = $fulfillableItem;
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARE FULFILLMENT ITEMS
    |--------------------------------------------------------------------------
    */

    $items = [];


    foreach ($postedItems as $requisition_item_id => $postedItem) {

        $requisition_item_id =
            (int) $requisition_item_id;


        /*
        |--------------------------------------------------------------------------
        | QUANTITY
        |--------------------------------------------------------------------------
        */

        $quantity =
            (float) (
                $postedItem['quantity']
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | SKIP ZERO QUANTITY
        |--------------------------------------------------------------------------
        */

        if ($quantity <= 0) {

            continue;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE REQUISITION ITEM
        |--------------------------------------------------------------------------
        */

        if (
            !isset(
                $validItems[
                    $requisition_item_id
                ]
            )
        ) {

            $_SESSION['error'] =
                'Invalid resource fulfillment item.';


            header(
                'Location: ' .
                URLROOT .
                '/ResourceRequisitionFulfillments/createResource/' .
                $requisition_id
            );

            exit;
        }


        $requisitionItem =
            $validItems[
                $requisition_item_id
            ];


        /*
        |--------------------------------------------------------------------------
        | VALIDATE REMAINING QUANTITY
        |--------------------------------------------------------------------------
        */

        $remaining_quantity =
            (float) $requisitionItem->remaining_quantity;


        if ($quantity > $remaining_quantity) {

            $_SESSION['error'] =
                'The fulfillment quantity for "' .
                $requisitionItem->resource_name .
                '" cannot exceed the remaining quantity of ' .
                number_format(
                    $remaining_quantity,
                    2
                ) .
                '.';


            header(
                'Location: ' .
                URLROOT .
                '/ResourceRequisitionFulfillments/createResource/' .
                $requisition_id
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | UNIT COST
        |--------------------------------------------------------------------------
        */

        $unit_cost =
            (float) (
                $postedItem['unit_cost']
                ?? 0
            );


        if ($unit_cost < 0) {

            $_SESSION['error'] =
                'Unit cost cannot be negative.';


            header(
                'Location: ' .
                URLROOT .
                '/ResourceRequisitionFulfillments/createResource/' .
                $requisition_id
            );

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | ADD VALID ITEM
        |--------------------------------------------------------------------------
        */

        $items[] = [

            'requisition_item_id' =>
                $requisition_item_id,

            /*
            |----------------------------------------------------------
            | IMPORTANT
            |
            | Use the database value, not the posted value.
            |----------------------------------------------------------
            */

            'resource_id' =>
                (int) $requisitionItem->resource_id,

            'resource_type' =>
                $requisitionItem->resource_type,

            'description' =>
                $requisitionItem->description,

            'uom' =>
                $requisitionItem->uom,

            'quantity' =>
                $quantity,

            'unit_cost' =>
                $unit_cost,

            'remarks' =>
                trim(
                    $postedItem['remarks']
                    ?? ''
                )

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NOTHING TO FULFILL
    |--------------------------------------------------------------------------
    */

    if (empty($items)) {

        $_SESSION['error'] =
            'Please enter a fulfillment quantity greater than zero for at least one resource item.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionFulfillments/createResource/' .
            $requisition_id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE FULFILLMENT NUMBER
    |--------------------------------------------------------------------------
    */

    $fulfillment_no =
        'RR-FUL-' .
        date('YmdHis') .
        '-' .
        random_int(100, 999);


    /*
    |--------------------------------------------------------------------------
    | PREPARE FULFILLMENT DATA
    |--------------------------------------------------------------------------
    */

    $data = [

        'requisition_id' =>
            $requisition_id,

        'fulfillment_no' =>
            $fulfillment_no,

        'fulfillment_date' =>
            date('Y-m-d H:i:s'),

        'fulfilled_by' =>
            (int) $_SESSION['user_id'],

        'remarks' =>
            trim(
                $_POST['remarks']
                ?? ''
            ),

        'items' =>
            $items

    ];


    /*
    |--------------------------------------------------------------------------
    | CREATE RESOURCE FULFILLMENT
    |--------------------------------------------------------------------------
    */

    try {

        $fulfillment_id =
            $this->fulfillmentModel
                ->createResourceFulfillment(
                    $data
                );


        if (
            empty($fulfillment_id)
            ||
            (int) $fulfillment_id <= 0
        ) {

            throw new Exception(
                'Resource fulfillment was created but no fulfillment ID was returned.'
            );
        }


        $_SESSION['success'] =
            'Resource requisition fulfilled successfully.';


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionFulfillments/details/' .
            (int) $fulfillment_id
        );

        exit;

    } catch (Throwable $e) {

        $_SESSION['error'] =
            $e->getMessage();


        header(
            'Location: ' .
            URLROOT .
            '/ResourceRequisitionFulfillments/createResource/' .
            $requisition_id
        );

        exit;
    }
}
}
