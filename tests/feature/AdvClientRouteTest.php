<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdvClientRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_advclient_route(): void
    {
        $User = User::find(1);
        $response = $this->actingAS($User)->get('/advclient');

        $response->assertStatus(200);
    }

    public function test_advclient_route_with_invalid_user_redirects_to_login(): void
    {
        $response = $this->get('/advclient');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
