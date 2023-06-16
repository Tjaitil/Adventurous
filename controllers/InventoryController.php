<?php

namespace App\controllers;

use App\models\Item;
use App\libs\response;
use App\libs\controller;
use App\models\Inventory;
use App\services\SessionService;
use App\libs\TemplateFetcher;

class InventoryController extends controller
{

    public function __construct(
        private TemplateFetcher $TemplateFetcher,
        private Inventory $inventory,
        private SessionService $sessionService
    ) {
        parent::__construct();
    }

    public function get()
    {
        $data = $this->inventory->all()->where('username', $this->sessionService->getCurrentUsername());
        $inventory_template = $this->TemplateFetcher->loadTemplate('inventory', $data);
        response::addTemplate("inventory", $inventory_template);
    }

    public function getPrices()
    {
        $Inventory_prices = Item::select('name', 'store_value')->join('inventory', 'items.name', '=', 'inventory.item')
            ->where('inventory.username', $this->sessionService->getCurrentUsername())
            ->get();

        $Stockpile_prices = Item::select('name', 'store_value')->join('stockpile', 'items.name', '=', 'stockpile.item')
            ->where('stockpile.username', $this->sessionService->getCurrentUsername())
            ->get();

        $prices = array_merge($Inventory_prices->toArray(), $Stockpile_prices->toArray());


        return Response::setResponse(["prices" => $prices]);
    }
}
