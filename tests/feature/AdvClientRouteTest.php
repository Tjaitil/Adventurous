<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AdvClientRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_index_renders_correctly(): void
    {
        $this->actingAs($this->getRandomUser());

        $response = $this->get('/advclient');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('AdvClient')

        );
    }

    public function test_advclient_route_with_invalid_user_redirects_to_login(): void
    {
        $response = $this->get('/advclient');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
