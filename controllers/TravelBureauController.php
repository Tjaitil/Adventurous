<?php
class TravelBureauController extends controller
{
    public $data;
    function __construct(
        private StoreService $storeService,
        private TravelBureau_model $travelBureau_model,
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private MapRequiredDataAction $mapRequiredDataAction,
    ) {
        parent::__construct();
    }

    public function index()
    {
        $this->loadModel('TravelBureau', true);

        $this->data = $this->mapRequiredDataAction->handle($this->travelBureau_model->all());
        $this->render('travelbureau', 'Travel Bureau', $this->data, true, true);
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function get()
    {
        $data = $this->mapRequiredDataAction->handle($this->travelBureau_model->all());

        return Response::addData("cart", $data)->setStatus(200);
    }

    public function buyCart(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        try {
            $item_data = $this->mapRequiredDataAction->handle($this->travelBureau_model->find($item));

            $this->storeService->makeStore(
                ["list" => $item_data]
            );


            if (!$this->storeService->isStoreItem($item)) {
                return $this->storeService->logNotStoreItem($item);
            }

            $item_data = $this->storeService->getStoreItem($item);

            foreach ($item_data->required_items as $key => $value) {
                if (!$this->inventoryService->hasEnoughAmount(
                    $value->name,
                    $value->amount * $amount
                )) {
                    return $this->inventoryService->logNotEnoughAmount($value->name);
                }
            }

            if (!$this->inventoryService->hasEnoughAmount(GameConstants::CURRENCY, $item_data->store_value)) {
                return $this->inventoryService->logNotEnoughAmount(GameConstants::CURRENCY);
            }

            $price = $item_data->store_value;

            if ($this->sessionService->isProfiency(GameConstants::MINER_SKILL_NAME)) {
                $price *= (1 - GameConstants::MINER_STORE_DISCOUNT);
            }

            foreach ($item_data->required_items as $key => $value) {
                $this->inventoryService->edit($value->name, $value->amount * $amount);
            }

            $this->inventoryService
                ->edit(
                    GameConstants::CURRENCY,
                    -$this->storeService->calculateItemCost($item_data->name, $amount)
                );

            $this->travelBureau_model->update($item);

            return Response::setStatus(200);
        } catch (Exception $e) {
            return Response::addMessage($e->getMessage());
        }
    }
}
