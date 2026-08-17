<?php

namespace App\Http\Controllers;

use App\Http\Resources\InventoryResourceCollection;
use App\Http\Responses\AdvResponse;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct(
        private Inventory $inventory,
    ) {}

    public function get(Request $request): JsonResponse
    {
        $Inventory = Inventory::with('item')->where('user_id', Auth::user()->id)->get();

        $template = view('inventory')
            ->with('Inventory', $Inventory)
            ->render();

        return (new AdvResponse)->addTemplate('inventory', $template)->toResponse($request);
    }

    public function getItems(): InventoryResourceCollection
    {
        $Inventory = Inventory::with('item')->where('user_id', Auth::user()->id)
            ->get();

        return new InventoryResourceCollection($Inventory);
    }

    public function getPrices(): JsonResponse
    {
        $Inventory_prices = Item::select('name', 'store_value')->join('inventory', 'items.item_id', '=', 'inventory.item_id')
            ->where('inventory.user_id', Auth::user()->id)
            ->get();

        $Stockpile_prices = Item::select('name', 'store_value')
            ->join('stockpile', 'items.item_id', '=', 'stockpile.item_id')
            ->where('stockpile.user_id', Auth::user()->id)
            ->get();

        $prices = array_merge($Inventory_prices->toArray(), $Stockpile_prices->toArray());

        return response()->json(['prices' => $prices]);
    }
}
