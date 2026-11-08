<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkforceLodgeResource;
use App\Http\Responses\AdvResponse;
use App\Models\User;
use App\Services\WorkforceLodgeService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkforceLodgeController extends Controller
{
    public function __construct(
        private readonly WorkforceLodgeService $workforceLodgeService,
    ) {}

    public function getData(#[CurrentUser] User $User): WorkforceLodgeResource
    {
        return $this->workforceLodgeService->getWorkforceLodgeResource($User);
    }

    public function upgradeEfficiency(#[CurrentUser] User $User, Request $request): AdvResponse|JsonResponse
    {
        return $this->workforceLodgeService->upgradeEfficiency($User, $request->input('skill'));
    }
}
