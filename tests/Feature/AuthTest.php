<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_shown_to_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('Road to Strong');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_user_can_login_and_sees_dashboard(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertRedirect('/');

        $this->actingAs($user)->get('/')->assertOk()->assertSee('Heute');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertRedirect('/login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
