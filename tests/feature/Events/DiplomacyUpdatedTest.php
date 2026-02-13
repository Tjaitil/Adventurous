<?php

namespace Tests\Feature\Events;

use App\Enums\GameLocations;
use App\Events\DiplomacyUpdated;
use App\Http\Resources\DiplomacyResource;
use App\Models\CityRelation;
use App\Models\Diplomacy;
use App\Services\DiplomacyService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DiplomacyUpdatedTest extends TestCase
{
    public function test_set_new_diplomacy_broadcasts_updated_diplomacy(): void
    {
        Event::fake([DiplomacyUpdated::class]);
        $this->actingAs($this->TestUser);

        $location = GameLocations::HIRTAM_LOCATION->value;

        $CityRelation = CityRelation::where('city', $location)->first();
        if (! $CityRelation instanceof CityRelation) {
            $CityRelation = new CityRelation();
            $CityRelation->city = $location;
        }

        $CityRelation->hirtam = 1;
        $CityRelation->pvitul = 1.1;
        $CityRelation->khanz = 0.9;
        $CityRelation->ter = 1;
        $CityRelation->fansal_plains = 1.1;
        $CityRelation->save();

        $Diplomacy = Diplomacy::where('user_id', $this->TestUser->id)->first();
        if (! $Diplomacy instanceof Diplomacy) {
            $Diplomacy = Diplomacy::factory()->create([
                'user_id' => $this->TestUser->id,
                'username' => $this->TestUser->username,
            ]);
        }

        $Diplomacy->hirtam = 1;
        $Diplomacy->pvitul = 1;
        $Diplomacy->khanz = 1;
        $Diplomacy->ter = 1;
        $Diplomacy->fansal_plains = 1;
        $Diplomacy->save();

        $service = app(DiplomacyService::class);
        $service->setNewDiplomacy($location, 10, $this->TestUser->id);

        $updatedDiplomacy = Diplomacy::where('user_id', $this->TestUser->id)->firstOrFail();
        $expectedPayload = (new DiplomacyResource($updatedDiplomacy))->resolve();

        Event::assertDispatched(DiplomacyUpdated::class, function (DiplomacyUpdated $event) use ($expectedPayload) {
            return $event->userId === $this->TestUser->id
                && $event->Diplomacy === $expectedPayload;
        });
    }
}
