<?php

class GoodsReturns extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST RETURNS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        AuthHelper::can('inventory.view');

        $model = $this->model('GoodsReturn');

        $data['returns'] = $model->getAll();

        $this->view(
            'goods-returns/index',
            $data
        );
    }

    public function details($id)
{
    AuthHelper::can('inventory.view');

    $returnModel =
        $this->model('GoodsReturn');

    $returnItemModel =
        $this->model('GoodsReturnItem');

    $return =
        $returnModel->getById((int)$id);

    if (!$return) {

        header(
            'Location: ' .
            URLROOT .
            '/goodsreturns'
        );

        exit;
    }

    $data['return'] =
        $return;

    $data['items'] =
        $returnItemModel->getByReturn(
            (int)$id
        );

    $this->view(
        'goods-returns/details',
        $data
    );
}

    /*
    |--------------------------------------------------------------------------
    | CREATE RETURN
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        AuthHelper::can('inventory.edit');

        $receiptModel =
            $this->model('GoodsReceipt');

        /*
        |--------------------------------------------------------------------------
        | POST
        |--------------------------------------------------------------------------
        */

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            try {

                $service =
                    $this->service('GoodsReturn');

                $service->returnGoods($_POST);

                FlashHelper::success(
                    'Goods returned to supplier successfully.'
                );

                header(
                    'Location: ' .
                    URLROOT .
                    '/goodsreturns'
                );

                exit;

            } catch (Throwable $e) {

                FlashHelper::error(
                    $e->getMessage()
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GET
        |--------------------------------------------------------------------------
        |
        | Load GRNs.
        |
        */

        $data['goodsReceipts'] =
            $receiptModel->getAll();


        $this->view(
            'goods-returns/create',
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET GRN ITEMS
    |--------------------------------------------------------------------------
    |
    | /goodsreturns/items/{grn_id}
    |
    */

    public function items($grn_id)
    {
        AuthHelper::can('inventory.view');

        $receiptItemModel =
            $this->model('GoodsReceiptItem');

        $returnItemModel =
            $this->model('GoodsReturnItem');


        $items =
            $receiptItemModel->getItems(
                (int)$grn_id
            );


        /*
        |--------------------------------------------------------------------------
        | Add already-returned quantity
        |--------------------------------------------------------------------------
        */

        foreach ($items as $item) {

            $item->returned_quantity =
                $returnItemModel->getReturnedQuantity(
                    $item->id
                );
        }


        header(
            'Content-Type: application/json'
        );

        echo json_encode($items);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | GET AVAILABLE WAREHOUSE LOCATIONS
    |--------------------------------------------------------------------------
    |
    | /goodsreturns/locations/{inventory_id}
    |
    */

    public function locations($inventory_id)
    {
        AuthHelper::can('inventory.view');

        $stockModel =
            $this->model('InventoryLocationStock');

        $locations =
            $stockModel->getAvailableItemLocations(
                (int)$inventory_id
            );


        header(
            'Content-Type: application/json'
        );

        echo json_encode($locations);

        exit;
    }
}