<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        session()->flush();
        $this->withoutVite();
    }

    #[Group('authentication')]
    public function test_user_can_view_login_form(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('LoginPage'));
    }

    #[Group('authentication')]
    public function test_root_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    #[Group('authentication')]
    public function test_user_can_login(): void
    {
        $response = $this->post('/authenticate', [
            'email' => $this->getRandomUser()->email,
            'password' => 'password',
        ]);

        $response->assertSessionHas('_token');
        $response->assertStatus(302);
        $response->assertRedirect(route('client'));
    }

    #[Group('authentication')]
    public function test_login_redirect_target_resolves(): void
    {
        $response = $this->post('/authenticate', [
            'email' => $this->getRandomUser()->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/advclient');

        $this->followRedirects($response)
            ->assertStatus(200);
    }

    #[Group('authentication')]
    public function test_user_can_logout(): void
    {
        $response = $this->actingAs($this->getRandomUser())->post('/logout');

        $response->assertStatus(302);

        $this->assertEquals(Auth::check(), false);

        $response->assertRedirect('/login');
    }

    #[Group('authentication')]
    public function test_user_without_session_is_redirected_to_login(): void
    {
        $response = $this->get('/advclient');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    #[Group('authentication')]
    public function test_logout_redirects_to_login(): void
    {
        $response = $this->actingAs($this->getRandomUser())->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
