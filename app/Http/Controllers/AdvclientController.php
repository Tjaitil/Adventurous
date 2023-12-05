<?php

namespace App\Http\Controllers;

use App\Models\Diplomacy;
use App\Models\Hunger;
use App\Models\UserData;
use App\Models\UserLevels;
use App\Services\HungerService;
use App\Services\InventoryService;
use App\Services\ProfiencyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdvclientController extends Controller
{
    public function __construct(
        private HungerService $hungerService,
        private InventoryService $inventoryService,
        private ProfiencyService $profiencyService,
    ) {
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user_data = UserData::where('username', Auth::user()->name ?? '')->first();
        if (! $user_data instanceof UserData) {
            Log::warning('User data not found for user:', ['user' => Auth::user(), 'session' => session()]);

            return redirect()->route('login');
        }

        return View('advclient')
            ->with('username', Auth::user()->name)
            ->with('location', $user_data->location)
            ->with('Levels', UserLevels::where('username', Auth::user()->name)->first()->toArray())
            ->with('profiency', $user_data->profiency)
            ->with('Hunger', Hunger::where('user_id', Auth::user()->id)->first())
            ->with('Inventory', $this->inventoryService->getInventory())
            ->with('profiency_status', $this->profiencyService->calculateProfienciesStatuses())
            ->with('Diplomacy', Diplomacy::where('username', Auth::user()->name)->get()->toArray())
            ->with('map_location', UserData::where('username', Auth::user()->name)->first()->map_location);
    }
}
