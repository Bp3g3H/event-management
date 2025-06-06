<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
        ]);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

         $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
 
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'These credentials do not match our records.',
            'errors' => [
                'email' => [
                    'These credentials do not match our records.'
                ]
            ]
        ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->post('/api/logout');

        $response->assertOk();
        $response->assertJson([
            'message' => 'Logged out successfully'
        ]);
    }
}
