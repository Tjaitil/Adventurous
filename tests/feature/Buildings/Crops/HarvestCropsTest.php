<?php

namespace Tests\Feature\Buildings\Crops;

use App\Enums\GameLocations;
use App\Models\Crop;
use App\Models\Farmer;
use App\Models\FarmerWorkforce;
use App\Models\UserLevels;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\SkillTestCase;

class HarvestCropsTest extends SkillTestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->beginDatabaseTransaction();
        $this->actingAs($this->RandomUser);
    }

    /**
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
     * @dataProvider locationProvider
     */
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
            'username' => $this->RandomUser->username,
            'item' => $Crop->crop_type,
        ]);

        $UserLevels = UserLevels::where('user_id', $this->RandomUser->id)->first();
        if (! $UserLevels instanceof UserLevels) {
            $this->fail();
        }

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
