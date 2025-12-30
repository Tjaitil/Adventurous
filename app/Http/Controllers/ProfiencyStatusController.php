<?php

namespace App\Http\Controllers;

use App\libs\Response;
use App\libs\controller;
use App\Services\ProfiencyService;
use App\libs\TemplateFetcher;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class ProfiencyStatusController extends controller
{
    public function __construct(
        private ProfiencyService $profiencyService,
    ) {
        parent::__construct();
    }

    public function getStatuses(#[CurrentUser()] User $User)
    {
        $template = TemplateFetcher::loadTemplate(
            'profiencyStatus',
            $this->profiencyService->calculateProfienciesStatuses($User->id)
        );

        return Response::addTemplate('profiencyStatusTemplate', $template);
    }

    /**
     * Calculate countdowns and data
     *
     * @return void
     */
    private function calculateWarriorData()
    {
        $this->warrior_status['training'] = 0;
        $this->warrior_status['finished'] = 0;
        $this->warrior_status['army_mission'] = 0;
        $this->warrior_status['idle'] = 0;
        foreach ($this->warrior_status['warriors'] as $key) {


            $countdown = date_timestamp_get(new DateTime($key['training_countdown']));
            if ($countdown < $this->date_now && $key['fetch_report'] == 1) {
                $this->warrior_status['finished'] += 1;
            } else if ($countdown > $this->date_now && $key['fetch_report'] == 1) {
                $this->warrior_status['training'] += 1;
            }
            if ($key['army_mission'] !== 0) {
                $this->warrior_status['army_mission'] += 1;
            } else {
                $this->warrior_status['idle'] += 1;
            }
        }

        // Set default if no the user has no active army missions
        if (count($this->warrior_status['army_missions']['current_army_missions']) === 0) {
            $this->warrior_status['army_missions']['current_army_missions'][0]['countdown'] = "No army missions active";
        } else {
            foreach ($this->warrior_status['army_missions']['current_army_missions'] as $key => $value) {
                $army_mission_countdown = date_timestamp_get(new DateTime($value['mission_countdown']));
                $this->warrior_status['army_missions']['current_army_missions'][$key]['countdown'] =
                    $army_mission_countdown;
                $string = "Armymission: %s";
                if (
                    $army_mission_countdown < $this->date_now &&
                    $value['mission_id'] > 0
                ) {
                    $this->warrior_status['army_missions']['current_army_missions']['status'] = sprintf($string, "finished");
                } elseif (
                    $army_mission_countdown > $this->date_now &&
                    $this->warrior_status['army_missions']['current_army_missions']['mission_id'] == 0
                ) {
                    $time_left = $countdown - $this->date_now;
                    $m = round($time_left / 60);
                    $countdown = "done in " . $m . 'm';

                    $this->warrior_status['army_missions']['current_army_missions']['status'] = sprintf($string, $countdown);
                } else {
                    $this->warrior_status['army_missions']['current_army_missions']['status'] = sprintf($string, 'none');
                }
            }
        }
    }

    /**
     * Calculate countdowns and data
     *
     * @return void
     */
    private function calculatefarmer_statusData()
    {
        $i = 0;
        foreach ($this->farmer_status['countdowns'] as $key) {
            $location = $key['location'];
            $this->farmer_status[$location]['crop_type'] = $key['crop_type'];

            if ($key['crop_countdown'] < $this->date_now && $key['plot1_harvest'] == 1) {
                $this->farmer_status[$location]['status'] = "finished";
            } else if ($key['crop_countdown'] > $this->date_now && $key['plot1_harvest'] == 1) {
                $time_left = $key['crop_countdown'] - $this->date_now;
                $m = round($time_left / 60);
                $this->farmer_status[$location]['status'] = "done in " . $m . 'm';
            } else {
                $this->farmer_status[$location]['status'] = "nothing happening";
            }
            $this->farmer_status[$location]['workforce'] = $this->farmer_status['workforce_data'][$location . '_workforce'];
            $i++;
        }
    }

    /**
     * Calculate countdowns and data
     *
     * @return void
     */
    private function calculateMinerData()
    {
        $i = 0;
        foreach ($this->miner_status['countdowns'] as $key) {
            $location = $key['location'];
            $this->miner_status[$location]['mining_type'] = $key['mining_type'];
            if ($key['mining_countdown'] < $this->date_now && $key['fetch_minerals'] == 1) {
                $this->miner_status[$location]['status'] = "Finished";
            } else if ($key['mining_countdown'] > $this->date_now && $key['fetch_minerals'] == 1) {
                $time_left = $key['mining_countdown'] - $this->date_now;
                $m = round($time_left / 60);
                $this->miner_status[$location]['status'] = "done in " . $m . 'm';
            } else {
                $this->miner_status[$location]['status'] = "nothing happening";
            }
            $this->miner_status[$location]['workforce'] = $this->miner_status['workforce_data'][$location . '_workforce'];
            $i++;
        }
    }
}
