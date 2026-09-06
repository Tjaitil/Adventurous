<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RouteTest extends TestCase
{
    /**
     * Named routes the application relies on, mapped to the URI they must
     * resolve to. Add a row here whenever a new ->name() route is introduced.
     *
     * @return array<string, array{name: string, uri: string}>
     */
    public static function namedRouteProvider(): array
    {
        return [
            'login' => ['name' => 'login', 'uri' => '/login'],
            'client' => ['name' => 'client', 'uri' => '/advclient'],
        ];
    }

    #[DataProvider('namedRouteProvider')]
    public function test_named_route_resolves_to_expected_uri(string $name, string $uri): void
    {
        $this->assertTrue(Route::has($name), "Route [{$name}] is not registered.");

        $this->assertSame(
            $uri,
            route($name, [], false),
            "Named route [{$name}] did not resolve to its expected URI."
        );
    }

    /**
     * Guard against a named route being added without a matching row above,
     * so this test keeps covering every named route in the application.
     */
    public function test_no_named_routes_are_untested(): void
    {
        /**
         * Names registered by dev/vendor packages (Ignition, Telescope, ...)
         * that aren't part of the application's own routing.
         */
        $vendorPrefixes = ['ignition.', 'telescope', 'horizon', 'sanctum.', 'livewire.'];

        $registered = array_filter(
            array_keys(Route::getRoutes()->getRoutesByName()),
            fn (string $name) => ! Str::startsWith($name, $vendorPrefixes),
        );
        $covered = array_column(self::namedRouteProvider(), 'name');

        $this->assertSame(
            [],
            array_values(array_diff($registered, $covered)),
            'Named routes missing a row in namedRouteProvider().'
        );
    }
}
