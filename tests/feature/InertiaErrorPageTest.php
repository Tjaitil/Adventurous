<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class InertiaErrorPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        // The handler only swaps in the Inertia error page outside local/testing.
        $this->app->detectEnvironment(fn () => 'production');
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

    public function test_aborted_status_is_forwarded_to_the_error_page(): void
    {
        Route::middleware('web')->get('/__test/forbidden', fn () => abort(403));

        $response = $this->get('/__test/forbidden');

        $response->assertStatus(403);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('ErrorPage')
            ->where('status', 403)
        );
    }

    public function test_expired_page_redirects_back_with_a_message(): void
    {
        Route::middleware('web')->get('/__test/expired', fn () => abort(419));

        $response = $this->from('/login')->get('/__test/expired');

        $response->assertRedirect('/login');
        $response->assertSessionHas('message', 'The page expired, please try again.');
    }

    public function test_local_environment_keeps_the_default_error_response(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
        $response->assertDontSee('data-page', false);
    }
}
