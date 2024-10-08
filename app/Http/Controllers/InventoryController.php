<?php

namespace App\Http\Controllers;

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
        $Inventory = Inventory::where('username', Auth::user()->username)->get();

        $template = view('inventory')
            ->with('Inventory', $Inventory)
            ->render();

        return (new AdvResponse)->addTemplate('inventory', $template)->toResponse($request);
    }

    public function getItems(): JsonResponse
    {
        $Inventory = Inventory::where('username', Auth::user()->username)
            ->get();

        return response()->json($Inventory);
    }

    public function getPrices(): JsonResponse
    {
        $Inventory_prices = Item::select('name', 'store_value')->join('inventory', 'items.name', '=', 'inventory.item')
            ->where('inventory.username', Auth::user()->username)
            ->get();

        $Stockpile_prices = Item::select('name', 'store_value')->join('stockpile', 'items.name', '=', 'stockpile.item')
            ->where('stockpile.username', Auth::user()->username)
            ->get();

        $prices = array_merge($Inventory_prices->toArray(), $Stockpile_prices->toArray());

        return response()->json(['prices' => $prices]);
    }
}
