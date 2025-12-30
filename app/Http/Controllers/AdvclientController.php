<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiplomacyResource;
use App\Models\Diplomacy;
use App\Models\Hunger;
use App\Models\Inventory;
use App\Models\UserData;
use App\Models\UserLevels;
use App\Services\ProfiencyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AdvclientController extends Controller
{
    public function __construct(
        private ProfiencyService $profiencyService,
    ) {}

    public function index(): Response|RedirectResponse
    {
        $user_data = UserData::where('username', Auth::user()->username ?? '')->first();
        if (! $user_data instanceof UserData) {
            Log::warning('User data not found for user:', ['user' => Auth::user(), 'session' => session()]);

            return redirect()->route('login');
        }

        return Inertia::render('AdvClient', [
            'gameLog' => session()->get('log') ?? [],
            'username' => Auth::user()->username,
            'location' => $user_data->location,
            'mapLocation' => UserData::where('username', Auth::user()->username)->first()?->map_location,
            'levels' => UserLevels::where('username', Auth::user()->username)->first()
                ?->toArray(),
            'profiency' => $user_data->profiency,
            'hunger' => Hunger::where('user_id', Auth::user()->id)->first(),
            'inventory' => Inventory::where('user_id', Auth::user()->id)->get(),
            'profiencyStatuses' => $this->profiencyService->calculateProfienciesStatuses(Auth::user()->id),
            'diplomacy' => Diplomacy::where('username', Auth::user()->username)->get()->toArray(),
            'initLevels' => UserLevels::where('username', Auth::user()->username)->first()
                ?->toArray(),
            'initMessages' => session()->get('log') ?? [],
            'diplomacyResource' => new DiplomacyResource(
                Diplomacy::where('username', Auth::user()->username)->first()
            ),
        ]);
    }
}
