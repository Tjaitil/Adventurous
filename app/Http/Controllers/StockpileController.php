<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockpileCollection;
use App\Http\Responses\AdvResponse;
use App\Models\User;
use App\Services\StockpileService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;

class StockpileController extends Controller
{
    public function __construct(
        private readonly StockpileService $stockpileService,
    ) {}

    public function getData(#[CurrentUser()] User $User): StockpileCollection
    {
        return $this->stockpileService->getStockpileResource($User);
    }

    public function update(#[CurrentUser] User $User, Request $request): AdvResponse|StockpileCollection
    {
        $input = $request->validate([
            'item' => 'required|string|min:1',
            'insert' => 'required|boolean',
            'amount' => 'required_without:all|integer|min:1',
        ]);

        return $this->stockpileService->update($User, $input);
    }
}
