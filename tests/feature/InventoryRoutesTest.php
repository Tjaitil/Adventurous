<?php

namespace Tests\Feature;

use Tests\TestCase;

class InventoryRoutesTest extends TestCase
{
    public function test_get_prices_endpoint(): void
    {
        $response = $this->actingAs($this->getRandomUser())
            ->get('/inventory/prices');

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('prices', $json);
    }

    public function test_can_get_inventory_items(): void
    {
        $response = $this->actingAs($this->getRandomUser())
            ->get('/inventory/items');

        $response->assertStatus(200)
            ->json();

    }
}
