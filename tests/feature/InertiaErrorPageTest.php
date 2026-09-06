<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class InertiaErrorPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        config(['app.debug' => false]);
    }

    public function test_unknown_route_renders_inertia_error_page(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('ErrorPage')
            ->where('status', 404)
        );
    }

    public function test_error_page_renders_for_guests(): void
    {
        $response = $this->get('/advclient/does-not-exist');

        $response->assertStatus(404);
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('ErrorPage'));
    }

    public function test_debug_mode_keeps_default_error_response(): void
    {
        config(['app.debug' => true]);

        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
        $this->assertStringNotContainsString('"component":"ErrorPage"', (string) $response->getContent());
    }
}
