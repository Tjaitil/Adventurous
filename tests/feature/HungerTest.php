<?php

namespace App\tests;

use App\Models\HealingItem;
use App\Models\Hunger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HungerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->RandomUser);
    }

    public function test_get_hunger()
    {
        $response = $this->get('/hunger/get');
        $response->assertStatus(200);
        $response->json();
    }

    public function test_get_heal_data()
    {
        $HealingItem = HealingItem::first();

        $response = $this->get('/hunger/item/get?item='.$HealingItem->item);

        $response->assertStatus(200);
        $response->json();
    }

    public function test_hunger_is_not_allowed_when_full()
    {
        $HealingItem = HealingItem::first();

        $this->insertItemToInventory($this->RandomUser, $HealingItem->item, 3);

        $Hunger = Hunger::where('user_id', $this->RandomUser->id)->first();

        $Hunger->current = 100;

        $response = $this->post('/hunger/restore', [
            'item' => $HealingItem->item,
            'amount' => 1,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    public function test_restore_hunger()
    {
        $HealingItem = HealingItem::first();

        $this->insertItemToInventory($this->RandomUser, $HealingItem->item, 3);

        $Hunger = Hunger::where('user_id', $this->RandomUser->id)->first();
        if (! $Hunger instanceof Hunger) {
            $this->fail('Hunger model not found');
        }
        $Hunger->current = 80;
        $Hunger->save();

        $response = $this->post('/hunger/restore', [
            'item' => $HealingItem->item,
            'amount' => 1,
        ]);

        $response->assertStatus(200);
        $response->json();

        $this->assertDatabaseHas('inventory', [
            'user_id' => $this->RandomUser->id,
            'item' => $HealingItem->item,
            'amount' => 2,
        ]);
    }

    public function test_restore_hunger_with_invalid_item()
    {
        $response = $this->post('/hunger/restore', [
            'item' => 'invalid item',
            'amount' => 3,
        ]);

        $response->assertStatus(422);
        $response->json();
    }
}
