<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Tests\Support\UserTrait;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use UserTrait;

    protected function setUp(): void
    {
        parent::setUp();
        session()->flush();
    }

    /**
     * @group authentication
     */
    public function test_user_can_view_login_form(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    /**
     * @group authentication
     */
    public function test_user_can_login(): void
    {
        $response = $this->post('/authenticate', [
            'email' => $this->getRandomUser()->email,
            'password' => 'password',
        ]);

        $response->assertSessionHas('_token');
        $response->assertStatus(302);
        $response->assertRedirect('/main');
    }

    /**
     * @group authentication
     */
    public function test_user_can_logout(): void
    {
        $response = $this->actingAs($this->getRandomUser())->post('/logout');

        $response->assertStatus(302);

        $this->assertEquals(Auth::check(), false);

        $response->assertRedirect('/login');
    }

    /**
     * @group authentication
     */
    public function test_user_without_session_is_redirected_to_login(): void
    {
        $response = $this->get('/advclient');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
