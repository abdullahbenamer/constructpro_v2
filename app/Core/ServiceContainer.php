<?php

require_once '../app/Core/Database.php';

class ServiceContainer
{

    /**
     * Shared Database connection
     */
    private Database $db;

    /**
     * Service instances (Singleton per request)
     */
    private array $instances = [];

    /**
     * Service definitions
     */
    private array $definitions = [

    'PurchaseOrder' => [
    'class' => PurchaseOrderService::class,
    'dependencies' => [
        PurchaseOrderModel::class
    ]
],

        'InventoryTransfer' => [
            'class' => InventoryTransferService::class,
            'dependencies' => [
                InventoryLocationStockModel::class,
                InventoryMovementModel::class,
                InventoryTransferModel::class
            ]
        ],

        'ProjectCost' => [
            'class' => ProjectCostService::class,
            'dependencies' => [
                ProjectCostModel::class,
                InventoryLocationStockModel::class,
                InventoryMovementModel::class,
                ProjectLedgerModel::class,
                InventoryModel::class
            ]
        ],

        'ReservationFulfillment' => [
    'class' => ReservationFulfillmentService::class,

    'dependencies' => [
        InventoryReservationModel::class,
        InventoryModel::class,
        InventoryLocationStockModel::class,
        InventoryMovementModel::class
    ],

    'services' => [
        'ProjectCost'
    ]
],

        'GoodsReceipt' => [
            'class' => GoodsReceiptService::class,

            'dependencies' => [
                PurchaseOrderModel::class,
                GoodsReceiptModel::class,
                GoodsReceiptItemModel::class,
                SupplierLedgerModel::class
            ],

            'services' => [
                'Inventory'
            ]
        ],

        'SupplierPayment' => [
            'class' => SupplierPaymentService::class,
            'dependencies' => [
                SupplierPaymentModel::class,
                SupplierLedgerModel::class,
                SupplierPaymentAllocationModel::class
            ]
        ],

        'AccountsPayable' => [
            'class' => AccountsPayableService::class,
            'dependencies' => [
                SupplierModel::class,
                SupplierLedgerModel::class,
                SupplierPaymentModel::class
            ]
        ],

        'Inventory' => [
            'class' => InventoryService::class,
            'dependencies' => [
                InventoryLocationStockModel::class,
                InventoryMovementModel::class,
                InventoryTransferModel::class
            ]
        ],

     'GoodsReturn' => [
    'class' => GoodsReturnService::class,

    'dependencies' => [
        GoodsReceiptItemModel::class,
        GoodsReturnModel::class,
        GoodsReturnItemModel::class,
        SupplierLedgerModel::class
    ],

    'services' => [
        'Inventory'
    ]
],

    ];

    public function __construct()
    {
        /*
    |---------------------------------------------
    | ONE Database connection for the entire request
    |---------------------------------------------
    */

        $this->db = new Database();
    }

    /**
     * Resolve Service
     */
    public function make(string $service)
    {
        /*
        |--------------------------------------------------------------------------
        | Return existing instance
        |--------------------------------------------------------------------------
        */

        if (isset($this->instances[$service])) {
            return $this->instances[$service];
        }

        /*
        |--------------------------------------------------------------------------
        | Service exists?
        |--------------------------------------------------------------------------
        */

        if (!isset($this->definitions[$service])) {

            throw new Exception("Unknown service '{$service}'.");
        }

        $definition = $this->definitions[$service];

        $dependencies = [];

        /*
        |--------------------------------------------------------------------------
        | Build all dependencies using SAME Database connection
        |--------------------------------------------------------------------------
        */

        foreach ($definition['dependencies'] as $class) {

            // Load model if needed

            if (!class_exists($class)) {

                require_once '../app/Models/' . $class . '.php';
            }

            $dependencies[] = new $class($this->db);
        }

        /*
|--------------------------------------------------------------------------
| Build service dependencies
|--------------------------------------------------------------------------
*/

        foreach ($definition['services'] ?? [] as $serviceName) {

            $dependencies[] = $this->make($serviceName);
        }

        /*
        |--------------------------------------------------------
        | Load service if needed
        |--------------------------------------------------------
        */

        $serviceClass = $definition['class'];

        if (!class_exists($serviceClass)) {

            require_once '../app/Services/' . $serviceClass . '.php';
        }

        $this->instances[$service] =
            new $serviceClass(...$dependencies);

        return $this->instances[$service];
    }
}
