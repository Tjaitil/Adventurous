<?php

namespace Tests\Feature;

use Tests\TestCase;

class InventoryRoutesTest extends TestCase
{
    public function test_inventory_route(): void
    {
        $response = $this->actingAs($this->getRandomUser())
            ->get('/inventory');

        $response->assertStatus(200);
        $json = $response->json();
        $templates = $json['html'] ?? [];
        $this->assertArrayHasKey('inventory', $templates);
    }

    public function test_get_prices_endpoint(): void
    {
        $response = $this->actingAs($this->getRandomUser())
            ->get('/inventory/prices');

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('prices', $json);
    }
}
