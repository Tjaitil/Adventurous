<?php

namespace Tests\Feature\Crops;

use App\Enums\GameLocations;
use App\Models\Crop;
use App\Models\Farmer;
use App\Models\FarmerWorkforce;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\SkillTestCase;

class CropsTest extends SkillTestCase
{
    use DatabaseTransactions;

    public $connectionsToTransact = ['mysql'];

    public FarmerWorkforce $FarmerWorkforce;

    public Farmer $Farmer;

    public function setUp(): void
    {
        parent::setUp();

        $this->beginDatabaseTransaction();
        $this->actingAs($this->RandomUser);

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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     *
     * @param  value-of<GameLocations::TOWHAR_LOCATION|GameLocations::KRASNUR_LOCATION>  $location
     */
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

        $this->assertDatabaseHas('farmer', [
            'user_id' => $this->RandomUser->id,
            'crop_type' => null,
        ]);

        $this->assertDatabaseHas('farmer_workforce', [
            $location => $location,
            'user_id' => $this->RandomUser->id,
            'avail_workforce' => $this->FarmerWorkforce->avail_workforce + 3,
        ]);
    }

    public static function locationProvider()
    {
        return [
            'tomato' => [GameLocations::TOWHAR_LOCATION->value, 'tomato'],
            'watermelon' => [GameLocations::KRASNUR_LOCATION->value, 'watermelon'],
        ];
    }
}
