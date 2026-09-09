<?php

namespace Tests\Feature\Controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Enums\GameEvents;
use App\Enums\GameLocations;
use App\Models\Crop;
use App\Models\Farmer;
use App\Models\FarmerWorkforce;
use App\Models\Item;
use App\Models\UserLevels;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SkillTestCase;

class CropsControllerTest extends SkillTestCase
{
    use RefreshDatabase;

    public $connectionsToTransact = ['testing'];

    public FarmerWorkforce $FarmerWorkforce;

    public Farmer $Farmer;

    protected function setUp(): void
    {
        parent::setUp();        $this->actingAs($this->RandomUser);

        $this->Farmer = Farmer::where('user_id', $this->RandomUser->id)->firstOrFail();
        $this->FarmerWorkforce = FarmerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
    }

    public function test_retrieve_building()
    {
        $response = $this->get('/crops');
        $response->assertStatus(200);

        $response->assertViewIs('crops');
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_get_countdown(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $response = $this->get('/crops/countdown');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'crop_finishes_at',
            'crop_type',
        ]);
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_get_view_data(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $response = $this->get('/crops/data');

        $response->assertStatus(200);
        $response->json();

        $response->assertJsonStructure([
            'crops',
            'workforce',
            'farmer',
        ]);
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_start_growing(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }
        $this->setFarmerLevel($Crop->farmer_level);

        $this->insertItemToInventory($this->RandomUser, $Crop->seed_item, $Crop->seed_required);
        $availWorkforce = $this->FarmerWorkforce->avail_workforce;

        $response = $this->post('/crops/start', [
            'crop_type' => $cropType,
            'workforce_amount' => 1,
        ]);

        $response->assertStatus(200);
        $response->json();

        $this->assertDatabaseHas('farmer', [
            'user_id' => $this->RandomUser->id,
            'crop_type' => $cropType,
        ]);

        $this->assertDatabaseHas('farmer_workforce', [
            $location => 1,
            'user_id' => $this->RandomUser->id,
            'avail_workforce' => $availWorkforce - 1,
        ]);
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_user_cannot_start_growing_if_no_seed(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }

        $this->setFarmerLevel($Crop->farmer_level);

        $response = $this->post('/crops/start', [
            'crop_type' => $cropType,
            'workforce_amount' => 1,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_user_cannot_start_growing_if_no_workforce(string $location, string $cropType)
    {
        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }

        $this->insertItemToInventory($this->RandomUser, $Crop->seed_item, $Crop->seed_required);

        $this->FarmerWorkforce->krasnur += $this->FarmerWorkforce->avail_workforce;
        $this->FarmerWorkforce->avail_workforce = 0;
        $this->FarmerWorkforce->save();

        $response = $this->post('/crops/start', [
            'crop_type' => $cropType,
            'workforce_amount' => 1,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_cannot_start_when_crops_are_already_growing(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);
        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }

        $this->setFarmerLevel($Crop->farmer_level);

        $this->Farmer->crop_type = $cropType;
        $this->Farmer->crop_finishes_at = Carbon::now()->addMinutes(1);
        $this->Farmer->save();

        $response = $this->post('/crops/start', [
            'crop_type' => $cropType,
            'workforce_amount' => 1,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_user_cannot_grow_seed_if_farmer_is_too_level(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }

        if ($Crop->farmer_level === 1) {
            $this->assertTrue(true);
        } else {
            $this->setFarmerLevel($Crop->farmer_level - 1);

            $this->insertItemToInventory($this->RandomUser, $Crop->seed_item, $Crop->seed_required);

            $this->FarmerWorkforce->avail_workforce = 1;
            $this->FarmerWorkforce->save();

            $response = $this->post('/crops/start', [
                'crop_type' => $cropType,
                'workforce_amount' => 1,
            ]);

            $response->assertStatus(422);
            $response->json();
        }
    }

    /**
     *
     * @param  value-of<GameLocations::TOWHAR_LOCATION|GameLocations::KRASNUR_LOCATION>  $location
     */
    #[DataProvider('locationProvider')]
    public function test_harvest(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setFarmerLevel(44);

        $Farmer = Farmer::where('user_id', $this->RandomUser->id)->where('location', $location)->firstOrFail();
        if (! $Farmer instanceof Farmer) {
            $this->fail('Farmer not found');
        }
        $Farmer->crop_type = $cropType;
        $Farmer->crop_finishes_at = Carbon::now()->subMinutes(5);
        $Farmer->save();

        $this->FarmerWorkforce->avail_workforce -= 3;
        $this->FarmerWorkforce->{$location} = 3;
        $this->FarmerWorkforce->save();

        $response = $this->post('/crops/end', [
            'crop_type' => $cropType,
            'workforce_amount' => 1,
            'is_cancelling' => false,
        ]);

        $response->assertStatus(200);
        $response->json();

        $this->assertResponseHasEvent($response, GameEvents::XpGainedEvent->value);

        $this->assertDatabaseHas('farmer', [
            'user_id' => $this->RandomUser->id,
            'crop_type' => null,
        ]);

        $this->assertDatabaseHas('farmer_workforce', [
            $location => 0,
            'user_id' => $this->RandomUser->id,
            'avail_workforce' => $this->FarmerWorkforce->avail_workforce + 3,
        ]);
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_cant_harvest_with_no_active_crops(string $location, string $cropType)
    {
        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }

        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setFarmerLevel(44);

        $Farmer = Farmer::where('user_id', $this->RandomUser->id)->firstOrFail();
        if (! $Farmer instanceof Farmer) {
            $this->fail('Farmer not found');
        }

        $Farmer->crop_type = null;

        $FarmerWorkforce = FarmerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
        if (! $FarmerWorkforce instanceof FarmerWorkforce) {
            $this->fail('FarmerWorkforce not found');
        }

        $FarmerWorkforce->avail_workforce -= 3;
        $FarmerWorkforce->{$location} = 3;

        $response = $this->post('/crops/end', [
            'crop_type' => 'tomato',
            'workforce_amount' => 1,
            'is_cancelling' => false,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_cant_harvest_with_not_finished_crops(string $location, string $cropType)
    {
        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }

        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setFarmerLevel($Crop->farmer_level);

        $Farmer = Farmer::where('user_id', $this->RandomUser->id)->firstOrFail();
        if (! $Farmer instanceof Farmer) {
            $this->fail('Farmer not found');
        }

        $Farmer->crop_type = $cropType;
        $Farmer->crop_finishes_at = now()->addMinutes(15);

        Carbon::setTestNow(now()->addMinutes(10));

        $FarmerWorkforce = FarmerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
        if (! $FarmerWorkforce instanceof FarmerWorkforce) {
            $this->fail('FarmerWorkforce not found');
        }

        $FarmerWorkforce->avail_workforce -= 3;
        $FarmerWorkforce->{$location} = 3;

        $response = $this->post('/crops/end', [
            'crop_type' => 'tomato',
            'workforce_amount' => 1,
            'is_cancelling' => false,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_cant_cancel_finished_crops(string $location, string $cropType)
    {
        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }

        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setFarmerLevel($Crop->farmer_level);

        $Farmer = Farmer::where('user_id', $this->RandomUser->id)->firstOrFail();
        if (! $Farmer instanceof Farmer) {
            $this->fail('Farmer not found');
        }

        $Farmer->crop_type = $cropType;
        $Farmer->crop_finishes_at = now()->addMinutes(5);

        Carbon::setTestNow(now()->addMinutes(10));

        $FarmerWorkforce = FarmerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
        if (! $FarmerWorkforce instanceof FarmerWorkforce) {
            $this->fail('FarmerWorkforce not found');
        }

        $FarmerWorkforce->avail_workforce -= 3;
        $FarmerWorkforce->{$location} = 3;

        $response = $this->post('/crops/end', [
            'crop_type' => 'tomato',
            'workforce_amount' => 1,
            'is_cancelling' => true,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     */
    #[DataProvider('locationProvider')]
    public function test_cancel_harvest_doesnt_give_xp_or_crops(string $location, string $cropType)
    {
        $Crop = Crop::where('crop_type', $cropType)->first();
        if (! $Crop instanceof Crop) {
            $this->fail('Crop type not found');
        }

        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setFarmerLevel($Crop->farmer_level);

        $Farmer = Farmer::where('user_id', $this->RandomUser->id)->firstOrFail();
        if (! $Farmer instanceof Farmer) {
            $this->fail('Farmer not found');
        }

        $Farmer->crop_type = $cropType;
        $Farmer->crop_finishes_at = now()->addMinutes(15);

        Carbon::setTestNow(now()->addMinutes(10));

        $FarmerWorkforce = FarmerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
        if (! $FarmerWorkforce instanceof FarmerWorkforce) {
            $this->fail('FarmerWorkforce not found');
        }

        $FarmerWorkforce->avail_workforce -= 3;
        $FarmerWorkforce->{$location} = 3;

        $response = $this->post('/crops/end', [
            'crop_type' => 'tomato',
            'workforce_amount' => 1,
            'is_cancelling' => true,
        ]);

        $response->assertStatus(422);
        $response->json();

        $this->assertDatabaseMissing('inventory', [
            'user_id' => $this->RandomUser->id,
            'item_id' => Item::where('name', $Crop->crop_type)->value('item_id'),
        ]);

        $UserLevels = UserLevels::where('user_id', $this->RandomUser->id)->first();
        if (! $UserLevels instanceof UserLevels) {
            $this->fail();
        }
        $this->assertResponseNotHasEvent($response, GameEvents::XpGainedEvent->value);

        $this->assertEquals($this->RandomUser->userLevels->farmer_xp, $UserLevels->farmer_xp);
    }

    public static function locationProvider()
    {
        return [
            'tomato' => [GameLocations::TOWHAR_LOCATION->value, 'tomato'],
            'watermelon' => [GameLocations::KRASNUR_LOCATION->value, 'watermelon'],
        ];
    }
}
