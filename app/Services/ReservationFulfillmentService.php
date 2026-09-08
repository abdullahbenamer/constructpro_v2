<?php

require_once '../app/Services/BaseService.php';

class ReservationFulfillmentService extends BaseService
{
    private $reservationModel;
    private $inventoryModel;
    private $projectCostService;


    public function __construct(
        InventoryReservationModel $reservationModel,
        InventoryModel $inventoryModel,
        InventoryLocationStockModel $locationStockModel,
        InventoryMovementModel $movementModel,
        ProjectCostService $projectCostService
    ) {
        $this->reservationModel = $reservationModel;

        $this->inventoryModel = $inventoryModel;

        $this->locationStockModel = $locationStockModel;

        $this->movementModel = $movementModel;

        $this->projectCostService = $projectCostService;
    }

    public function fulfill(int $reservationId): void
    {
        /*
    |--------------------------------------------------------------
    | 1. GET RESERVATION
    |--------------------------------------------------------------
    */

        $reservation =
            $this->reservationModel->getById(
                $reservationId
            );

        if (!$reservation) {

            throw new Exception(
                'Reservation not found.'
            );
        }


        /*
    |--------------------------------------------------------------
    | 2. VALIDATE STATUS
    |--------------------------------------------------------------
    */

        if ($reservation->status !== 'ACTIVE') {

            throw new Exception(
                'Only ACTIVE reservations can be fulfilled.'
            );
        }


        /*
    |--------------------------------------------------------------
    | 3. GET INVENTORY ITEM
    |--------------------------------------------------------------
    */

        $item =
            $this->inventoryModel->getById(
                $reservation->inventory_id
            );

        if (!$item) {

            throw new Exception(
                'Inventory item not found.'
            );
        }


        /*
    |--------------------------------------------------------------
    | 4. VALIDATE PROJECT
    |--------------------------------------------------------------
    */

        if (empty($reservation->project_id)) {

            throw new Exception(
                'Reservation must have a project before fulfillment.'
            );
        }


        /*
    |--------------------------------------------------------------
    | 5. CREATE PROJECT COST
    |--------------------------------------------------------------
    */

        $this->projectCostService->create([

            'project_id' =>
            (int)$reservation->project_id,

            'cost_type' =>
            'materials',

            'description' =>
            'Reservation Fulfillment: '
                . $item->name,

            'quantity' =>
            (float)$reservation->quantity,

            'unit_price' =>
            (float)$item->cost_price,

            'inventory_id' =>
            (int)$reservation->inventory_id,

            'location_id' =>
            (int)$reservation->location_id

        ]);


        /*
    |--------------------------------------------------------------
    | 6. MARK RESERVATION FULFILLED
    |--------------------------------------------------------------
    */

        $this->reservationModel->markFulfilled(
            $reservationId
        );
    }
}
