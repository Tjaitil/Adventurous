<?php

namespace App\Http\Controllers;

use App\libs\controller;
use App\Models\Diplomacy;
use App\Models\UserData;
use App\Models\UserLevels;
use App\Services\HungerService;
use App\Services\InventoryService;
use App\Services\ProfiencyService;
use App\Services\SessionService;

class AdvclientController extends controller
{
    protected $city;
    protected $cityfile;
    public $data = array();

    function __construct(
        private HungerService $hungerService,
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private ProfiencyService $profiencyService,
        private Diplomacy $diplomacy
    ) {
        parent::__construct();
    }
    public function index()
    {
        $this->generateGameID();

        // TODO: Fix this
        $_SESSION['gamedata']['inventory'] = [];

        $user_data = UserData::where('username', $this->sessionService->getCurrentUsername())->first()->toArray();

        $this->render(
            'advclient',
            "",
            [
                'username' => $this->sessionService->getCurrentUsername(),
                'location' => $user_data['location'],
                'levels' => UserLevels::where('username', $this->sessionService->getCurrentUsername())->first()->toArray(),
                'profiency' => $user_data['profiency'],
                'current_hunger' => $this->hungerService->getCurrentHunger(),
                'inventory' => $this->inventoryService->getInventory()->toArray(),
                'profiency_status' => $this->profiencyService->calculateProfienciesStatuses(),
                'diplomacy_data' => $this->diplomacy->where('username', $this->sessionService->getCurrentUsername())->get()->toArray(),
            ],
            false
        );
    }
    private function generateGameID()
    {
        $str = 0;
        for ($i = 0; $i < 8; $i++) {
            $str .= rand(0, 9);
        }
        $_SESSION['gameid'] = $str;
    }
}
