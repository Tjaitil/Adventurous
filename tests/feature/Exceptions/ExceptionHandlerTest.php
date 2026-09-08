<?php

namespace Tests\Feature\Exceptions;

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia;
use RuntimeException;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_not_found_renders_the_inertia_error_page_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('ErrorPage')
            ->where('status', 404)
        );
    }

    public function test_not_found_keeps_the_default_page_outside_production(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
        $response->assertDontSee('data-page="', false);
    }

    public function test_forbidden_renders_the_inertia_error_page_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        Route::middleware('web')->get('/__test/forbidden', fn () => abort(403));

        $response = $this->get('/__test/forbidden');

        $response->assertStatus(403);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('ErrorPage')
            ->where('status', 403)
        );
    }

    public function test_error_page_renders_for_guests_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->get('/advclient/does-not-exist');

        $response->assertStatus(404);
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('ErrorPage'));
    }

    public function test_server_errors_render_the_inertia_error_page_in_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        Route::middleware('web')->get('/__test/boom', fn () => throw new RuntimeException('boom'));

        $response = $this->get('/__test/boom');

        $response->assertStatus(500);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('ErrorPage')
            ->where('status', 500)
        );
    }

    public function test_server_errors_keep_the_default_page_outside_production(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        Route::middleware('web')->get('/__test/boom', fn () => throw new RuntimeException('boom'));

        $response = $this->get('/__test/boom');

        $response->assertStatus(500);
        // The Inertia root element (`<div id="app" data-page="...">`) must be absent;
        // the framework's own debug page echoes this test's source, so match on the
        // attribute assignment rather than the bare string.
        $response->assertDontSee('data-page="', false);
    }

    public function test_expired_page_redirects_back_with_a_message(): void
    {
        Route::middleware('web')->get('/__test/expired', fn () => abort(419));

        $response = $this->from('/login')->get('/__test/expired');

        $response->assertRedirect('/login');
        $response->assertSessionHas('message', 'The page expired, please try again.');
    }
}
